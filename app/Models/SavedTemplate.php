<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedTemplate extends OracleModel
{
    public const UPDATED_AT = null;

    protected $table = 'saved_templates';

    protected $fillable = [
        'student_id',
        'template_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
