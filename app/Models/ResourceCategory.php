<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceCategory extends OracleModel
{
    protected $table = 'resource_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
