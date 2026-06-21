<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchTopic extends OracleModel
{
    protected $table = 'research_topics';

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'academic_field_id',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicField(): BelongsTo
    {
        return $this->belongsTo(AcademicField::class);
    }

    public function collections(): HasMany
    {
        return $this->hasMany(ResearchCollection::class);
    }
}
