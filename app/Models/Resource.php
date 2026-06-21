<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Resource extends OracleModel
{
    protected $table = 'resources';

    protected $fillable = [
        'resource_category_id',
        'academic_field_id',
        'task_id',
        'title',
        'description',
        'resource_url',
        'difficulty_level',
        'status',
        'save_count',
        'average_rating',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'save_count' => 'integer',
            'average_rating' => 'decimal:2',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }

    public function academicField(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(AcademicTask::class, 'task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function savedResources(): HasMany
    {
        return $this->hasMany(SavedResource::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'saved_resources')
            ->withPivot('id', 'created_at');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable', 'reviewable_type', 'reviewable_id');
    }
}
