<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Services\Auth\LoginService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class LoginServiceTest extends TestCase
{
    use RefreshDatabase;

    private LoginService $loginService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginService = new LoginService();
    }

    public function test_successful_login_clears_rate_limiter(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(false);
        RateLimiter::shouldReceive('clear')->once();

        $this->loginService->authenticate('test@example.com', 'password', false, '127.0.0.1');

        $this->assertAuthenticatedAs($user);
    }

    public function test_failed_login_hits_rate_limiter(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(false);
        RateLimiter::shouldReceive('hit')->once();

        $this->expectException(ValidationException::class);
        $this->loginService->authenticate('test@example.com', 'wrongpassword', false, '127.0.0.1');

        $this->assertGuest();
    }

    public function test_rate_limited_login_fires_lockout_event(): void
    {
        Event::fake([Lockout::class]);
        RateLimiter::shouldReceive('tooManyAttempts')->once()->andReturn(true);
        RateLimiter::shouldReceive('availableIn')->once()->andReturn(60);

        $this->expectException(ValidationException::class);

        $this->loginService->authenticate('test@example.com', 'password', false, '127.0.0.1');

        Event::assertDispatched(Lockout::class);
    }
}
