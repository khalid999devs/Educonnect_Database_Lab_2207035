<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplatePurchase extends OracleModel
{
    public const UPDATED_AT = null;

    protected $table = 'template_purchases';

    protected $fillable = [
        'student_id',
        'template_id',
        'amount',
        'payment_status',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
