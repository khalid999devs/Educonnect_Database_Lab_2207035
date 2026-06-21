<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExtractedDocumentData extends OracleModel
{
    protected $table = 'extracted_document_data';

    protected $fillable = [
        'document_id',
        'student_id',
        'data_type',
        'data_key',
        'data_value',
        'confidence_score',
    ];

    protected function casts(): array
    {
        return [
            'confidence_score' => 'decimal:2',
        ];
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(AcademicDocument::class, 'document_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
