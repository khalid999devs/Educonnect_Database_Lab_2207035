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
            $oracleMessage = $this->oracleBusinessMessage($exception);

            if ($oracleMessage === null) {
                report($exception);

                return ApiResponse::error('Oracle database operation failed', null, 500);
            }

            return ApiResponse::error(
                $oracleMessage,
                null,
                $this->isConflict($exception) ? 409 : 422,
            );
        }
    }

    private function oracleBusinessMessage(Throwable $exception): ?string
    {
        if (preg_match('/ORA-20\d{3}:\s*([^\r\n]+)/', $exception->getMessage(), $matches) !== 1) {
            return null;
        }

        return trim($matches[1]);
    }

    private function isConflict(Throwable $exception): bool
    {
        return preg_match('/ORA-(20004|20014|20025|20043|20053)/', $exception->getMessage()) === 1;
    }
}
