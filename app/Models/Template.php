<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Template extends OracleModel
{
    protected $table = 'templates';

    protected $fillable = [
        'template_category_id',
        'university_id',
        'academic_field_id',
        'title',
        'description',
        'template_url',
        'price',
        'is_paid',
        'status',
        'download_count',
        'average_rating',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_paid' => 'boolean',
            'download_count' => 'integer',
            'average_rating' => 'decimal:2',
        ];
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'APPROVED');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TemplateCategory::class, 'template_category_id');
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function academicField(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function savedTemplates(): HasMany
    {
        return $this->hasMany(SavedTemplate::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'saved_templates')
            ->withPivot('id', 'created_at');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(TemplatePurchase::class);
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable', 'reviewable_type', 'reviewable_id');
    }
}
