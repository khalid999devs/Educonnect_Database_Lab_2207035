<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends OracleModel
{
    protected $table = 'universities';

    protected $fillable = [
        'name',
        'country',
        'city',
        'website_url',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }
}
