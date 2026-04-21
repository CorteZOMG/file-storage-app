<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Services\Auth\RegisterService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_fires_event(): void
    {
        Event::fake([Registered::class]);

        $service = new RegisterService();

        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'password' => 'secretpassword',
        ];

        $user = $service->register($data);

        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'name' => 'John Doe'
        ]);
        
        $this->assertTrue(Hash::check('secretpassword', $user->password));

        Event::assertDispatched(Registered::class, function ($event) use ($user) {
            return $event->user->id === $user->id;
        });
    }
}
