<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedResource extends OracleModel
{
    public const UPDATED_AT = null;

    protected $table = 'saved_resources';

    protected $fillable = [
        'student_id',
        'resource_id',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }
}
