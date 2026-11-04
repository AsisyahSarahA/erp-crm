<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerMergeHistory extends Model
{
    protected $table = 'crm_customer_merge_histories';

    protected $fillable = [
        'master_customer_id',
        'merged_customer_id',
        'merged_customer_code',
        'merged_customer_snapshot',
        'migrated_records_summary',
        'reason',
        'merged_by',
        'merged_at',
    ];

    protected $casts = [
        'master_customer_id' => 'integer',
        'merged_customer_id' => 'integer',
        'merged_customer_snapshot' => 'array',
        'migrated_records_summary' => 'array',
        'merged_by' => 'integer',
        'merged_at' => 'datetime',
    ];

    public function masterCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'master_customer_id');
    }
}

