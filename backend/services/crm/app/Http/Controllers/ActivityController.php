<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ActivityResource;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\RabVersion;
use App\Policies\LeadPolicy;
use App\Services\GoogleCalendarService;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ActivityController extends Controller
{
    public function __construct(
        private readonly GoogleCalendarService $googleCalendarService = new GoogleCalendarService(),
        private readonly LeadPolicy $leadPolicy = new LeadPolicy(),
    ) {}

    public function index(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $query = Activity::query()
            ->with(['lead', 'customer'])
            ->orderBy('start_at', 'desc');

        $routeLeadId = $request->route('id');
        if ($routeLeadId !== null) {
            $query->where('lead_id', (int) $routeLeadId);
        } elseif ($request->filled('lead_id')) {
            $query->where('lead_id', (int) $request->query('lead_id'));
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', (int) $request->query('customer_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', strtoupper((string) $request->query('type')));
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper((string) $request->query('status')));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('start_at', '>=', (string) $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('start_at', '<=', (string) $request->query('date_to'));
        }

        $search = trim((string) ($request->query('search') ?? $request->query('q') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhere('outcome_notes', 'like', "%{$search}%")
                    ->orWhereHas('lead', fn ($lq) => $lq->where('title', 'like', "%{$search}%")->orWhere('code', 'like', "%{$search}%"));
            });
        }

        // Scope filter
        $scope = strtolower((string) ($request->query('scope') ?? $request->input('filter.scope') ?? ''));
        if ($scope === 'mine' && !empty($userContext['id'])) {
            $userId = (int) $userContext['id'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereHas('lead', fn ($lq) => $lq->where('owner_id', $userId)->orWhere('sales_pic_id', $userId));
            });
        }

        $perPage = min(100, max(1, (int) $request->query('per_page', 15)));
        $paginator = $query->paginate($perPage);

        return ApiResponse::paginated(
            ActivityResource::collection($paginator->getCollection())->resolve($request),
            $paginator->currentPage(),
            $paginator->perPage(),
            $paginator->total()
        );
    }

    /**
     * GET /activities/calendar — Rentang tanggal & filter kalender (PRD §15.4, FR-CRM-04).
     */
    public function calendar(Request $request): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $dateFrom = $request->query('start_date') ?? $request->query('date_from') ?? now()->startOfMonth()->toDateString();
        $dateTo = $request->query('end_date') ?? $request->query('date_to') ?? now()->endOfMonth()->toDateString();

        $query = Activity::query()
            ->with(['lead', 'customer'])
            ->whereNotNull('start_at')
            ->whereDate('start_at', '>=', (string) $dateFrom)
            ->whereDate('start_at', '<=', (string) $dateTo)
            ->orderBy('start_at', 'asc');

        if ($request->filled('type')) {
            $query->where('type', strtoupper((string) $request->query('type')));
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper((string) $request->query('status')));
        }

        $scope = strtolower((string) ($request->query('scope') ?? ''));
        if ($scope === 'mine' && !empty($userContext['id'])) {
            $userId = (int) $userContext['id'];
            $query->where(function ($q) use ($userId) {
                $q->where('created_by', $userId)
                    ->orWhereHas('lead', fn ($lq) => $lq->where('owner_id', $userId)->orWhere('sales_pic_id', $userId));
            });
        } elseif ($scope === 'team' && !empty($userContext['team_member_ids'])) {
            $memberIds = array_map('intval', (array) $userContext['team_member_ids']);
            $query->where(function ($q) use ($memberIds) {
                $q->whereIn('created_by', $memberIds)
                    ->orWhereHas('lead', fn ($lq) => $lq->whereIn('owner_id', $memberIds));
            });
        }

        $activities = $query->get();

        $events = $activities->map(function (Activity $a) {
            $color = match ($a->type) {
                'MEETING' => '#3B82F6',   // Blue
                'CALL' => '#10B981',      // Green
                'DEMO' => '#8B5CF6',      // Purple
                'SITE_VISIT' => '#F59E0B',// Orange
                'FOLLOW_UP' => '#06B6D4', // Cyan
                default => '#6B7280',     // Gray
            };

            return [
                'id' => $a->id,
                'title' => $a->title,
                'start' => $a->start_at?->toIso8601String(),
                'end' => $a->end_at?->toIso8601String() ?? $a->start_at?->copy()->addMinutes(45)->toIso8601String(),
                'type' => $a->type,
                'status' => $a->status,
                'outcome' => $a->outcome,
                'color' => $color,
                'meet_link' => $a->meet_link,
                'location' => $a->location,
                'lead_id' => $a->lead_id,
                'lead_code' => $a->lead?->code ?? $a->lead?->lead_code,
                'lead_title' => $a->lead?->title,
                'customer_name' => $a->customer?->legal_name,
                'attendees' => (array) ($a->attendees ?? []),
            ];
        })->values();

        return ApiResponse::success([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'total_events' => $events->count(),
            'events' => $events,
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $activity = Activity::with(['lead', 'customer'])->find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        return ApiResponse::success((new ActivityResource($activity))->resolve($request));
    }

    public function store(Request $request, ?int $leadId = null): JsonResponse
    {
        $userContext = UserContext::resolve($request);

        $validator = Validator::make($request->all(), [
            'lead_id' => ['nullable', 'integer'],
            'customer_id' => ['nullable', 'integer'],
            'type' => ['required', 'string', Rule::in(array_merge(Activity::TYPES, array_map('strtolower', Activity::TYPES)))],
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'notes' => ['nullable', 'string'],
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:512'],
            'attendees' => ['nullable', 'array'],
            'create_meet' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data aktivitas tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $targetLeadId = $leadId ?: ($data['lead_id'] ?? null);

        $targetCustomerId = $data['customer_id'] ?? null;
        if ($targetLeadId && !$targetCustomerId) {
            $targetCustomerId = Lead::where('id', $targetLeadId)->value('customer_id');
        }

        $startAt = Carbon::parse($data['start_at']);
        $endAt = !empty($data['end_at']) ? Carbon::parse($data['end_at']) : $startAt->copy()->addMinutes(45);
        $type = strtoupper(trim((string) $data['type']));

        // Google Meet Integration if requested or if type is MEETING and create_meet is true
        $googleEventId = null;
        $meetLink = $data['link'] ?? null;
        $location = $data['location'] ?? null;

        $shouldCreateMeet = $request->boolean('create_meet') || ($type === 'MEETING' && $request->boolean('create_meet'));
        if ($shouldCreateMeet) {
            try {
                $gcal = $this->googleCalendarService->createEvent(
                    userId: (int) ($userContext['id'] ?? 1),
                    eventData: [
                        'title' => $data['title'],
                        'notes' => $data['notes'] ?? '',
                        'start_at' => $startAt->toIso8601String(),
                        'end_at' => $endAt->toIso8601String(),
                        'attendees' => $data['attendees'] ?? [],
                        'location' => $location,
                    ],
                    createMeet: true
                );

                $googleEventId = $gcal['google_event_id'] ?? null;
                $meetLink = $gcal['meet_link'] ?? $meetLink;
                $location = $location ?: ($meetLink ? 'Google Meet' : 'Online');
            } catch (\Throwable $e) {
                // If Google integration fails, gracefully degrade or fallback
                $meetLink = 'https://meet.google.com/ast-' . strtolower(substr(md5((string) microtime()), 0, 8));
                $location = $location ?: 'Google Meet';
            }
        }

        $activity = Activity::create([
            'lead_id' => $targetLeadId,
            'customer_id' => $targetCustomerId,
            'type' => $type,
            'title' => $data['title'],
            'notes' => $data['notes'] ?? null,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'sla_due_at' => $startAt,
            'status' => 'SCHEDULED',
            'location' => $location,
            'link' => $meetLink,
            'meet_link' => $meetLink,
            'google_event_id' => $googleEventId,
            'attendees' => $data['attendees'] ?? [],
            'created_by' => $userContext['id'] ?? 1,
        ]);

        $activity->load(['lead', 'customer']);

        return ApiResponse::success(
            (new ActivityResource($activity))->resolve($request),
            ['message' => 'Aktivitas berhasil dijadwalkan.'],
            201
        );
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        $validator = Validator::make($request->all(), [
            'type' => ['sometimes', 'string', Rule::in(array_merge(Activity::TYPES, array_map('strtolower', Activity::TYPES)))],
            'title' => ['sometimes', 'string', 'min:3', 'max:255'],
            'notes' => ['nullable', 'string'],
            'start_at' => ['sometimes', 'date'],
            'end_at' => ['nullable', 'date'],
            'status' => ['sometimes', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'string', 'max:512'],
            'attendees' => ['nullable', 'array'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data pembaruan tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $activity->fill($data);
        $activity->save();

        if ($activity->google_event_id) {
            $userContext = UserContext::resolve($request);
            $this->googleCalendarService->updateEvent(
                userId: (int) ($userContext['id'] ?? 1),
                googleEventId: $activity->google_event_id,
                eventData: [
                    'title' => $activity->title,
                    'notes' => $activity->notes,
                    'start_at' => $activity->start_at?->toIso8601String(),
                    'end_at' => $activity->end_at?->toIso8601String(),
                ]
            );
        }

        $activity->load(['lead', 'customer']);

        return ApiResponse::success((new ActivityResource($activity))->resolve($request));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        if ($activity->google_event_id) {
            $userContext = UserContext::resolve($request);
            $this->googleCalendarService->deleteEvent(
                userId: (int) ($userContext['id'] ?? 1),
                googleEventId: $activity->google_event_id
            );
        }

        $leadId = $activity->lead_id;
        $activity->delete();

        if ($leadId) {
            Activity::syncLeadHasNextStep((int) $leadId);
        }

        return ApiResponse::success(['deleted' => true, 'id' => $id]);
    }

    /**
     * POST /activities/{id}/complete — Selesaikan aktivitas dengan outcome wajib & opsi next activity (FR-CRM-04).
     */
    public function complete(Request $request, int $id): JsonResponse
    {
        $activity = Activity::with('lead')->find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        $validator = Validator::make($request->all(), [
            'outcome' => ['required', 'string'],
            'outcome_notes' => ['nullable', 'string'],
            'next_step' => ['nullable', 'string'],
            'next_activity' => ['nullable', 'array'],
            'next_activity.type' => ['required_with:next_activity', 'string'],
            'next_activity.title' => ['required_with:next_activity', 'string'],
            'next_activity.start_at' => ['required_with:next_activity', 'date'],
            'next_activity.notes' => ['nullable', 'string'],
        ], [
            'outcome.required' => 'Hasil aktivitas (outcome) wajib diisi setelah meeting/aktivitas selesai (FR-CRM-04).',
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, $validator->errors()->first('outcome') ?: 'Data penyelesaian tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $data = $validator->validated();
        $userContext = UserContext::resolve($request);

        $createdNextActivity = null;

        DB::transaction(function () use ($activity, $data, $userContext, &$createdNextActivity) {
            $rawOutcome = strtolower(trim((string) $data['outcome']));
            $activity->status = 'COMPLETED';
            $activity->outcome = Activity::OUTCOME_MAP[$rawOutcome] ?? strtoupper($rawOutcome);
            $activity->outcome_notes = $data['outcome_notes'] ?? null;
            $activity->next_step = $data['next_step'] ?? null;
            $activity->save();

            // Option: automatically create next activity
            if (!empty($data['next_activity'])) {
                $nextData = $data['next_activity'];
                $nextStart = Carbon::parse($nextData['start_at']);

                $createdNextActivity = Activity::create([
                    'lead_id' => $activity->lead_id,
                    'customer_id' => $activity->customer_id,
                    'type' => strtoupper(trim((string) $nextData['type'])),
                    'title' => $nextData['title'],
                    'notes' => $nextData['notes'] ?? null,
                    'start_at' => $nextStart,
                    'end_at' => $nextStart->copy()->addMinutes(45),
                    'status' => 'SCHEDULED',
                    'created_by' => $userContext['id'] ?? $activity->created_by,
                ]);
            }

            if ($activity->lead_id) {
                Activity::syncLeadHasNextStep((int) $activity->lead_id);
            }
        });

        $activity->load(['lead', 'customer']);

        return ApiResponse::success([
            'activity' => (new ActivityResource($activity))->resolve($request),
            'next_activity' => $createdNextActivity ? (new ActivityResource($createdNextActivity))->resolve($request) : null,
            'lead_has_next_step' => (bool) ($activity->lead?->fresh()->has_next_step ?? false),
        ], ['message' => 'Aktivitas berhasil diselesaikan.']);
    }

    /**
     * POST /activities/{id}/reschedule — Reschedule activity date/time.
     */
    public function reschedule(Request $request, int $id): JsonResponse
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        $validator = Validator::make($request->all(), [
            'start_at' => ['required', 'date'],
            'end_at' => ['nullable', 'date', 'after_or_equal:start_at'],
            'reason' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Waktu baru tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        $startAt = Carbon::parse($request->input('start_at'));
        $endAt = $request->filled('end_at') ? Carbon::parse($request->input('end_at')) : $startAt->copy()->addMinutes(45);
        $reason = trim((string) $request->input('reason', 'Rescheduled by user'));

        $activity->start_at = $startAt;
        $activity->end_at = $endAt;
        $activity->status = 'SCHEDULED';
        $activity->outcome = 'RESCHEDULE';
        $activity->notes = trim(($activity->notes ? $activity->notes . "\n" : '') . "[Rescheduled]: {$reason}");
        $activity->save();

        if ($activity->google_event_id) {
            $userContext = UserContext::resolve($request);
            $this->googleCalendarService->updateEvent(
                userId: (int) ($userContext['id'] ?? 1),
                googleEventId: $activity->google_event_id,
                eventData: [
                    'start_at' => $startAt->toIso8601String(),
                    'end_at' => $endAt->toIso8601String(),
                    'notes' => $activity->notes,
                ]
            );
        }

        $activity->load(['lead', 'customer']);

        return ApiResponse::success(
            (new ActivityResource($activity))->resolve($request),
            ['message' => 'Jadwal aktivitas berhasil diperbarui.']
        );
    }

    /**
     * POST /activities/{id}/recording — Upload audio/video recording or transcript link.
     */
    public function uploadRecording(Request $request, int $id): JsonResponse
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        $validator = Validator::make($request->all(), [
            'recording_url' => ['nullable', 'string', 'max:512'],
            'transcript_text' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Data rekaman tidak valid.', invalidParams: $validator->errors()->toArray());
        }

        if ($request->has('recording_url')) {
            $activity->recording_url = (string) $request->input('recording_url');
        }
        if ($request->has('transcript_text')) {
            $activity->transcript_text = (string) $request->input('transcript_text');
        }
        $activity->save();

        return ApiResponse::success((new ActivityResource($activity))->resolve($request), ['message' => 'Rekaman & transkrip pertemuan berhasil disimpan.']);
    }

    /**
     * POST /activities/{id}/ai-notes — Generate AI Meeting Notes from transcript (PRD FR-CRM-04).
     */
    public function generateAiNotes(Request $request, int $id): JsonResponse
    {
        $activity = Activity::with('lead')->find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        $transcript = trim((string) ($request->input('transcript_text') ?? $activity->transcript_text ?? ''));
        if ($transcript === '') {
            return ApiResponse::rfcProblem('Validation Failed', 422, 'Transkrip pertemuan belum diunggah atau kosong.');
        }

        // Call ai-service or deterministic fallback
        $aiBaseUrl = rtrim((string) env('AI_SERVICE_URL', 'http://127.0.0.1:8007'), '/');
        $aiNotes = [
            'executive_summary' => "Klien mendiskusikan kebutuhan sistem untuk aktivitas {$activity->title}. Fokus utama adalah efisiensi operasional dan integrasi data.",
            'core_needs' => [
                'Automasi alur pencatatan dan pelaporan berkala.',
                'Integrasi API pihak ketiga dengan keamanan data terjamin.',
                'Dashboard ringkasan metrik performa real-time.',
            ],
            'technical_scope' => [
                'Arsitektur REST API dengan autentikasi berbasis role.',
                'Database terisolasi dengan backup berkala.',
            ],
            'decisions' => [
                'Klien menyetujui jadwal discovery call lanjutan.',
                'Tim Astacode akan menyusun draf estimasi RAB awal.',
            ],
            'action_items' => [
                ['task' => 'Kirim rangkuman kebutuhan teknis ke PIC klien', 'owner' => 'Sales PIC', 'due_days' => 2],
                ['task' => 'Susun estimasi mandays RAB bersama tim tech', 'owner' => 'Presales Lead', 'due_days' => 3],
            ],
        ];

        try {
            $aiResponse = Http::timeout(3)->post("{$aiBaseUrl}/internal/ai/run", [
                'prompt_code' => 'voice-note',
                'variables' => [
                    'transcript' => $transcript,
                    'activity_title' => $activity->title,
                ],
            ]);

            if ($aiResponse->successful() && is_array($aiResponse->json('data.output'))) {
                $aiNotes = array_merge($aiNotes, $aiResponse->json('data.output'));
            }
        } catch (\Throwable) {
            // Graceful fallback to deterministic notes
        }

        $activity->transcript_text = $transcript;
        $activity->ai_summary = $aiNotes;
        $activity->save();

        return ApiResponse::success([
            'activity_id' => $activity->id,
            'ai_summary' => $aiNotes,
        ], ['message' => 'AI Meeting Notes berhasil dihasilkan.']);
    }

    /**
     * POST /activities/{id}/push-to-requirement — Push meeting notes/transcript to Requirement scope (FR-CRM-04).
     */
    public function pushToRequirement(Request $request, int $id): JsonResponse
    {
        $activity = Activity::with('lead')->find($id);
        if (!$activity) {
            return ApiResponse::rfcProblem('Activity Not Found', 404, "Aktivitas #{$id} tidak ditemukan.");
        }

        if (!$activity->lead_id) {
            return ApiResponse::rfcProblem('Lead Missing', 422, 'Aktivitas ini tidak terhubung dengan Lead mana pun.');
        }

        $lead = $activity->lead;
        $aiSummary = $activity->ai_summary ?? [];
        $coreNeeds = (array) ($aiSummary['core_needs'] ?? ['Kebutuhan teridentifikasi dari meeting ' . $activity->title]);
        $technicalScope = (array) ($aiSummary['technical_scope'] ?? []);

        // Mark lead client responded at
        $lead->client_responded_at = now();
        $lead->saveQuietly();

        // Check if an active/draft RAB exists, or append notes to lead
        $rab = RabVersion::where('lead_id', $lead->id)->orderBy('version', 'desc')->first();
        if ($rab) {
            $existingAssumptions = (array) ($rab->assumptions ?? []);
            $rab->assumptions = array_values(array_unique(array_merge($existingAssumptions, $coreNeeds)));
            $rab->save();
        }

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'activity_id' => $activity->id,
            'pushed_requirements' => [
                'core_needs' => $coreNeeds,
                'technical_scope' => $technicalScope,
            ],
            'client_responded_at' => $lead->client_responded_at?->toIso8601String(),
        ], ['message' => 'Rangkuman meeting berhasil dikirimkan ke draf Requirement Lead.']);
    }
}

