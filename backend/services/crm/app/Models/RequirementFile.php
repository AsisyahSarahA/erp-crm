<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequirementFile extends Model
{
    protected $table = 'crm_requirement_files';

    public const KINDS = [
        'doc',
        'audio',
        'image',
    ];

    protected $fillable = [
        'requirement_id',
        'lead_id',
        'file_id',
        'file_name',
        'mime_type',
        'file_size_bytes',
        'file_path',
        'kind',
        'antivirus_status',
        'transcript_text',
    ];

    protected $casts = [
        'requirement_id' => 'integer',
        'lead_id' => 'integer',
        'file_id' => 'integer',
        'file_size_bytes' => 'integer',
    ];

    public static function resolveKind(?string $mimeType, string $fileName): string
    {
        $mime = strtolower((string) $mimeType);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (str_starts_with($mime, 'audio/') || str_starts_with($mime, 'video/') || in_array($ext, ['mp3', 'm4a', 'wav', 'mp4', 'ogg', 'webm'], true)) {
            return 'audio';
        }

        if (str_starts_with($mime, 'image/') || in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true)) {
            return 'image';
        }

        return 'doc';
    }

    public function requirement(): BelongsTo
    {
        return $this->belongsTo(RequirementVersion::class, 'requirement_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }
}

