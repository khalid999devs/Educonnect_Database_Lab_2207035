<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract class ApiController extends Controller
{
    protected function runOracleOperation(callable $operation, string $message, int $status = 200): JsonResponse
    {
        try {
            return ApiResponse::success($message, $operation(), $status);
        } catch (Throwable $exception) {
            $oracleMessage = $this->oracleMessage($exception);

            return ApiResponse::error(
                $oracleMessage ?? 'Oracle database operation failed',
                null,
                $this->isConflict($exception) ? 409 : 422,
            );
        }
    }

    private function oracleMessage(Throwable $exception): ?string
    {
        if (preg_match('/ORA-\d+:\s*([^\r\n]+)/', $exception->getMessage(), $matches) !== 1) {
            return null;
        }

        return trim($matches[1]);
    }

    private function isConflict(Throwable $exception): bool
    {
        return preg_match('/ORA-(20004|20014|20025|20043|20053)/', $exception->getMessage()) === 1;
    }
}
