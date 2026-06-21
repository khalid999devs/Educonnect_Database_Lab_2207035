<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends OracleModel
{
    public const UPDATED_AT = null;

    protected $table = 'audit_logs';

    protected $fillable = [
        'table_name',
        'record_id',
        'action_type',
        'old_value',
        'new_value',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'record_id' => 'integer',
        ];
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
