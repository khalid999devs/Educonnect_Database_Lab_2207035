<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends OracleModel
{
    protected $table = 'students';

    protected $fillable = [
        'user_id',
        'university_id',
        'academic_field_id',
        'department',
        'semester',
        'skill_level',
        'bio',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function academicField(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class);
    }

    public function preferences(): HasMany
    {
        return $this->hasMany(StudentPreference::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AcademicDocument::class);
    }

    public function extractedDocumentData(): HasMany
    {
        return $this->hasMany(ExtractedDocumentData::class);
    }

    public function savedResources(): HasMany
    {
        return $this->hasMany(SavedResource::class);
    }

    public function resources(): BelongsToMany
    {
        return $this->belongsToMany(Resource::class, 'saved_resources')
            ->withPivot('id', 'created_at');
    }

    public function savedTemplates(): HasMany
    {
        return $this->hasMany(SavedTemplate::class);
    }

    public function templates(): BelongsToMany
    {
        return $this->belongsToMany(Template::class, 'saved_templates')
            ->withPivot('id', 'created_at');
    }

    public function templatePurchases(): HasMany
    {
        return $this->hasMany(TemplatePurchase::class);
    }

    public function researchTopics(): HasMany
    {
        return $this->hasMany(ResearchTopic::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
