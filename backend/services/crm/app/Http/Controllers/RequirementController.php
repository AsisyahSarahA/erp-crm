<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\GenerateRabJob;
use App\Models\Lead;
use App\Models\RabItem;
use App\Models\RabVersion;
use App\Models\RequirementFile;
use App\Models\RequirementVersion;
use App\Policies\LeadPolicy;
use App\Services\AiRabGeneratorService;
use App\Services\RequirementIngestionService;
use App\Support\UserContext;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Astacode\Kernel\Support\Terbilang;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RequirementController extends Controller
{
    public function __construct(
        private readonly RequirementIngestionService $ingestionService = new RequirementIngestionService(),
        private readonly AiRabGeneratorService $generatorService = new AiRabGeneratorService(),
        private readonly LeadPolicy $leadPolicy = new LeadPolicy(),
    ) {}

    /**
     * GET /leads/{id}/requirements — List all requirement versions for a Lead.
     */
    public function index(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->leadPolicy->view($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Akses ditolak.');
        }

        $requirements = RequirementVersion::with(['files', 'aiRun', 'rabVersion.items'])
            ->where('lead_id', $lead->id)
            ->orderByDesc('version')
            ->get();

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'active_rab_version_id' => $lead->active_rab_version_id,
            'requirements' => $requirements->toArray(),
        ]);
    }

    /**
     * POST /leads/{id}/requirements — Save Requirement Version (prompt + files + params) & auto-transition to Requirement stage.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->leadPolicy->update($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin mengubah requirement lead ini.');
        }

        $result = $this->createRequirementVersionFromRequest($request, $lead, $userContext, initialStatus: 'DRAFT');
        if ($result instanceof JsonResponse) {
            return $result;
        }

        /** @var RequirementVersion $requirement */
        $requirement = $result;
        $lead = $this->ingestionService->autoTransitionLeadToRequirement($lead->fresh(), $userContext);

        $requirement->load(['files', 'aiRun', 'rabVersion.items']);

        return ApiResponse::success([
            'requirement' => $requirement->toArray(),
            'lead_stage_id' => $lead->stage_id,
            'lead_stage' => $lead->stage,
        ], ['message' => 'Requirement berhasil disimpan dan stage Lead tercatat di tahap Requirement.'], 201);
    }

    /**
     * POST /leads/{id}/requirements/generate — Save RequirementVersion (status=PROCESSING) & dispatch GenerateRabJob.
     * Supports FULL generation, REGENERATE_FEEDBACK, and PARTIAL_SECTION.
     */
    public function generate(Request $request, int $id): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        if (!$this->leadPolicy->update($userContext, $lead)) {
            return ApiResponse::rfcProblem('Forbidden', 403, 'Anda tidak memiliki izin menjalankan AI Generate RAB pada lead ini.');
        }

        $requirement = null;

        // If regenerating or generating from an existing requirement without new prompt/files, clone it into a new version
        if ($request->filled('requirement_id') && !$request->filled('prompt_text') && empty($request->allFiles()) && !$request->filled('files')) {
            $baseReq = RequirementVersion::with('files')
                ->where('lead_id', $lead->id)
                ->find((int) $request->input('requirement_id'));

            if (!$baseReq) {
                return ApiResponse::rfcProblem('Requirement Not Found', 404, 'Versi requirement acuan tidak ditemukan.');
            }

            $nextVersion = ((int) RequirementVersion::where('lead_id', $lead->id)->max('version')) + 1;
            $mode = strtoupper((string) $request->input('generation_mode', $request->filled('feedback_text') ? 'REGENERATE_FEEDBACK' : ($request->filled('target_section') ? 'PARTIAL_SECTION' : 'FULL')));

            $requirement = RequirementVersion::create([
                'lead_id' => $lead->id,
                'version' => $nextVersion,
                'prompt_text' => $baseReq->prompt_text,
                'status' => 'PROCESSING',
                'params' => array_merge((array) ($baseReq->params ?? []), (array) $request->input('params', [])),
                'generation_mode' => $mode,
                'feedback_text' => $request->input('feedback_text'),
                'target_section' => $request->input('target_section'),
                'pii_redacted' => (bool) ($request->input('params.redact_pii', $baseReq->pii_redacted)),
                'zero_retention' => (bool) ($request->input('params.zero_retention', $baseReq->zero_retention)),
                'created_by' => $userContext['id'] ?? 1,
            ]);

            foreach ($baseReq->files as $f) {
                RequirementFile::create([
                    'requirement_id' => $requirement->id,
                    'lead_id' => $lead->id,
                    'file_id' => $f->file_id,
                    'file_name' => $f->file_name,
                    'mime_type' => $f->mime_type,
                    'file_size_bytes' => $f->file_size_bytes,
                    'file_path' => $f->file_path,
                    'kind' => $f->kind,
                    'antivirus_status' => $f->antivirus_status,
                    'transcript_text' => $f->transcript_text,
                ]);
            }
        } else {
            $result = $this->createRequirementVersionFromRequest($request, $lead, $userContext, initialStatus: 'PROCESSING');
            if ($result instanceof JsonResponse) {
                return $result;
            }
            $requirement = $result;
        }

        // Auto-transition lead to Requirement stage when requirement is submitted
        $lead = $this->ingestionService->autoTransitionLeadToRequirement($lead->fresh(), $userContext);

        // Dispatch async GenerateRabJob
        GenerateRabJob::dispatch($requirement->id);

        $requirement = $requirement->fresh(['files', 'aiRun', 'rabVersion.items']);
        $lead = $lead->fresh();

        return ApiResponse::success([
            'initial_status' => 'PROCESSING',
            'requirement' => $requirement->toArray(),
            'rab_draft' => $requirement->rabVersion ? $requirement->rabVersion->toArray() : null,
            'ai_run' => $requirement->aiRun ? $requirement->aiRun->toArray() : null,
            'lead_stage_id' => $lead->stage_id,
            'active_rab_version_id' => $lead->active_rab_version_id,
        ], ['message' => 'AI Generate RAB berhasil diproses.'], 201);
    }

    /**
     * POST /leads/{id}/requirements/{requirementId}/use-as-rab — Action "Gunakan sebagai RAB".
     * Sets the AI draft RAB as the Lead's active RAB without overwriting prior versions and without touching Quotations (BR-CRM-10).
     */
    public function useAsRab(Request $request, int $id, int $requirementId): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $requirement = RequirementVersion::with(['rabVersion.items', 'aiRun'])
            ->where('lead_id', $lead->id)
            ->find($requirementId);

        if (!$requirement || !$requirement->rabVersion) {
            return ApiResponse::rfcProblem('RAB Draft Not Found', 404, 'Draf RAB hasil AI untuk versi requirement ini tidak ditemukan.');
        }

        $lead->active_rab_version_id = $requirement->rabVersion->id;
        $lead->saveQuietly();

        if ($requirement->aiRun) {
            $requirement->aiRun->update([
                'human_decision' => 'USED_AS_RAB',
                'decided_by' => (string) ($userContext['id'] ?? '1'),
                'decided_at' => now(),
            ]);
        }

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'active_rab_version_id' => $lead->active_rab_version_id,
            'rab_version' => $requirement->rabVersion->fresh('items')->toArray(),
            'human_decision' => 'USED_AS_RAB',
        ], ['message' => 'Draf RAB hasil AI berhasil digunakan sebagai RAB aktif.']);
    }

    /**
     * POST /leads/{id}/requirements/{requirementId}/merge-to-rab — Action "Gabungkan (merge) ke RAB saat ini".
     * Creates a new RAB version combining existing active RAB items + new AI RAB items without overwriting the old version.
     */
    public function mergeToRab(Request $request, int $id, int $requirementId): JsonResponse
    {
        $userContext = UserContext::resolve($request);
        $lead = Lead::find($id);

        if (!$lead) {
            return ApiResponse::rfcProblem('Lead Not Found', 404, "Lead #{$id} tidak ditemukan.");
        }

        $requirement = RequirementVersion::with(['rabVersion.items', 'aiRun'])
            ->where('lead_id', $lead->id)
            ->find($requirementId);

        if (!$requirement || !$requirement->rabVersion) {
            return ApiResponse::rfcProblem('RAB Draft Not Found', 404, 'Draf RAB hasil AI untuk versi requirement ini tidak ditemukan.');
        }

        $currentActiveRab = $lead->active_rab_version_id
            ? RabVersion::with('items')->find($lead->active_rab_version_id)
            : null;

        if (!$currentActiveRab) {
            return $this->useAsRab($request, $id, $requirementId);
        }

        $mergedRab = DB::transaction(function () use ($lead, $currentActiveRab, $requirement, $userContext) {
            $nextVersion = ((int) RabVersion::where('lead_id', $lead->id)->max('version')) + 1;
            $aiRab = $requirement->rabVersion;

            $combinedItems = collect($currentActiveRab->items)
                ->concat($aiRab->items)
                ->values();

            $subtotalSell = (float) $combinedItems->sum(fn ($i) => (float) ($i->subtotal_sell ?: $i->subtotal));
            $totalHpp = (float) $combinedItems->sum(fn ($i) => (float) ($i->subtotal_cost ?: $i->hpp));
            $totalMandays = (float) $combinedItems->sum('mandays');

            $contingencyPct = (float) ($currentActiveRab->contingency_pct ?? 10.0);
            $contingencyAmount = round($subtotalSell * ($contingencyPct / 100.0), 2);
            $dpp = round($subtotalSell + $contingencyAmount, 2);
            $vatRate = (float) ($currentActiveRab->vat_rate ?? 11.0);
            $vatAmount = round($dpp * ($vatRate / 100.0), 2);
            $total = round($dpp + $vatAmount, 2);
            $marginPct = $dpp > 0 ? round((($dpp - $totalHpp) / $dpp) * 100.0, 2) : 0.0;
            $marginStatus = $marginPct >= 40.0 ? 'GREEN' : ($marginPct >= 25.0 ? 'YELLOW' : 'RED');

            $newRab = RabVersion::create([
                'lead_id' => $lead->id,
                'requirement_id' => $requirement->id,
                'ai_run_id' => $requirement->ai_run_id,
                'version' => $nextVersion,
                'source' => 'MERGED',
                'status' => 'DRAFT',
                'total_mandays' => round($totalMandays, 2),
                'duration_weeks' => max(2, (int) ceil($totalMandays / 10.0)),
                'subtotal' => $subtotalSell,
                'contingency_pct' => $contingencyPct,
                'contingency_amount' => $contingencyAmount,
                'dpp' => $dpp,
                'tax_rate' => $vatRate,
                'vat_rate' => $vatRate,
                'tax' => $vatAmount,
                'vat_amount' => $vatAmount,
                'total' => $total,
                'total_terbilang' => Terbilang::rupiah((int) round($total)),
                'hpp_internal' => $totalHpp,
                'margin_pct' => $marginPct,
                'margin_status' => $marginStatus,
                'wbs_completed' => true,
                'assumptions' => array_values(array_unique(array_merge(
                    (array) ($currentActiveRab->assumptions ?? []),
                    (array) ($aiRab->assumptions ?? [])
                ))),
                'risks' => array_values(array_unique(array_merge(
                    (array) ($currentActiveRab->risks ?? []),
                    (array) ($aiRab->risks ?? [])
                ))),
                'out_of_scope' => array_values(array_unique(array_merge(
                    (array) ($currentActiveRab->out_of_scope ?? []),
                    (array) ($aiRab->out_of_scope ?? [])
                ))),
                'clarifying_questions' => (array) ($aiRab->clarifying_questions ?? []),
                'tech_stack' => (array) ($aiRab->tech_stack ?: $currentActiveRab->tech_stack),
                'created_by' => (string) ($userContext['id'] ?? '1'),
            ]);

            foreach ($combinedItems as $idx => $oldItem) {
                RabItem::create([
                    'rab_version_id' => $newRab->id,
                    'lead_id' => $lead->id,
                    'sort_order' => $idx + 1,
                    'category' => $oldItem->category,
                    'layer' => $oldItem->layer,
                    'name' => $oldItem->name,
                    'description' => $oldItem->description,
                    'role' => $oldItem->role,
                    'role_code' => $oldItem->role_code,
                    'mandays' => $oldItem->mandays,
                    'qty' => $oldItem->qty,
                    'unit' => $oldItem->unit,
                    'daily_rate_sell' => $oldItem->daily_rate_sell,
                    'daily_rate_cost' => $oldItem->daily_rate_cost,
                    'unit_price' => $oldItem->unit_price,
                    'subtotal_sell' => $oldItem->subtotal_sell,
                    'subtotal_cost' => $oldItem->subtotal_cost,
                    'subtotal' => $oldItem->subtotal,
                    'cost_rate' => $oldItem->cost_rate,
                    'hpp' => $oldItem->hpp,
                    'complexity' => $oldItem->complexity,
                    'confidence' => $oldItem->confidence,
                    'origin' => $oldItem->origin,
                    'ai_confidence' => $oldItem->ai_confidence,
                    'notes' => $oldItem->notes,
                ]);
            }

            $lead->active_rab_version_id = $newRab->id;
            $lead->saveQuietly();

            if ($requirement->aiRun) {
                $requirement->aiRun->update([
                    'human_decision' => 'MERGED_TO_RAB',
                    'decided_by' => (string) ($userContext['id'] ?? '1'),
                    'decided_at' => now(),
                ]);
            }

            return $newRab->fresh('items');
        });

        return ApiResponse::success([
            'lead_id' => $lead->id,
            'previous_rab_version_id' => $currentActiveRab->id,
            'active_rab_version_id' => $mergedRab->id,
            'rab_version' => $mergedRab->toArray(),
            'human_decision' => 'MERGED_TO_RAB',
        ], ['message' => 'Draf RAB hasil AI berhasil digabungkan ke RAB baru tanpa menimpa versi sebelumnya.']);
    }

    /**
     * Validate request (requiring at least prompt_text or files), scan files for virus & 25MB limit,
     * and persist a new RequirementVersion + RequirementFiles.
     */
    private function createRequirementVersionFromRequest(
        Request $request,
        Lead $lead,
        array $userContext,
        string $initialStatus,
    ): RequirementVersion|JsonResponse {
        $promptText = trim((string) ($request->input('prompt_text') ?? $request->input('prompt') ?? ''));
        $uploadedFiles = $request->file('files') ? (is_array($request->file('files')) ? $request->file('files') : [$request->file('files')]) : [];
        $metaFiles = is_array($request->input('files')) ? $request->input('files') : [];

        // Filter out any UploadedFile instances already captured in $uploadedFiles
        $metaFiles = array_values(array_filter($metaFiles, fn ($f) => is_array($f)));
        $allFilesInput = array_merge($uploadedFiles, $metaFiles);

        if ($promptText === '' && empty($allFilesInput)) {
            return ApiResponse::rfcProblem(
                title: 'Validation Failed',
                status: 422,
                detail: 'Wajib mengisi prompt teks kebutuhan atau mengunggah minimal 1 file dokumen/rekaman (FR-CRM-05).',
                invalidParams: [
                    'prompt_text' => ['Prompt teks atau file requirement wajib diisi minimal salah satu.'],
                ]
            );
        }

        // Validate & Antivirus Scan each file (Max 25 MB)
        $validatedFilesMeta = [];
        foreach ($allFilesInput as $fileInput) {
            $scan = $this->ingestionService->validateAndScanFile($fileInput);
            if (!$scan['is_valid']) {
                return ApiResponse::rfcProblem(
                    title: $scan['error_code'] === 'VIRUS_DETECTED' ? 'Antivirus Security Alert' : 'File Size Limit Exceeded',
                    status: 422,
                    detail: (string) $scan['message'],
                    extra: ['error_code' => $scan['error_code']]
                );
            }
            $validatedFilesMeta[] = $scan['file_meta'];
        }

        $rawParams = (array) $request->input('params', []);
        $params = [
            'platform' => $rawParams['platform'] ?? $request->input('platform', 'keduanya'),
            'tech_preference' => $rawParams['tech_preference'] ?? $request->input('tech_preference', 'Laravel + React'),
            'user_count' => $rawParams['user_count'] ?? $request->input('user_count', 200),
            'mandatory_integrations' => (array) ($rawParams['mandatory_integrations'] ?? $request->input('mandatory_integrations', [])),
            'deadline' => $rawParams['deadline'] ?? $request->input('deadline'),
            'detail_level' => $rawParams['detail_level'] ?? $request->input('detail_level', 'Standar'),
            'productivity_assumption' => $rawParams['productivity_assumption'] ?? $request->input('productivity_assumption', 'normal'),
            'catalog_template' => $rawParams['catalog_template'] ?? $request->input('catalog_template'),
            'redact_pii' => (bool) ($rawParams['redact_pii'] ?? $request->boolean('redact_pii', false)),
            'zero_retention' => (bool) ($rawParams['zero_retention'] ?? $request->boolean('zero_retention', false)),
        ];

        $generationMode = strtoupper((string) $request->input(
            'generation_mode',
            $request->filled('feedback_text') ? 'REGENERATE_FEEDBACK' : ($request->filled('target_section') ? 'PARTIAL_SECTION' : 'FULL')
        ));

        return DB::transaction(function () use ($lead, $promptText, $params, $generationMode, $request, $validatedFilesMeta, $userContext, $initialStatus) {
            $nextVersion = ((int) RequirementVersion::where('lead_id', $lead->id)->max('version')) + 1;

            $requirement = RequirementVersion::create([
                'lead_id' => $lead->id,
                'version' => $nextVersion,
                'prompt_text' => $promptText !== '' ? $promptText : null,
                'status' => $initialStatus,
                'params' => $params,
                'generation_mode' => $generationMode,
                'feedback_text' => $request->input('feedback_text'),
                'target_section' => $request->input('target_section'),
                'pii_redacted' => (bool) $params['redact_pii'],
                'zero_retention' => (bool) $params['zero_retention'],
                'created_by' => $userContext['id'] ?? 1,
            ]);

            foreach ($validatedFilesMeta as $meta) {
                RequirementFile::create(array_merge($meta, [
                    'requirement_id' => $requirement->id,
                    'lead_id' => $lead->id,
                ]));
            }

            return $requirement;
        });
    }
}

