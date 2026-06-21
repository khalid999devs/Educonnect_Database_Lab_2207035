<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends OracleModel
{
    protected $table = 'reviews';

    protected $fillable = [
        'student_id',
        'reviewable_type',
        'reviewable_id',
        'rating',
        'comment_text',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'reviewable_type', 'reviewable_id');
    }
}
