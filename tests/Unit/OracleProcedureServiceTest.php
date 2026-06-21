<?php

namespace Tests\Unit;

use App\Services\OracleProcedureService;
use Illuminate\Database\DatabaseManager;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PDO;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yajra\Oci8\Oci8Connection;

class OracleProcedureServiceTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_procedure_methods_use_expected_names_and_bindings(): void
    {
        $database = Mockery::mock(DatabaseManager::class);
        $connection = Mockery::mock(Oci8Connection::class);
        $database->shouldReceive('connection')->with('oracle')->times(6)->andReturn($connection);

        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_save_resource', [
                'p_student_id' => 1,
                'p_resource_id' => 2,
            ])
            ->andReturnTrue();
        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_save_template', [
                'p_student_id' => 1,
                'p_template_id' => 3,
            ])
            ->andReturnTrue();
        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_purchase_template', [
                'p_student_id' => 1,
                'p_template_id' => 4,
            ])
            ->andReturnTrue();
        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_add_extracted_data', [
                'p_document_id' => 5,
                'p_student_id' => 1,
                'p_data_type' => 'TOPIC',
                'p_data_key' => 'database',
                'p_data_value' => [
                    'value' => 'Oracle transaction processing',
                    'type' => PDO::PARAM_LOB,
                ],
                'p_confidence_score' => 0.95,
            ])
            ->andReturnTrue();
        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_approve_resource', [
                'p_resource_id' => 2,
                'p_admin_user_id' => 6,
            ])
            ->andReturnTrue();
        $connection->shouldReceive('executeProcedure')
            ->once()
            ->with('proc_approve_template', [
                'p_template_id' => 3,
                'p_admin_user_id' => 6,
            ])
            ->andReturnTrue();

        $service = new OracleProcedureService($database);
        $service->saveResource(1, 2);
        $service->saveTemplate(1, 3);
        $service->purchaseTemplate(1, 4);
        $service->addExtractedData([
            'document_id' => 5,
            'student_id' => 1,
            'data_type' => 'TOPIC',
            'data_key' => 'database',
            'data_value' => 'Oracle transaction processing',
            'confidence_score' => 0.95,
        ]);
        $service->approveResource(2, 6);
        $service->approveTemplate(3, 6);
    }

    public function test_function_methods_return_rounded_integers(): void
    {
        $database = Mockery::mock(DatabaseManager::class);
        $connection = Mockery::mock(Oci8Connection::class);
        $database->shouldReceive('connection')->with('oracle')->twice()->andReturn($connection);

        $connection->shouldReceive('executeFunction')
            ->once()
            ->with('fn_profile_completion', ['p_student_id' => 1], PDO::PARAM_STR, 100)
            ->andReturn('87.5');
        $connection->shouldReceive('executeFunction')
            ->once()
            ->with('fn_recommendation_score', [
                'p_student_id' => 1,
                'p_resource_id' => 2,
            ], PDO::PARAM_STR, 100)
            ->andReturn('71');

        $service = new OracleProcedureService($database);

        $this->assertSame(88, $service->getProfileCompletion(1));
        $this->assertSame(71, $service->getRecommendationScore(1, 2));
    }

    public function test_failed_procedure_execution_throws_an_exception(): void
    {
        $database = Mockery::mock(DatabaseManager::class);
        $connection = Mockery::mock(Oci8Connection::class);
        $database->shouldReceive('connection')->with('oracle')->once()->andReturn($connection);
        $connection->shouldReceive('executeProcedure')->once()->andReturnFalse();

        $this->expectException(RuntimeException::class);

        (new OracleProcedureService($database))->saveResource(1, 2);
    }
}
