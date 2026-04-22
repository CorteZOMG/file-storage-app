<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserJourneyFlowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Successful Flow: Register -> Upload -> Share a file
     */
    public function test_successful_complete_user_journey_flow(): void
    {
        Storage::fake('local');

        // 1. User registers and receives their token
        $response = $this->postJson('/api/register', [
            'name' => 'Flow User',
            'email' => 'flow@example.com',
            'password' => 'password123',
        ]);
        $response->assertStatus(201);
        $token = $response->json('access_token');

        // 2. User uploads a file uniquely using that token
        $file = UploadedFile::fake()->image('flow_test.jpg');
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson('/api/files', [
                'file' => $file,
                'comment' => 'My flow file'
            ]);
        $response->assertStatus(201);
        $fileId = $response->json('data.id');

        // 3. User generates a public share link for that specific file
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->postJson("/api/files/{$fileId}/links", [
                'type' => 'public'
            ]);
        $response->assertStatus(201);

        $linkUrl = $response->json('link_url');
        $tokenParts = explode('/', (string) $linkUrl);
        $shareToken = end($tokenParts);

        // 4. An unauthenticated guest accesses that link over the web
        $guestResponse = $this->getJson("/api/share/{$shareToken}");
        $guestResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'flow_test.jpg');

        // 5. The original User checks their account reports and sees exactly 1 file counted
        $response = $this->withHeader('Authorization', "Bearer $token")
            ->getJson('/api/reports');
        $response->assertStatus(200)
            ->assertJsonPath('total_files', 1);
    }

    /**
     * Unsuccessful Flow: Interception attempting to modify private resources.
     */
    public function test_unsuccessful_hacker_interaction_flow(): void
    {
        Storage::fake('local');

        // Alice creates an account and safely uploads a file
        $aliceResponse = $this->postJson('/api/register', [
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);
        $aliceToken = $aliceResponse->json('access_token');

        Auth::guard('web')->logout();
        Auth::forgetGuards();

        $file = UploadedFile::fake()->image('private.jpg');
        $uploadResponse = $this->withToken($aliceToken)->postJson('/api/files', ['file' => $file]);
        $fileId = $uploadResponse->json('data.id');

        // 1. Bob creates his own account
        $bobToken = $this->postJson('/api/register', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ])->json('access_token');

        Auth::guard('web')->logout();
        Auth::forgetGuards(); // Prevent Bob's web guard session from bleeding <- fix from AI agent

        // 2. Bob is malicious and tries to delete Alice's file via a guessed ID
        $response = $this->withToken($bobToken)->deleteJson("/api/files/{$fileId}");

        $response->assertStatus(403);

        // 3. Bob tries to generate a public link to expose Alice's file
        $response = $this->withToken($bobToken)->postJson("/api/files/{$fileId}/links", ['type' => 'public']);
        $response->assertStatus(403);

        // 4. Alice asserts her file is still perfectly confidential
        Auth::forgetGuards();
        $response = $this->withToken($aliceToken)->getJson("/api/files/{$fileId}");
        $response->assertStatus(200);
    }

    /**
     * Garbage Collection Flow: Ensuring soft deletes correctly aggregate in reports
     */
    public function test_garbage_collector_flow(): void
    {
        Storage::fake('local');

        $token = $this->postJson('/api/register', [
            'name' => 'Garbage Collector',
            'email' => 'gc@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ])->json('access_token');
        Auth::forgetGuards();

        // 1. Upload 3 files
        $fileIds = [];
        for ($i = 1; $i <= 3; $i++) {
            $file = UploadedFile::fake()->image("doc{$i}.jpg");
            $response = $this->withToken($token)->postJson('/api/files', ['file' => $file]);
            $fileIds[] = $response->json('data.id');
        }

        // 2. Delete 2 of them
        $this->withToken($token)->deleteJson("/api/files/{$fileIds[0]}");
        $this->withToken($token)->deleteJson("/api/files/{$fileIds[1]}");

        // 3. Check reports
        $response = $this->withToken($token)->getJson('/api/reports');
        $response->assertStatus(200)
            ->assertJsonPath('total_files', 1) // Only 1 fully active
            ->assertJsonPath('deleted_files', 2); // 2 properly soft-deleted
    }

    /**
     * One-time links Flow: Self-destructing one-time links
     */
    public function test_mission_impossible_one_time_link_flow(): void
    {
        Storage::fake('local');

        $token = $this->postJson('/api/register', [
            'name' => 'Hunt',
            'email' => 'ethan@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ])->json('access_token');
        Auth::forgetGuards();

        $file = UploadedFile::fake()->image('secret.jpg');
        $fileId = $this->withToken($token)->postJson('/api/files', ['file' => $file])->json('data.id');

        // Generate one-time link
        $response = $this->withToken($token)->postJson("/api/files/{$fileId}/links", ['type' => 'one-time']);
        $linkUrl = $response->json('link_url');
        $tokenParts = explode('/', (string) $linkUrl);
        $shareToken = end($tokenParts);

        // 1st View - Success
        $guestResponse1 = $this->getJson("/api/share/{$shareToken}");
        $guestResponse1->assertStatus(200);

        // 2nd View - Self Destructed (404)
        $guestResponse2 = $this->getJson("/api/share/{$shareToken}");
        $guestResponse2->assertStatus(404);
    }
}
