<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FileRecord;
use App\Services\NullVirusScanner;
use App\Services\VirusScannerInterface;
use Astacode\Kernel\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{
    private VirusScannerInterface $virusScanner;

    public function __construct(?VirusScannerInterface $virusScanner = null)
    {
        $this->virusScanner = $virusScanner ?? new NullVirusScanner();
    }

    /**
     * List uploaded files with filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $query = FileRecord::with('uploader:id,name,email');

        if ($request->filled('attachable_type')) {
            $query->where('attachable_type', $request->attachable_type);
        }

        if ($request->filled('attachable_id')) {
            $query->where('attachable_id', $request->attachable_id);
        }

        $files = $query->latest()->paginate(min((int) $request->get('per_page', 20), 100));

        return ApiResponse::paginated($files);
    }

    /**
     * Upload a new file (up to 25MB) with SHA-256 hash and virus scan.
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:25600', // 25 MB max
            'attachable_type' => 'nullable|string|max:100',
            'attachable_id' => 'nullable|string|max:100',
            'is_private' => 'nullable|boolean',
        ]);

        $uploadedFile = $request->file('file');
        $tempPath = $uploadedFile->getRealPath();

        // 1. Virus Scanning
        $scanStatus = $this->virusScanner->scan($tempPath);
        if ($scanStatus === 'infected') {
            return ApiResponse::rfcProblem(
                title: 'Malware Detected',
                status: 422,
                detail: 'File terdeteksi mengandung ancaman/malware dan ditolak oleh sistem keamanan.',
                type: 'https://astacode.com/errors/virus-detected'
            );
        }

        // 2. SHA-256 checksum
        $hashSha256 = hash_file('sha256', $tempPath);

        // 3. Store to disk
        $disk = config('filesystems.default', 'local');
        $storedPath = $uploadedFile->store('uploads/' . date('Y/m'), $disk);

        $fileRecord = FileRecord::create([
            'name' => pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
            'original_name' => $uploadedFile->getClientOriginalName(),
            'path' => $storedPath,
            'disk' => $disk,
            'mime_type' => $uploadedFile->getMimeType() ?? 'application/octet-stream',
            'size_bytes' => $uploadedFile->getSize(),
            'hash_sha256' => $hashSha256,
            'version' => 1,
            'virus_scan_status' => $scanStatus,
            'uploaded_by' => $request->user()?->id,
            'attachable_type' => $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'is_private' => $request->boolean('is_private', true),
        ]);

        AuditLog::create([
            'user_id' => (string) ($request->user()?->id ?? 'system'),
            'service' => 'core-service',
            'action' => 'file.uploaded',
            'entity_type' => 'file',
            'entity_id' => (string) $fileRecord->id,
            'new_values' => ['name' => $fileRecord->original_name, 'size' => $fileRecord->size_bytes, 'sha256' => $hashSha256],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'notes' => "File {$fileRecord->original_name} diunggah.",
        ]);

        return ApiResponse::success([
            'file' => $fileRecord,
            'download_url' => $fileRecord->generateSignedUrl(60),
        ], 'File berhasil diunggah.', 201);
    }

    /**
     * Download file via signed URL or authenticated request.
     */
    public function download(Request $request, string $id): BinaryFileResponse|JsonResponse
    {
        $file = FileRecord::find($id);
        if (!$file) {
            return ApiResponse::rfcProblem('File Not Found', 404, "File dengan ID {$id} tidak ditemukan.");
        }

        if (!Storage::disk($file->disk)->exists($file->path)) {
            return ApiResponse::rfcProblem('File Missing', 404, 'File fisik tidak ditemukan pada storage server.');
        }

        $fullPath = Storage::disk($file->disk)->path($file->path);

        return response()->download($fullPath, $file->original_name, [
            'Content-Type' => $file->mime_type,
        ]);
    }

    /**
     * Generate a temporary signed download URL for client sharing.
     */
    public function signedUrl(Request $request, string $id): JsonResponse
    {
        $file = FileRecord::find($id);
        if (!$file) {
            return ApiResponse::rfcProblem('File Not Found', 404);
        }

        $minutes = (int) $request->get('expires_in', 60);
        $url = $file->generateSignedUrl($minutes);

        return ApiResponse::success([
            'signed_url' => $url,
            'expires_at' => now()->addMinutes($minutes)->toIso8601String(),
        ]);
    }

    /**
     * View file metadata and checksum.
     */
    public function show(string $id): JsonResponse
    {
        $file = FileRecord::with('uploader:id,name,email')->find($id);
        if (!$file) {
            return ApiResponse::rfcProblem('File Not Found', 404);
        }

        return ApiResponse::success($file);
    }
}

