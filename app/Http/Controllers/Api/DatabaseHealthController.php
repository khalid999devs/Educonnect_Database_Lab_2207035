<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;
use Yajra\Oci8\Oci8Connection;

class DatabaseHealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $tnsAdmin = (string) config('oracle.tns_admin');

        $status = [
            'default_connection' => config('database.default'),
            'oracle_connection_configured' => array_key_exists('oracle', config('database.connections')),
            'laravel_oci8_installed' => class_exists(Oci8Connection::class),
            'oci8_extension_loaded' => extension_loaded('oci8'),
            'tns_alias_configured' => filled(config('database.connections.oracle.tns')),
            'oracle_username_configured' => filled(config('database.connections.oracle.username')),
            'oracle_password_configured' => filled(config('database.connections.oracle.password')),
            'tns_admin_configured' => filled($tnsAdmin),
            'wallet_tnsnames_present' => filled($tnsAdmin)
                && file_exists(rtrim($tnsAdmin, '/').'/tnsnames.ora'),
        ];

        if (! $status['oci8_extension_loaded']) {
            return ApiResponse::error('Oracle database connection is not ready', [
                ...$status,
                'detail' => 'PHP OCI8 extension is not installed or enabled.',
            ], 503);
        }

        if (
            ! $status['tns_alias_configured'] ||
            ! $status['oracle_username_configured'] ||
            ! $status['oracle_password_configured'] ||
            ! $status['tns_admin_configured'] ||
            ! $status['wallet_tnsnames_present']
        ) {
            return ApiResponse::error('Oracle database connection is not ready', [
                ...$status,
                'detail' => 'Oracle credentials or wallet files are not fully configured.',
            ], 503);
        }

        try {
            $result = DB::connection('oracle')->select('select sysdate from dual');

            return ApiResponse::success('Oracle database connection is healthy', [
                ...$status,
                'database_time' => $result[0]->sysdate ?? $result[0]->SYSDATE ?? null,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return ApiResponse::error('Oracle database connection is not ready', [
                ...$status,
                'detail' => 'Oracle connectivity check failed.',
            ], 503);
        }
    }
}
