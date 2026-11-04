<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStageHistory;
use App\Services\CustomerDuplicateDetector;
use App\Services\LeadAutoAssignService;
use App\Services\LeadScoringService;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Outbox\HasOutbox;
use Astacode\Kernel\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicLeadCaptureController extends Controller
{
    use HasOutbox;

    public function __construct(
        private readonly LeadAutoAssignService $autoAssignService = new LeadAutoAssignService(),
        private readonly LeadScoringService $scoringService = new LeadScoringService(),
        private readonly CustomerDuplicateDetector $duplicateDetector = new CustomerDuplicateDetector()
    ) {}

    /**
     * Public lead capture endpoint for website inquiry forms, landing pages, and webhooks.
     */
    public function capture(Request $request): JsonResponse
    {
        // 1. Honeypot anti-spam check (silent rejection for bot submissions)
        if ($request->filled('_hp_bio') || $request->filled('website_url_hp') || $request->filled('honeypot')) {
            return ApiResponse::success([
                'status' => 'received',
                'notice' => 'Form submission accepted.',
            ]);
        }

        // 2. Validate input
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['nullable', 'string', 'max:32'],
            'phone_wa' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'project_title' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:64'],
            'est_value' => ['nullable', 'numeric', 'min:0'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:64'],
            'sub_source' => ['nullable', 'string', 'max:128'],
            'utm_source' => ['nullable', 'string', 'max:128'],
            'utm_medium' => ['nullable', 'string', 'max:128'],
            'utm_campaign' => ['nullable', 'string', 'max:128'],
            'utm_term' => ['nullable', 'string', 'max:128'],
            'utm_content' => ['nullable', 'string', 'max:128'],
            'captcha_token' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Form input tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $data = $validator->validated();

        $rawPhone = trim((string) ($data['phone_wa'] ?? $data['phone'] ?? ''));
        $rawEmail = trim((string) ($data['email'] ?? ''));

        if ($rawPhone === '' && $rawEmail === '') {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Mohon cantumkan nomor WhatsApp atau alamat email untuk dihubungi.',
                invalidParams: ['phone' => ['Nomor WhatsApp atau Email wajib diisi.']]
            );
        }

        $phoneE164 = $rawPhone !== '' ? PhoneNormalizer::toE164($rawPhone) : null;
        $companyName = trim((string) ($data['company_name'] ?? 'Perorangan'));
        $title = trim((string) ($data['project_title'] ?? $data['title'] ?? 'Kebutuhan Pengembangan Solusi Digital'));
        $estValue = (float) ($data['est_value'] ?? $data['budget'] ?? 50000000);
        $sourceCode = strtoupper((string) ($data['source'] ?? $data['utm_source'] ?? 'WEBSITE'));

        // Parse UTM from query or body
        $utm = [
            'source' => $data['utm_source'] ?? $request->query('utm_source'),
            'medium' => $data['utm_medium'] ?? $request->query('utm_medium'),
            'campaign' => $data['utm_campaign'] ?? $request->query('utm_campaign'),
            'term' => $data['utm_term'] ?? $request->query('utm_term'),
            'content' => $data['utm_content'] ?? $request->query('utm_content'),
        ];
        $utm = array_filter($utm);

        $result = DB::transaction(function () use ($data, $phoneE164, $rawEmail, $companyName, $title, $estValue, $sourceCode, $utm) {
            // 1. Resolve Lead Source
            $source = LeadSource::firstOrCreate(
                ['code' => $sourceCode ?: 'WEBSITE'],
                ['name' => $data['source'] ?? 'Website Inbound Form', 'category' => 'ORGANIC_WEB', 'channel_group' => 'ORGANIC_WEB']
            );

            // 2. Duplicate Check / Existing Customer Lookup
            $dup = $this->duplicateDetector->check([
                'legal_name' => $companyName,
                'phone_wa' => $phoneE164,
                'email' => $rawEmail ?: null,
            ]);

            if ($dup['has_duplicates']) {
                $customerId = $dup['matches'][0]['customer_id'];
                $customer = Customer::find($customerId);
                $customer->latest_source_id = $source->id;
                $customer->save();
            } else {
                $customer = Customer::create([
                    'legal_name' => $companyName,
                    'type' => strtolower($companyName) === 'perorangan' ? 'perorangan' : 'perusahaan',
                    'phone' => $phoneE164,
                    'email' => $rawEmail ?: null,
                    'status' => 'prospect',
                    'first_source_id' => $source->id,
                    'latest_source_id' => $source->id,
                    'owner_id' => 10,
                    'team_id' => 1,
                    'company_id' => 1,
                ]);
            }

            // 3. Contact
            $contact = Contact::where('customer_id', $customer->id)
                ->where(function ($q) use ($phoneE164, $rawEmail) {
                    if ($phoneE164) {
                        $q->where('phone_wa', $phoneE164)->orWhere('phone', $phoneE164);
                    }
                    if ($rawEmail) {
                        $q->orWhere('email', $rawEmail);
                    }
                })->first();

            if (!$contact) {
                $hasPrimary = Contact::where('customer_id', $customer->id)->where('is_primary', true)->exists();
                $contact = Contact::create([
                    'customer_id' => $customer->id,
                    'name' => $data['name'],
                    'role' => 'decision_maker',
                    'phone_wa' => $phoneE164,
                    'phone' => $phoneE164,
                    'email' => $rawEmail ?: null,
                    'is_primary' => !$hasPrimary,
                    'consent_comm' => true,
                    'consent_at' => now(),
                    'preferred_channel' => $phoneE164 ? 'whatsapp' : 'email',
                ]);
            }

            // 4. Attribution
            CustomerAttribution::create([
                'customer_id' => $customer->id,
                'source_id' => $source->id,
                'touch_type' => $customer->wasRecentlyCreated ? 'first_touch' : 'latest_touch',
                'sub_source' => $data['sub_source'] ?? 'Public Form Capture',
                'utm_source' => $utm['source'] ?? null,
                'utm_medium' => $utm['medium'] ?? null,
                'utm_campaign' => $utm['campaign'] ?? null,
                'utm_term' => $utm['term'] ?? null,
                'utm_content' => $utm['content'] ?? null,
                'touched_at' => now(),
            ]);

            // 5. Auto Assign
            $assigned = $this->autoAssignService->assign([
                'est_value' => $estValue,
                'project_type' => $data['project_type'] ?? 'web_mobile_app',
            ]);

            // 6. Create Lead
            $lead = new Lead([
                'customer_id' => $customer->id,
                'contact_id' => $contact->id,
                'title' => $title,
                'project_type' => $data['project_type'] ?? 'web_mobile_app',
                'priority' => 'medium',
                'stage' => 'NEW_LEAD',
                'stage_id' => 'new_lead',
                'source' => $source->code,
                'source_id' => $source->id,
                'sub_source' => $data['sub_source'] ?? 'Public Form Capture',
                'utm' => $utm ?: null,
                'est_value' => $estValue,
                'sales_pic_id' => $assigned['sales_pic_id'],
                'owner_id' => $assigned['owner_id'],
                'team_id' => $assigned['team_id'],
                'sales_pic_name' => $assigned['sales_pic_name'],
                'tags' => ['inbound_web', 'public_capture'],
                'consent' => true,
                'consent_at' => now(),
                'days_in_stage' => 1,
                'is_stale' => false,
                'has_next_step' => true,
            ]);

            $scoring = $this->scoringService->calculate($lead, $customer);
            $lead->score = $scoring['score'];
            $lead->score_label = $scoring['label'];
            $lead->score_breakdown = $scoring['breakdown'];
            $lead->save();

            // 7. Activity "Follow up pertama" (SLA ≤ 30 menit)
            Activity::create([
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'type' => 'FOLLOW_UP',
                'title' => 'Follow up pertama prospek form web (SLA ≤ 30 menit)',
                'notes' => 'Tindak lanjuti inquiry form website: ' . ($data['notes'] ?? 'Tidak ada catatan.'),
                'start_at' => now(),
                'end_at' => now()->addMinutes(30),
                'sla_due_at' => now()->addMinutes(30),
                'status' => 'SCHEDULED',
                'created_by' => $assigned['owner_id'],
            ]);

            // 8. Stage History
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'from_stage' => 'NONE',
                'to_stage' => 'new_lead',
                'changed_by' => null,
                'reason' => 'Lead masuk dari form publik / landing page (lead.captured).',
                'changed_at' => now(),
            ]);

            // 9. Emit Event `lead.captured` & `lead.created`
            $this->recordEvent(
                eventName: 'lead.captured',
                payload: [
                    'lead_id' => $lead->id,
                    'code' => $lead->code,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->legal_name,
                    'contact_name' => $contact->name,
                    'phone_wa' => $phoneE164,
                    'email' => $rawEmail,
                    'source' => $source->code,
                    'utm' => $utm,
                    'captured_at' => now()->toIso8601String(),
                ],
                aggregateType: 'Lead',
                aggregateId: (string) $lead->id
            );

            return [
                'lead' => $lead,
                'customer' => $customer,
                'contact' => $contact,
            ];
        });

        return ApiResponse::success([
            'message' => 'Terima kasih, inquiry Anda telah kami terima dan tim Astacode akan menghubungi Anda dalam waktu maksimal 30 menit.',
            'lead_code' => $result['lead']->code,
            'status' => 'NEW_LEAD',
        ], [], 201);
    }
}

