<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPreference extends OracleModel
{
    protected $table = 'student_preferences';

    protected $fillable = [
        'student_id',
        'goal_type',
        'preference_key',
        'preference_value',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
