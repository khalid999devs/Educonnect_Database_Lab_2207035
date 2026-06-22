<?php

namespace App\Providers;

use App\Models\Resource;
use App\Models\Template;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $tnsAdmin = (string) config('oracle.tns_admin');

        if ($tnsAdmin !== '') {
            putenv("TNS_ADMIN={$tnsAdmin}");
        }
    }

    public function boot(): void
    {
        Relation::enforceMorphMap([
            'RESOURCE' => Resource::class,
            'TEMPLATE' => Template::class,
            'TOOL' => Tool::class,
        ]);
    }
}
