<?php

namespace Tests\Unit;

use App\Providers\AppServiceProvider;
use Tests\TestCase;

class AppServiceProviderTest extends TestCase
{
    public function test_tns_admin_configuration_is_exported_for_oci(): void
    {
        $originalTnsAdmin = getenv('TNS_ADMIN');

        try {
            config(['oracle.tns_admin' => '/tmp/educonnect-test-wallet']);

            (new AppServiceProvider($this->app))->register();

            $this->assertSame('/tmp/educonnect-test-wallet', getenv('TNS_ADMIN'));
        } finally {
            $originalTnsAdmin === false
                ? putenv('TNS_ADMIN')
                : putenv("TNS_ADMIN={$originalTnsAdmin}");
        }
    }
}
