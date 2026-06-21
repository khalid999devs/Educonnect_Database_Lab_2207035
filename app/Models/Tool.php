<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Tool extends OracleModel
{
    protected $table = 'tools';

    protected $fillable = [
        'tool_category_id',
        'name',
        'description',
        'website_url',
        'academic_field_id',
        'task_id',
        'is_free',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_free' => 'boolean',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ToolCategory::class, 'tool_category_id');
    }

    public function academicField(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(AcademicTask::class, 'task_id');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable', 'reviewable_type', 'reviewable_id');
    }
}
