<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchCollection extends OracleModel
{
    protected $table = 'research_collections';

    protected $fillable = [
        'research_topic_id',
        'title',
        'collection_type',
        'resource_url',
        'summary',
        'keywords',
        'reading_status',
    ];

    public function researchTopic(): BelongsTo
    {
        return $this->belongsTo(ResearchTopic::class);
    }
}
