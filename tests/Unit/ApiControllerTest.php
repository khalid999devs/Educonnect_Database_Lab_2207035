<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DashboardController;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_oracle_duplicate_errors_are_returned_as_conflicts(): void
    {
        $controller = $this->controllerHarness();
        $response = $controller->execute(function (): void {
            throw new RuntimeException('ORA-20004: Resource is already saved by this student.');
        });

        $this->assertSame(409, $response->getStatusCode());
        $this->assertSame([
            'success' => false,
            'message' => 'Resource is already saved by this student.',
            'errors' => [],
        ], $response->getData(true));
    }

    public function test_other_oracle_business_errors_are_unprocessable(): void
    {
        $controller = $this->controllerHarness();
        $response = $controller->execute(function (): void {
            throw new RuntimeException('ORA-20001: Student does not exist.');
        });

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame('Student does not exist.', $response->getData(true)['message']);
    }

    public function test_dashboard_controller_delegates_to_dashboard_service(): void
    {
        $service = Mockery::mock(DashboardService::class);
        $service->shouldReceive('getForStudent')->once()->with(3)->andReturn([
            'profile_completion' => 100,
        ]);

        $response = (new DashboardController)->show(3, $service);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(100, $response->getData(true)['data']['profile_completion']);
    }

    private function controllerHarness(): object
    {
        return new class extends ApiController
        {
            public function execute(callable $operation): JsonResponse
            {
                return $this->runOracleOperation($operation, 'Completed');
            }
        };
    }
}
