<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class DatabaseHealthTest extends TestCase
{
    public function test_database_health_endpoint_returns_standard_error_json_when_config_is_incomplete(): void
    {
        config([
            'database.default' => 'oracle',
            'database.connections.oracle.tns' => 'educonnectdb_low',
            'database.connections.oracle.username' => 'educonnect',
            'database.connections.oracle.password' => '',
            'oracle.tns_admin' => '',
        ]);

        $response = $this->getJson('/api/v1/health/database');

        $response
            ->assertStatus(503)
            ->assertJson([
                'success' => false,
                'message' => 'Oracle database connection is not ready',
            ])
            ->assertJsonPath('errors.oracle_connection_configured', true)
            ->assertJsonPath('errors.oci8_extension_loaded', true)
            ->assertJsonPath('errors.oracle_password_configured', false)
            ->assertJsonPath('errors.tns_admin_configured', false);
    }

    public function test_database_health_endpoint_hides_low_level_connection_errors(): void
    {
        $walletPath = storage_path('framework/testing/oracle-wallet');

        if (! is_dir($walletPath)) {
            mkdir($walletPath, 0777, true);
        }

        file_put_contents($walletPath.'/tnsnames.ora', 'EDUCONNECTDB_LOW = placeholder');

        try {
            config([
                'database.default' => 'oracle',
                'database.connections.oracle.tns' => 'educonnectdb_low',
                'database.connections.oracle.username' => 'educonnect',
                'database.connections.oracle.password' => 'configured',
                'oracle.tns_admin' => $walletPath,
            ]);

            DB::shouldReceive('connection')
                ->once()
                ->with('oracle')
                ->andThrow(new RuntimeException('ORA-12154: private connection details'));

            $this->getJson('/api/v1/health/database')
                ->assertStatus(503)
                ->assertJsonPath('errors.detail', 'Oracle connectivity check failed.')
                ->assertDontSee('private connection details');
        } finally {
            @unlink($walletPath.'/tnsnames.ora');
            @rmdir($walletPath);
        }
    }
}
