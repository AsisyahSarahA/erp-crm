<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\CrmImmutableAuditLog;
use App\Models\Customer;
use App\Models\CustomerAttribution;
use App\Models\Lead;
use App\Models\LeadChecklistProgress;
use App\Models\LeadSource;
use App\Models\LeadStageHistory;
use App\Models\PipelineStageModel;
use App\Models\StageChecklist;
use App\Policies\LeadPolicy;
use App\Services\CustomerDuplicateDetector;
use App\Services\LeadAutoAssignService;
use App\Services\LeadScoringService;
use App\Services\NextBestActionService;
use App\Services\PipelineGuardException;
use App\Services\PipelineTransitionGuard;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Support\PhoneNormalizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadPolicy $policy = new LeadPolicy(),
        private readonly LeadAutoAssignService $autoAssignService = new LeadAutoAssignService(),
        private readonly LeadScoringService $scoringService = new LeadScoringService(),
        private readonly CustomerDuplicateDetector $duplicateDetector = new CustomerDuplicateDetector(),
        private readonly PipelineTransitionGuard $transitionGuard = new PipelineTransitionGuard(),
        private readonly NextBestActionService $nextBestActionService = new NextBestActionService(),
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $query = Lead::query()
            ->with(['customer', 'contact', 'nextActivity'])
            ->accessibleBy($userContext);

        // Filters
        $filters = (array) $request->query('filter', []);

        $stageFilter = $filters['stage'] ?? $request->query('stage');
        if (!empty($stageFilter)) {
            $stages = array_map('strtolower', explode(',', (string) $stageFilter));
            $query->where(function ($q) use ($stages) {
                $q->whereIn('stage_id', $stages)
                    ->orWhereIn('stage', array_map('strtoupper', $stages));
            });
        }

        $sourceFilter = $filters['source'] ?? $request->query('source');
        if (!empty($sourceFilter)) {
            $query->where(function ($q) use ($sourceFilter) {
                $q->where('source', (string) $sourceFilter)
                    ->orWhere('source_id', (int) $sourceFilter);
            });
        }

        $priorityFilter = $filters['priority'] ?? $request->query('priority');
        if (!empty($priorityFilter)) {
            $query->where('priority', strtolower((string) $priorityFilter));
        }

        $ownerFilter = $filters['owner_id'] ?? $request->query('owner_id') ?? $request->query('sales_pic_id');
        if ($ownerFilter !== null && $ownerFilter !== '') {
            $query->where(function ($q) use ($ownerFilter) {
                $q->where('owner_id', (int) $ownerFilter)
                    ->orWhere('sales_pic_id', (int) $ownerFilter);
            });
        }

        $industryFilter = $filters['industry'] ?? $request->query('industry');
        if (!empty($industryFilter)) {
            $query->where(function ($q) use ($industryFilter) {
                $q->where('industry', (string) $industryFilter)
                    ->orWhereHas('customer', fn ($cq) => $cq->where('industry', (string) $industryFilter));
            });
        }

        if ($request->boolean('stale_only') || ($filters['stale_only'] ?? false)) {
            $query->where('is_stale', true);
        }

        if ($request->filled('tag')) {
            $tag = (string) $request->query('tag');
            $query->whereJsonContains('tags', $tag);
        }

        if ($request->filled('min_value')) {
            $query->where('est_value', '>=', (float) $request->query('min_value'));
        }
        if ($request->filled('max_value')) {
            $query->where('est_value', '<=', (float) $request->query('max_value'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', (string) $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', (string) $request->query('date_to'));
        }

        // Search
        $search = trim((string) ($request->query('search') ?? $request->query('q') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('lead_code', 'like', "%{$search}%")
                    ->orWhere('sales_pic_name', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('legal_name', 'like', "%{$search}%")
                            ->orWhere('brand_name', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('contact', function ($ctq) use ($search) {
                        $ctq->where('name', 'like', "%{$search}%")
                            ->orWhere('phone_wa', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortParam = (string) $request->query('sort', '-created_at');
        $allowedSorts = ['id', 'code', 'title', 'est_value', 'weighted_value', 'win_probability', 'score', 'days_in_stage', 'created_at', 'updated_at'];
        foreach (explode(',', $sortParam) as $sortCol) {
            $sortCol = trim($sortCol);
            if ($sortCol === '') {
                continue;
            }
            $dir = str_starts_with($sortCol, '-') ? 'desc' : 'asc';
            $colName = ltrim($sortCol, '-+');
            if (in_array($colName, $allowedSorts, true)) {
                $query->orderBy($colName, $dir);
            }
        }

        $perPage = min(100, max(1, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage);

        return ApiResponse::paginated(
            LeadResource::collection($paginator->getCollection())->resolve($request),
            $paginator->currentPage(),
            $paginator->perPage(),
            $paginator->total()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:64'],
            'priority' => ['nullable', 'string', 'in:hot,high,medium,low,HOT,HIGH,MEDIUM,LOW'],
            'est_value' => ['required', 'numeric', 'min:0'],
            'source' => ['nullable', 'string', 'max:64'],
            'source_id' => ['nullable', 'integer'],
            'sub_source' => ['nullable', 'string', 'max:128'],
            'utm' => ['nullable', 'array'],
            'budget_range' => ['nullable', 'string', 'max:128'],
            'target_closing' => ['nullable', 'date'],
            'target_golive' => ['nullable', 'date'],
            'industry' => ['nullable', 'string', 'max:64'],
            'services' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
            'consent' => ['nullable', 'boolean'],

            // Customer details
            'customer_mode' => ['nullable', 'string', 'in:new,existing,NEW,EXISTING'],
            'customer_id' => ['nullable', 'integer'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['nullable', 'string', 'max:255'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'customer_type' => ['nullable', 'string', 'in:perusahaan,perorangan,instansi,startup,umkm,PERUSAHAAN,PERORANGAN,INSTANSI,STARTUP,UMKM'],
            'npwp' => ['nullable', 'string', function (string $attribute, mixed $value, \Closure $fail) {
                $digits = preg_replace('/[^\d]/', '', (string) $value) ?? '';
                if ($digits !== '' && !in_array(strlen($digits), [15, 16], true)) {
                    $fail('NPWP harus terdiri dari 15 atau 16 digit angka.');
                }
            }],

            // Primary Contact
            'pic_name' => ['nullable', 'string', 'min:2', 'max:255'],
            'contact_name' => ['nullable', 'string', 'min:2', 'max:255'],
            'contact_role' => ['nullable', 'string', 'max:64'],
            'contact_title' => ['nullable', 'string', 'max:128'],
            'phone' => ['nullable', 'string', 'max:32'],
            'phone_wa' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],

            'owner_id' => ['nullable', 'integer'],
            'sales_pic_id' => ['nullable', 'integer'],
            'sales_pic_name' => ['nullable', 'string', 'max:255'],
            'duplicate_resolution' => ['nullable', 'string', 'in:CHECK,LINK_EXISTING,CREATE_NEW,MERGE'],
            'force_create' => ['nullable', 'boolean'],
        ], [
            'pic_name.min' => 'Nama Kontak (PIC) wajib diisi minimal 2 karakter.',
            'contact_name.min' => 'Nama Kontak (PIC) wajib diisi minimal 2 karakter.',
        ]);

        if ($validator->fails()) {
            $picError = $validator->errors()->first('pic_name') ?: $validator->errors()->first('contact_name');
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: $picError ?: 'Data input lead tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $data = $validator->validated();

        // FR-CRM-01: PIC Name is required min 2 chars
        $picName = trim((string) ($data['pic_name'] ?? $data['contact_name'] ?? ''));
        if (mb_strlen($picName) < 2) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Nama Kontak (PIC) wajib diisi minimal 2 karakter.',
                invalidParams: ['pic_name' => ['Nama Kontak (PIC) wajib diisi minimal 2 karakter.']]
            );
        }

        // FR-CRM-01: WhatsApp required for manual forms (normalizes to E.164);
        // Marketing inbound may provide email only until Follow Up
        $sourceStr = strtoupper((string) ($data['source'] ?? ''));
        $isMarketingChannel = in_array($sourceStr, ['MARKETING', 'MARKETING_AUTOMATION', 'TIKTOK', 'INSTAGRAM', 'META_ADS'], true);
        $rawPhone = trim((string) ($data['phone_wa'] ?? $data['phone'] ?? ''));
        $rawEmail = trim((string) ($data['email'] ?? ''));

        if ($rawPhone === '' && (!$isMarketingChannel || $rawEmail === '')) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Nomor WhatsApp wajib diisi dalam format Indonesia (+628...).',
                invalidParams: ['phone_wa' => ['Nomor WhatsApp wajib diisi.']]
            );
        }

        $phoneE164 = $rawPhone !== '' ? PhoneNormalizer::toE164($rawPhone) : null;
        $companyName = trim((string) ($data['legal_name'] ?? $data['company_name'] ?? 'Perorangan'));

        // Duplicate Check (AC-CRM-02)
        $resolution = strtoupper((string) ($data['duplicate_resolution'] ?? ($request->boolean('force_create') ? 'CREATE_NEW' : 'CHECK')));
        $checkPayload = [
            'legal_name' => $companyName,
            'phone_wa' => $phoneE164,
            'email' => $rawEmail ?: null,
            'npwp' => $data['npwp'] ?? null,
        ];
        $dupCheck = $this->duplicateDetector->check($checkPayload);

        $targetCustomerId = !empty($data['customer_id']) ? (int) $data['customer_id'] : null;

        if ($targetCustomerId === null && $dupCheck['has_duplicates'] && $resolution !== 'CREATE_NEW') {
            if ($resolution === 'LINK_EXISTING') {
                $targetCustomerId = (int) $dupCheck['matches'][0]['customer_id'];
            } else {
                return ApiResponse::rfcProblem(
                    title: 'Duplicate Customer Detected',
                    status: 409,
                    detail: 'Customer sudah ada: Tautkan / Buat baru / Gabung',
                    type: 'https://astacode.com/errors/duplicate-customer',
                    extra: [
                        'duplicate_check' => $dupCheck,
                    ]
                );
            }
        }

        $lead = DB::transaction(function () use ($data, $userContext, $picName, $phoneE164, $rawEmail, $companyName, $targetCustomerId, $sourceStr) {
            // 1. Resolve Lead Source
            $sourceStrClean = $sourceStr ?: 'WEBSITE';
            $sourceId = $data['source_id'] ?? null;
            if (!$sourceId && !empty($data['source'])) {
                $sourceId = LeadSource::where('code', strtoupper((string) $data['source']))->value('id');
            }
            if (!$sourceId) {
                $sourceId = LeadSource::firstOrCreate(
                    ['code' => $sourceStrClean],
                    ['name' => $data['source'] ?? 'Website Inbound Form', 'category' => 'INBOUND', 'channel_group' => 'INBOUND']
                )->id;
            }

            // 2. Resolve Customer (Existing vs New)
            if ($targetCustomerId !== null) {
                $customer = Customer::findOrFail($targetCustomerId);
                $customer->latest_source_id = $sourceId;
                $customer->save();
            } else {
                $customer = Customer::create([
                    'legal_name' => $companyName,
                    'brand_name' => $data['brand_name'] ?? null,
                    'type' => $data['customer_type'] ?? 'perusahaan',
                    'industry' => $data['industry'] ?? null,
                    'npwp' => $data['npwp'] ?? null,
                    'phone' => $phoneE164,
                    'email' => $rawEmail ?: null,
                    'status' => 'prospect',
                    'first_source_id' => $sourceId,
                    'latest_source_id' => $sourceId,
                    'owner_id' => $data['owner_id'] ?? $userContext['id'] ?? 1,
                    'team_id' => $data['team_id'] ?? $userContext['team_id'] ?? 1,
                    'company_id' => $userContext['company_id'] ?? 1,
                    'created_by' => isset($userContext['id']) ? (string) $userContext['id'] : '1',
                ]);
            }

            // 3. Resolve Contact
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
                    'name' => $picName,
                    'role' => $data['contact_role'] ?? 'decision_maker',
                    'title' => $data['contact_title'] ?? null,
                    'phone_wa' => $phoneE164,
                    'phone' => $phoneE164,
                    'email' => $rawEmail ?: null,
                    'is_primary' => !$hasPrimary,
                    'consent_comm' => $data['consent'] ?? true,
                    'preferred_channel' => $phoneE164 ? 'whatsapp' : 'email',
                ]);
            }

            // 4. Record Touch Attribution
            $utm = (array) ($data['utm'] ?? []);
            CustomerAttribution::create([
                'customer_id' => $customer->id,
                'source_id' => $sourceId,
                'touch_type' => $customer->wasRecentlyCreated ? 'first_touch' : 'latest_touch',
                'sub_source' => $data['sub_source'] ?? null,
                'campaign_name' => $utm['campaign'] ?? null,
                'utm_source' => $utm['source'] ?? null,
                'utm_medium' => $utm['medium'] ?? null,
                'utm_campaign' => $utm['campaign'] ?? null,
                'utm_term' => $utm['term'] ?? null,
                'utm_content' => $utm['content'] ?? null,
                'touched_at' => now(),
            ]);

            // 5. Auto-assign Sales PIC
            $assigned = $this->autoAssignService->assign($data);

            // 6. Create Lead (stage: new_lead)
            $lead = new Lead([
                'customer_id' => $customer->id,
                'contact_id' => $contact->id,
                'title' => $data['title'],
                'project_type' => $data['project_type'] ?? 'web_mobile_app',
                'priority' => $data['priority'] ?? 'medium',
                'stage' => 'NEW_LEAD',
                'stage_id' => 'new_lead',
                'source' => $data['source'] ?? 'WEBSITE',
                'source_id' => $sourceId,
                'sub_source' => $data['sub_source'] ?? null,
                'utm' => $utm ?: null,
                'est_value' => $data['est_value'],
                'budget_range' => $data['budget_range'] ?? null,
                'target_closing' => $data['target_closing'] ?? null,
                'target_golive' => $data['target_golive'] ?? null,
                'sales_pic_id' => $assigned['sales_pic_id'],
                'owner_id' => $assigned['owner_id'],
                'team_id' => $assigned['team_id'],
                'sales_pic_name' => $assigned['sales_pic_name'],
                'industry' => $data['industry'] ?? $customer->industry,
                'services' => $data['services'] ?? null,
                'tags' => $data['tags'] ?? [],
                'consent' => $data['consent'] ?? true,
                'consent_at' => now(),
                'days_in_stage' => 1,
                'is_stale' => false,
                'has_next_step' => true,
                'created_by' => isset($userContext['id']) ? (string) $userContext['id'] : '1',
            ]);

            // 7. Calculate initial Lead Score
            $scoring = $this->scoringService->calculate($lead, $customer);
            $lead->score = $scoring['score'];
            $lead->score_label = $scoring['label'];
            $lead->score_breakdown = $scoring['breakdown'];
            $lead->save();

            // 8. Create Activity "Follow up pertama" (SLA <= 30 menit jam kerja)
            $slaMinutes = 30;
            Activity::create([
                'lead_id' => $lead->id,
                'customer_id' => $customer->id,
                'type' => 'FOLLOW_UP',
                'title' => 'Follow up pertama prospek baru (SLA ≤ 30 menit)',
                'notes' => 'Hubungi PIC via WhatsApp/Email untuk kualifikasi kebutuhan awal & jadwal discovery call.',
                'start_at' => now(),
                'end_at' => now()->addMinutes($slaMinutes),
                'sla_due_at' => now()->addMinutes($slaMinutes),
                'status' => 'SCHEDULED',
                'created_by' => $assigned['owner_id'],
            ]);

            // 9. Record Lead Stage History
            LeadStageHistory::create([
                'lead_id' => $lead->id,
                'from_stage' => 'NONE',
                'to_stage' => 'new_lead',
                'changed_by' => $userContext['id'] ?? null,
                'reason' => 'Lead baru dibuat dan otomatis masuk stage New Lead (AC-CRM-01).',
                'changed_at' => now(),
            ]);

            // 10. Emit Outbox Event `lead.created`
            $lead->recordEvent(
                eventName: 'lead.created',
                payload: [
                    'lead_id' => $lead->id,
                    'code' => $lead->code,
                    'title' => $lead->title,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->legal_name,
                    'contact_id' => $contact->id,
                    'contact_name' => $contact->name,
                    'est_value' => (float) $lead->est_value,
                    'owner_id' => $lead->owner_id,
                    'sales_pic_name' => $lead->sales_pic_name,
                    'source' => $lead->source,
                    'created_at' => $lead->created_at?->toIso8601String(),
                ],
                aggregateType: 'Lead',
                aggregateId: (string) $lead->id
            );

            return $lead;
        });

        $lead->load(['customer', 'contact', 'nextActivity', 'checklistProgress']);

        return ApiResponse::success(
            (new LeadResource($lead))->resolve($request),
            ['message' => 'Lead baru berhasil dibuat (AC-CRM-01).'],
            201
        );
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::with(['customer', 'contact', 'nextActivity', 'stageHistories', 'watchers', 'checklistProgress'])->find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ke data lead ini ditolak.');
        }

        return ApiResponse::success((new LeadResource($lead))->resolve($request));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::with(['customer', 'contact'])->find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->update($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin mengubah data lead ini.');
        }

        $validator = Validator::make($request->all(), [
            'title' => ['sometimes', 'string', 'min:3', 'max:255'],
            'project_type' => ['sometimes', 'string', 'max:64'],
            'priority' => ['sometimes', 'string', 'in:hot,high,medium,low,HOT,HIGH,MEDIUM,LOW'],
            'est_value' => ['sometimes', 'numeric', 'min:0'],
            'win_probability' => ['nullable', 'integer', 'min:0', 'max:100'],
            'budget_range' => ['nullable', 'string', 'max:128'],
            'target_closing' => ['nullable', 'date'],
            'target_golive' => ['nullable', 'date'],
            'owner_id' => ['nullable', 'integer'],
            'sales_pic_id' => ['nullable', 'integer'],
            'sales_pic_name' => ['nullable', 'string', 'max:255'],
            'industry' => ['nullable', 'string', 'max:64'],
            'services' => ['nullable', 'array'],
            'tags' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Data perubahan lead tidak valid.',
                invalidParams: $validator->errors()->toArray()
            );
        }

        $lead->fill($validator->validated());

        // Recalculate score
        $scoring = $this->scoringService->calculate($lead, $lead->customer);
        $lead->score = $scoring['score'];
        $lead->score_label = $scoring['label'];
        $lead->score_breakdown = $scoring['breakdown'];
        $lead->save();

        $lead->load(['customer', 'contact', 'nextActivity', 'checklistProgress']);

        return ApiResponse::success((new LeadResource($lead))->resolve($request));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->delete($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menghapus lead.');
        }

        $lead->delete();

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }

    /**
     * PATCH /leads/{id}/stage — Centralized Pipeline Stage Transition endpoint (CRM-BE-03).
     * Returns HTTP 422 with `guard_failure` and `deep_link_tab` when blocked by PipelineTransitionGuard.
     */
    public function transitionStage(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->update($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin mengubah stage lead ini.');
        }

        $targetStage = (string) ($request->input('stage') ?? $request->input('to_stage') ?? $request->input('stage_id') ?? '');
        if ($targetStage === '') {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Target stage wajib diisi.',
                invalidParams: ['stage' => ['Target stage wajib diisi.']]
            );
        }

        try {
            $updatedLead = $this->transitionGuard->transition(
                lead: $lead,
                targetStage: $targetStage,
                userContext: $userContext,
                options: [
                    'reason' => $request->input('reason'),
                    'override' => $request->boolean('override'),
                    'reject_reason_code' => $request->input('reject_reason_code'),
                    'reject_reason_id' => $request->input('reject_reason_id'),
                    'reject_notes' => $request->input('reject_notes'),
                    'trigger_source' => $request->input('trigger_source', 'api'),
                ],
            );

            $updatedLead->load(['customer', 'contact', 'nextActivity', 'stageHistories', 'checklistProgress']);

            return ApiResponse::success(
                (new LeadResource($updatedLead))->resolve($request),
                ['message' => "Stage lead berhasil dipindahkan ke {$updatedLead->stage}."]
            );
        } catch (PipelineGuardException $e) {
            return response()->json(
                $e->toProblemDetails('/' . ltrim($request->path(), '/')),
                $e->httpStatus
            );
        }
    }

    /**
     * Reopen a Lost / Rejected lead (FR-CRM-10).
     * Permitted only for Sales Manager and Super Admin; routed through PipelineTransitionGuard.
     */
    public function reopen(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->reopen($userContext, $lead)) {
            return ApiResponse::rfcProblem(
                title: 'Forbidden',
                status: 403,
                detail: 'Hanya Sales Manager atau Direktur yang berwenang membuka kembali lead yang ditolak (FR-CRM-10).'
            );
        }

        $reason = trim((string) $request->input('reason', ''));
        if (mb_strlen($reason) < 5) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Alasan pembukaan kembali lead (reopen) wajib diisi minimal 5 karakter.'
            );
        }

        try {
            $updatedLead = $this->transitionGuard->transition(
                lead: $lead,
                targetStage: 'follow_up',
                userContext: $userContext,
                options: [
                    'reason' => "Reopen oleh {$userContext['name']}: {$reason}",
                    'trigger_source' => 'reopen',
                ],
            );

            $updatedLead->recordEvent(
                eventName: 'lead.reopened',
                payload: [
                    'lead_id' => $updatedLead->id,
                    'code' => $updatedLead->code,
                    'reopened_by' => $updatedLead->reopened_by,
                    'reason' => $reason,
                    'to_stage' => 'follow_up',
                ],
                aggregateType: 'Lead',
                aggregateId: (string) $updatedLead->id
            );

            $updatedLead->load(['customer', 'contact', 'nextActivity', 'stageHistories', 'checklistProgress']);

            return ApiResponse::success(
                (new LeadResource($updatedLead))->resolve($request),
                ['message' => 'Lead berhasil dibuka kembali ke tahap Follow Up.']
            );
        } catch (PipelineGuardException $e) {
            return response()->json(
                $e->toProblemDetails('/' . ltrim($request->path(), '/')),
                $e->httpStatus
            );
        }
    }

    /**
     * GET /leads/{id}/checklists — Mandatory stage activity checklists & lead progress.
     */
    public function checklists(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::with('checklistProgress')->find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $stageFilter = $request->query('stage_id');
        $query = StageChecklist::query()->where('is_active', true)->orderBy('sort_order', 'asc');
        if (!empty($stageFilter)) {
            $query->where('stage_id', Lead::normalizeStageId((string) $stageFilter));
        }

        $items = $query->get();
        $progressMap = $lead->checklistProgress->keyBy('checklist_id');

        $result = $items->map(function (StageChecklist $item) use ($progressMap) {
            $prog = $progressMap->get($item->id);
            return [
                'id' => $item->id,
                'stage_id' => $item->stage_id,
                'code' => $item->code,
                'label' => $item->label,
                'description' => $item->description,
                'is_mandatory' => (bool) $item->is_mandatory,
                'is_completed' => (bool) ($prog?->is_completed ?? false),
                'completed_by' => $prog?->completed_by,
                'completed_at' => $prog?->completed_at?->toIso8601String(),
                'notes' => $prog?->notes,
            ];
        })->values();

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'current_stage' => $lead->stage_id,
            'checklists' => $result,
        ]);
    }

    /**
     * POST|PATCH /leads/{id}/checklists/{checklistId}/toggle — Toggle checklist completion.
     */
    public function toggleChecklist(Request $request, int $id, int $checklistId): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->update($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $checklist = StageChecklist::find($checklistId);
        if (!$checklist) {
            return ApiResponse::rfcProblem('Checklist Not Found', 404, "Checklist #{$checklistId} tidak ditemukan.");
        }

        $progress = LeadChecklistProgress::firstOrNew([
            'lead_id' => $lead->id,
            'checklist_id' => $checklist->id,
        ]);

        $targetState = $request->has('is_completed')
            ? $request->boolean('is_completed')
            : !((bool) $progress->is_completed);

        $progress->is_completed = $targetState;
        $progress->completed_by = $targetState ? (string) ($userContext['id'] ?? '1') : null;
        $progress->completed_at = $targetState ? now() : null;
        if ($request->has('notes')) {
            $progress->notes = $request->input('notes');
        }
        $progress->save();

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'checklist_id' => $checklist->id,
            'code' => $checklist->code,
            'is_completed' => (bool) $progress->is_completed,
            'completed_by' => $progress->completed_by,
            'completed_at' => $progress->completed_at?->toIso8601String(),
        ]);
    }

    /**
     * GET|POST /leads/{id}/next-actions — Next Best Action (rule + AI service).
     */
    public function nextActions(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::with(['activeRab', 'activeTopScheme', 'latestReadinessCheck', 'checklistProgress'])->find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $includeAi = $request->boolean('include_ai', true);
        $result = $this->nextBestActionService->generate($lead, $includeAi);

        return ApiResponse::success($result);
    }

    /**
     * GET /leads/{id}/audit-logs — Immutable Audit Log for stage, value, discount, and TOP changes.
     */
    public function auditLogs(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->policy->view($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $logs = CrmImmutableAuditLog::query()
            ->where('lead_id', $lead->id)
            ->orderByDesc('id')
            ->get();

        return ApiResponse::success($logs->toArray());
    }

    /**
     * GET /pipeline-stages — Retrieve stage probabilities & SLA config.
     */
    public function pipelineStages(): JsonResponse
    {
        $stages = PipelineStageModel::query()->orderBy('sequence', 'asc')->get();

        return ApiResponse::success($stages->toArray());
    }

    /**
     * PUT|PATCH /pipeline-stages/{code} — Admin-configurable stage probability & SLA.
     */
    public function updatePipelineStage(Request $request, string $code): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        if (!$this->policy->hasUnrestrictedAccess($userContext)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Hanya Admin yang dapat mengubah konfigurasi probabilitas stage.');
        }

        $validator = Validator::make($request->all(), [
            'win_probability_pct' => ['required', 'integer', 'min:0', 'max:100'],
            'sla_hours' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Konfigurasi stage tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $stage = PipelineStageModel::query()->where('code', strtoupper($code))->first();
        if (!$stage) {
            return ApiResponse::rfcProblem('Stage Not Found', 404, "Stage '{$code}' tidak ditemukan.");
        }

        $stage->win_probability_pct = (int) $request->input('win_probability_pct');
        if ($request->has('sla_hours')) {
            $stage->sla_hours = (int) $request->input('sla_hours');
        }
        $stage->save();

        return ApiResponse::success($stage->toArray());
    }

    public function bulkAssign(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        if (!$this->policy->hasUnrestrictedAccess($userContext)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Hanya manajer yang berwenang mengalihkan PIC lead secara massal.');
        }

        $validator = Validator::make($request->all(), [
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['integer'],
            'owner_id' => ['required', 'integer'],
            'sales_pic_name' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data penugasan massal tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $ids = $validator->validated()['lead_ids'];
        $ownerId = $validator->validated()['owner_id'];
        $picName = $validator->validated()['sales_pic_name'] ?? "Sales Rep #{$ownerId}";

        $affected = Lead::whereIn('id', $ids)->update([
            'owner_id' => $ownerId,
            'sales_pic_id' => $ownerId,
            'sales_pic_name' => $picName,
            'updated_at' => now(),
        ]);

        return ApiResponse::success([
            'updated_count' => $affected,
            'assigned_to' => ['owner_id' => $ownerId, 'sales_pic_name' => $picName],
        ]);
    }

    public function bulkTags(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'lead_ids' => ['required', 'array', 'min:1'],
            'lead_ids.*' => ['integer'],
            'tags' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data tag massal tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $ids = $validator->validated()['lead_ids'];
        $tags = $validator->validated()['tags'];

        $affected = Lead::whereIn('id', $ids)->update([
            'tags' => json_encode($tags),
            'updated_at' => now(),
        ]);

        return ApiResponse::success(['updated_count' => $affected, 'tags' => $tags]);
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        if (!$this->policy->hasUnrestrictedAccess($userContext)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $ids = (array) $request->input('lead_ids', []);
        $deleted = Lead::whereIn('id', $ids)->delete();

        return ApiResponse::success(['deleted_count' => $deleted]);
    }

    public function export(Request $request): Response|JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $leads = Lead::with(['customer', 'contact'])
            ->accessibleBy($userContext)
            ->orderBy('id')
            ->get();

        if ($request->query('format') === 'json') {
            return ApiResponse::success(LeadResource::collection($leads)->resolve($request));
        }

        $headers = ['code', 'title', 'customer_name', 'contact_name', 'stage', 'priority', 'source', 'est_value', 'sales_pic_name', 'score', 'days_in_stage', 'created_at'];
        $lines = [implode(',', $headers)];

        foreach ($leads as $l) {
            $row = [
                $l->code ?? $l->lead_code,
                '"' . str_replace('"', '""', (string) $l->title) . '"',
                '"' . str_replace('"', '""', (string) ($l->customer?->legal_name ?? '')) . '"',
                '"' . str_replace('"', '""', (string) ($l->contact?->name ?? '')) . '"',
                $l->stage_id,
                $l->priority,
                $l->source,
                $l->est_value,
                '"' . str_replace('"', '""', (string) $l->sales_pic_name) . '"',
                $l->score,
                $l->days_in_stage,
                $l->created_at?->toDateString(),
            ];
            $lines[] = implode(',', $row);
        }

        $csv = "\xEF\xBB\xBF" . implode("\n", $lines);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="leads_export.csv"',
        ]);
    }
}
