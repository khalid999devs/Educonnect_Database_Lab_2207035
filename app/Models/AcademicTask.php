<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicTask extends OracleModel
{
    protected $table = 'academic_tasks';

    protected $fillable = [
        'name',
        'description',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class, 'task_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class, 'task_id');
    }
}
