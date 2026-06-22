<?php

namespace Tests\Unit;

use App\Http\Controllers\Api\AuthController;
use App\Models\User;
use Illuminate\Http\Request;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function test_current_user_response_loads_student_context(): void
    {
        $relations = [
            'student.university',
            'student.academicField',
            'student.preferences',
        ];

        $user = Mockery::mock(User::class);
        $user->shouldReceive('load')->once()->with($relations)->andReturnSelf();
        $user->shouldReceive('jsonSerialize')->once()->andReturn(['id' => 1]);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('user')->twice()->andReturn($user);

        $response = (new AuthController)->me($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Authenticated user retrieved', $response->getData(true)['message']);
    }
}
