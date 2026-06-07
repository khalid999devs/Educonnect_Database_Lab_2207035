<?php

namespace Tests\Feature;

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
            ->assertJsonPath('errors.oracle_password_configured', false);
    }
}
