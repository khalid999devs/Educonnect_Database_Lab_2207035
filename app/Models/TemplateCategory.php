<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends OracleModel
{
    protected $table = 'template_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }
}
