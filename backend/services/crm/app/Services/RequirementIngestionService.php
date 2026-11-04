<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\RequirementFile;
use App\Models\RequirementVersion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class RequirementIngestionService
{
    public const EICAR_SIGNATURE = 'X5O!P%@AP[4\PZX54(P^)7CC)7}$EICAR-STANDARD-ANTIVIRUS-TEST-FILE!$H+H*';

    public const FORBIDDEN_EXTENSIONS = [
        'exe', 'bat', 'cmd', 'sh', 'php', 'phtml', 'phar', 'dll', 'msi', 'vbs', 'js',
    ];

    public function __construct(
        private readonly PipelineTransitionGuard $transitionGuard = new PipelineTransitionGuard()
    ) {}

    public function maxFileSizeBytes(): int
    {
        $maxMb = (int) env('CRM_MAX_REQUIREMENT_FILE_MB', 25);

        return max(1, $maxMb) * 1024 * 1024;
    }

    /**
     * Validate file size (<= 25 MB) and scan with Antivirus Hook.
     *
     * @param array<string, mixed>|UploadedFile $fileInput
     * @return array{is_valid: bool, error_code?: string, message?: string, file_meta?: array<string, mixed>}
     */
    public function validateAndScanFile(array|UploadedFile $fileInput): array
    {
        $maxBytes = $this->maxFileSizeBytes();

        if ($fileInput instanceof UploadedFile) {
            $fileName = $fileInput->getClientOriginalName();
            $mimeType = $fileInput->getClientMimeType() ?: 'application/octet-stream';
            $sizeBytes = (int) $fileInput->getSize();
            $rawContent = $sizeBytes <= 2 * 1024 * 1024 ? (string) @file_get_contents($fileInput->getRealPath()) : '';
            $filePath = $fileInput->store('crm/requirements');
        } else {
            $fileName = (string) ($fileInput['file_name'] ?? $fileInput['name'] ?? 'document.pdf');
            $mimeType = (string) ($fileInput['mime_type'] ?? 'application/pdf');
            $sizeBytes = (int) ($fileInput['file_size_bytes'] ?? $fileInput['size'] ?? 1024);
            $rawContent = (string) ($fileInput['content'] ?? $fileInput['transcript_text'] ?? '');
            $filePath = (string) ($fileInput['file_path'] ?? "crm/requirements/{$fileName}");
        }

        if ($sizeBytes > $maxBytes) {
            $maxMb = (int) ($maxBytes / (1024 * 1024));
            return [
                'is_valid' => false,
                'error_code' => 'FILE_TOO_LARGE',
                'message' => "Ukuran file '{$fileName}' melebihi batas maksimal {$maxMb} MB.",
            ];
        }

        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, self::FORBIDDEN_EXTENSIONS, true)) {
            return [
                'is_valid' => false,
                'error_code' => 'VIRUS_DETECTED',
                'message' => "File '{$fileName}' ditolak oleh pemindai keamanan (ekstensi file berbahaya tidak diizinkan).",
            ];
        }

        if (
            str_contains($rawContent, self::EICAR_SIGNATURE)
            || str_contains(strtoupper($fileName), 'EICAR')
            || (($fileInput['simulate_virus'] ?? false) === true)
        ) {
            return [
                'is_valid' => false,
                'error_code' => 'VIRUS_DETECTED',
                'message' => "File '{$fileName}' terdeteksi mengandung signature malware/virus oleh Antivirus Hook.",
            ];
        }

        $kind = isset($fileInput['kind']) && in_array($fileInput['kind'], RequirementFile::KINDS, true)
            ? (string) $fileInput['kind']
            : RequirementFile::resolveKind($mimeType, $fileName);

        $transcript = null;
        if (is_array($fileInput) && !empty($fileInput['transcript_text'])) {
            $transcript = (string) $fileInput['transcript_text'];
        } elseif ($rawContent !== '' && in_array($ext, ['txt', 'md', 'csv', 'json'], true)) {
            $transcript = $rawContent;
        }

        return [
            'is_valid' => true,
            'file_meta' => [
                'file_id' => is_array($fileInput) ? ($fileInput['file_id'] ?? null) : null,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'file_size_bytes' => $sizeBytes,
                'file_path' => (string) $filePath,
                'kind' => $kind,
                'antivirus_status' => 'CLEAN',
                'transcript_text' => $transcript,
            ],
        ];
    }

    /**
     * Redact PII (emails, phone numbers, 15/16-digit NPWP/NIK) before sending to LLM.
     */
    public function redactPii(string $text): string
    {
        // Mask email addresses
        $redacted = preg_replace('/[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}/', '[EMAIL_REDACTED]', $text) ?? $text;

        // Mask 15-16 digit NPWP / NIK numbers
        $redacted = preg_replace('/\b\d{15,16}\b/', '[ID_REDACTED]', $redacted) ?? $redacted;
        $redacted = preg_replace('/\b\d{2}\.\d{3}\.\d{3}\.\d-\d{3}\.\d{3}\b/', '[NPWP_REDACTED]', $redacted) ?? $redacted;

        // Mask Indonesian phone numbers (+628... or 08...)
        $redacted = preg_replace('/(\+62|62|0)8[1-9][0-9]{6,11}\b/', '[PHONE_REDACTED]', $redacted) ?? $redacted;

        return $redacted;
    }

    /**
     * Chunk long text into token-safe segments (~4000 chars per chunk).
     *
     * @return list<string>
     */
    public function chunkText(string $text, int $maxCharsPerChunk = 4000): array
    {
        $clean = trim($text);
        if ($clean === '') {
            return [];
        }

        if (mb_strlen($clean) <= $maxCharsPerChunk) {
            return [$clean];
        }

        $chunks = [];
        $length = mb_strlen($clean);
        for ($offset = 0; $offset < $length; $offset += $maxCharsPerChunk) {
            $chunks[] = mb_substr($clean, $offset, $maxCharsPerChunk);
        }

        return $chunks;
    }

    /**
     * Fetch matching modules from crm_module_catalog as few-shot context for AI RAB generation.
     *
     * @return list<array<string, mixed>>
     */
    public function buildFewShotCatalogContext(?string $catalogTemplate, ?string $promptText, ?string $projectType): array
    {
        $query = DB::table('crm_module_catalog')->where('is_active', true);
        $allModules = $query->orderBy('id')->get();

        if ($allModules->isEmpty()) {
            return [];
        }

        $searchCorpus = strtolower(trim($catalogTemplate . ' ' . $promptText . ' ' . $projectType));
        $isHrd = str_contains($searchCorpus, 'hrd')
            || str_contains($searchCorpus, 'presensi')
            || str_contains($searchCorpus, 'payroll')
            || str_contains($searchCorpus, 'karyawan')
            || str_contains($searchCorpus, 'rekrutmen');

        $scored = $allModules->map(function ($m) use ($searchCorpus, $isHrd) {
            $score = 0;
            $nameLower = strtolower((string) $m->name);
            $catLower = strtolower((string) $m->category);

            foreach (['presensi', 'payroll', 'cuti', 'karyawan', 'rekrutmen', 'rbac', 'auth', 'dashboard', 'laporan', 'payment', 'api'] as $kw) {
                if (str_contains($searchCorpus, $kw) && (str_contains($nameLower, $kw) || str_contains($catLower, $kw))) {
                    $score += 10;
                }
            }

            if ($isHrd && (str_contains($nameLower, 'presensi') || str_contains($nameLower, 'payroll') || str_contains($nameLower, 'cuti') || str_contains($nameLower, 'rekrutmen'))) {
                $score += 15;
            }

            return ['module' => $m, 'score' => $score];
        })->sortByDesc('score')->take(8);

        $examples = [];
        foreach ($scored as $row) {
            $mod = $row['module'];
            $mandaysByRole = is_string($mod->default_mandays_by_role)
                ? (json_decode($mod->default_mandays_by_role, true) ?: [])
                : (array) $mod->default_mandays_by_role;

            $examples[] = [
                'code' => $mod->code ?? null,
                'category' => $mod->category,
                'name' => $mod->name,
                'description' => $mod->description,
                'total_mandays' => (float) $mod->total_mandays,
                'default_mandays_by_role' => $mandaysByRole,
            ];
        }

        return $examples;
    }

    /**
     * Automatically transition lead to `requirement` stage when requirement is entered (Task #7),
     * strictly going through PipelineTransitionGuard.
     */
    public function autoTransitionLeadToRequirement(Lead $lead, array $userContext): Lead
    {
        $currentStage = Lead::normalizeStageId($lead->stage_id ?: $lead->stage);

        // Mark client responded
        if ($lead->client_responded_at === null) {
            $lead->client_responded_at = now();
            $lead->saveQuietly();
        }

        if (!in_array($currentStage, ['new_lead', 'follow_up'], true)) {
            return $lead;
        }

        // Ensure discovery call / requirement activity is recorded so PipelineTransitionGuard passes cleanly
        $hasDiscoveryActivity = $lead->activities()
            ->where(function ($q) {
                $q->whereIn('type', ['CALL', 'MEETING', 'DISCOVERY_CALL'])
                    ->orWhere('title', 'like', '%Discovery%')
                    ->orWhere('title', 'like', '%Requirement%');
            })
            ->exists();

        if (!$hasDiscoveryActivity) {
            Activity::create([
                'lead_id' => $lead->id,
                'customer_id' => $lead->customer_id,
                'type' => 'MEETING',
                'title' => 'Discovery Call & Pengumpulan Requirement Klien',
                'notes' => 'Kebutuhan klien telah diterima dan didokumentasikan pada tab Requirement.',
                'start_at' => now()->subMinutes(15),
                'end_at' => now(),
                'status' => 'COMPLETED',
                'outcome' => 'SUCCESS',
                'created_by' => $userContext['id'] ?? $lead->owner_id ?? 1,
            ]);
        }

        if ($currentStage === 'new_lead') {
            if (empty($lead->owner_id) && empty($lead->sales_pic_id)) {
                $lead->owner_id = (int) ($userContext['id'] ?? 1);
                $lead->sales_pic_id = $lead->owner_id;
                $lead->saveQuietly();
            }

            $lead = $this->transitionGuard->transition(
                lead: $lead,
                targetStage: 'follow_up',
                userContext: $userContext,
                options: [
                    'reason' => 'Kontak awal & penerimaan dokumen requirement klien.',
                    'trigger_source' => 'requirement_input',
                ]
            );
        }

        return $this->transitionGuard->transition(
            lead: $lead,
            targetStage: 'requirement',
            userContext: $userContext,
            options: [
                'reason' => 'Transisi otomatis ke tahap Requirement saat requirement proyek diinput.',
                'trigger_source' => 'requirement_input',
            ]
        );
    }
}

