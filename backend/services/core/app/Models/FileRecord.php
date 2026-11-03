<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class FileRecord extends Model
{
    use HasUlids, SoftDeletes;

    protected $table = 'files';

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'disk',
        'mime_type',
        'size_bytes',
        'hash_sha256',
        'version',
        'virus_scan_status',
        'uploaded_by',
        'attachable_type',
        'attachable_id',
        'is_private',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
        'version' => 'integer',
        'is_private' => 'boolean',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function generateSignedUrl(int $expirationMinutes = 30): string
    {
        return URL::temporarySignedRoute(
            'files.download',
            now()->addMinutes($expirationMinutes),
            ['id' => $this->id]
        );
    }
}

