<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexQueryValidationTest extends TestCase
{
    public function test_catalogue_page_size_is_capped_before_query_execution(): void
    {
        $this->getJson('/api/v1/resources?per_page=100')
            ->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonPath('message', 'Validation failed')
            ->assertJsonValidationErrors(['per_page'], 'errors');
    }
}
