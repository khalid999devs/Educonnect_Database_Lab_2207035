<?php

namespace App\Services;

use Illuminate\Database\DatabaseManager;
use LogicException;
use PDO;
use RuntimeException;
use Yajra\Oci8\Oci8Connection;

class OracleProcedureService
{
    public function __construct(private readonly DatabaseManager $database) {}

    public function saveResource(int $studentId, int $resourceId): void
    {
        $this->executeProcedure('proc_save_resource', [
            'p_student_id' => $studentId,
            'p_resource_id' => $resourceId,
        ]);
    }

    public function saveTemplate(int $studentId, int $templateId): void
    {
        $this->executeProcedure('proc_save_template', [
            'p_student_id' => $studentId,
            'p_template_id' => $templateId,
        ]);
    }

    public function purchaseTemplate(int $studentId, int $templateId): void
    {
        $this->executeProcedure('proc_purchase_template', [
            'p_student_id' => $studentId,
            'p_template_id' => $templateId,
        ]);
    }

    /**
     * @param  array{
     *     document_id: int,
     *     student_id: int,
     *     data_type: string,
     *     data_key: string,
     *     data_value: ?string,
     *     confidence_score: int|float|string
     * }  $payload
     */
    public function addExtractedData(array $payload): void
    {
        $this->executeProcedure('proc_add_extracted_data', [
            'p_document_id' => $payload['document_id'],
            'p_student_id' => $payload['student_id'],
            'p_data_type' => $payload['data_type'],
            'p_data_key' => $payload['data_key'],
            'p_data_value' => [
                'value' => $payload['data_value'],
                'type' => PDO::PARAM_LOB,
            ],
            'p_confidence_score' => $payload['confidence_score'],
        ]);
    }

    public function approveResource(int $resourceId, int $adminUserId): void
    {
        $this->executeProcedure('proc_approve_resource', [
            'p_resource_id' => $resourceId,
            'p_admin_user_id' => $adminUserId,
        ]);
    }

    public function approveTemplate(int $templateId, int $adminUserId): void
    {
        $this->executeProcedure('proc_approve_template', [
            'p_template_id' => $templateId,
            'p_admin_user_id' => $adminUserId,
        ]);
    }

    public function getProfileCompletion(int $studentId): int
    {
        return (int) round($this->executeFunction('fn_profile_completion', [
            'p_student_id' => $studentId,
        ]));
    }

    public function getRecommendationScore(int $studentId, int $resourceId): int
    {
        return (int) round($this->executeFunction('fn_recommendation_score', [
            'p_student_id' => $studentId,
            'p_resource_id' => $resourceId,
        ]));
    }

    private function executeProcedure(string $name, array $bindings): void
    {
        if (! $this->connection()->executeProcedure($name, $bindings)) {
            throw new RuntimeException("Oracle procedure {$name} did not complete successfully.");
        }
    }

    private function executeFunction(string $name, array $bindings): float
    {
        return (float) $this->connection()->executeFunction(
            $name,
            $bindings,
            PDO::PARAM_STR,
            100,
        );
    }

    private function connection(): Oci8Connection
    {
        $connection = $this->database->connection('oracle');

        if (! $connection instanceof Oci8Connection) {
            throw new LogicException('The configured oracle connection must use Laravel-OCI8.');
        }

        return $connection;
    }
}
