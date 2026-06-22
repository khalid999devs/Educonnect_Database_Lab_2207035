<?php

namespace Tests\Unit;

use App\Http\Requests\Documents\DocumentIndexRequest;
use App\Http\Requests\Resources\ResourceIndexRequest;
use ReflectionMethod;
use Tests\TestCase;

class IndexQueryRequestTest extends TestCase
{
    public function test_resource_filters_are_normalized_for_oracle_values(): void
    {
        $request = ResourceIndexRequest::create('/resources', 'GET', [
            'search' => '  database  ',
            'difficulty_level' => 'intermediate',
            'status' => 'approved',
        ]);

        $this->prepare($request);

        $this->assertSame('database', $request->input('search'));
        $this->assertSame('INTERMEDIATE', $request->input('difficulty_level'));
        $this->assertSame('APPROVED', $request->input('status'));
    }

    public function test_document_request_exposes_student_and_pagination_filters(): void
    {
        $rules = (new DocumentIndexRequest)->rules();

        $this->assertArrayHasKey('student_id', $rules);
        $this->assertArrayHasKey('document_type', $rules);
        $this->assertArrayHasKey('status', $rules);
        $this->assertArrayHasKey('page', $rules);
        $this->assertArrayHasKey('per_page', $rules);
    }

    private function prepare(object $request): void
    {
        (new ReflectionMethod($request, 'prepareForValidation'))->invoke($request);
    }
}
