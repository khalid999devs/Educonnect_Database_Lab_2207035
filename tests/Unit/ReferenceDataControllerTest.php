<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\ReferenceDataController;
use App\Services\ReferenceDataService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class ReferenceDataControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_controller_returns_reference_data_from_service(): void
    {
        $data = [
            'universities' => [['id' => 1, 'name' => 'Example University']],
            'academic_fields' => [['id' => 2, 'name' => 'Computer Science']],
            'academic_tasks' => [],
            'resource_categories' => [],
            'tool_categories' => [],
            'template_categories' => [],
        ];

        $service = Mockery::mock(ReferenceDataService::class);
        $service->shouldReceive('getAll')->once()->andReturn($data);

        $response = (new ReferenceDataController)($service);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame($data, $response->getData(true)['data']);
    }
}
