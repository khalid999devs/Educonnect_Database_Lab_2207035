<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicDocument extends OracleModel
{
    protected $table = 'academic_documents';

    protected $fillable = [
        'student_id',
        'title',
        'document_type',
        'file_name',
        'file_path',
        'file_mime_type',
        'status',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function extractedData(): HasMany
    {
        return $this->hasMany(ExtractedDocumentData::class, 'document_id');
    }
}
