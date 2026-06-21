<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicField extends OracleModel
{
    protected $table = 'academic_fields';

    protected $fillable = [
        'name',
        'description',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function researchTopics(): HasMany
    {
        return $this->hasMany(ResearchTopic::class);
    }
}
