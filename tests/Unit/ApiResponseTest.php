<?php

namespace Tests\Unit;

use App\Support\ApiResponse;
use Tests\TestCase;

class ApiResponseTest extends TestCase
{
    public function test_success_response_uses_standard_shape(): void
    {
        $response = ApiResponse::success('Ready', ['status' => 'ok']);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([
            'success' => true,
            'message' => 'Ready',
            'data' => ['status' => 'ok'],
        ], $response->getData(true));
    }
}
