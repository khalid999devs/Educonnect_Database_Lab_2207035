<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ToolCategory extends OracleModel
{
    protected $table = 'tool_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function tools(): HasMany
    {
        return $this->hasMany(Tool::class);
    }
}
