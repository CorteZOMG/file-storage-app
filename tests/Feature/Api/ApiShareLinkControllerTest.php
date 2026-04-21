<?php

namespace Tests\Feature\Api;

use App\Models\File as FileModel;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiShareLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_link(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);

        $response = $this->actingAs($user)->postJson('/api/files/' . $file->id . '/links', [
            'type' => 'public'
        ]);

        $response->assertStatus(201)->assertJsonPath('type', 'public');
        $this->assertDatabaseHas('share_links', ['file_id' => $file->id, 'type' => 'public']);
    }

    public function test_can_access_shared_link(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        $link = ShareLink::create(['file_id' => $file->id, 'token' => 'tok123', 'type' => 'public', 'views' => 0]);

        $response = $this->getJson('/api/share/tok123');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => ['id', 'name'], 'image_url']);
                 
        $this->assertEquals(1, $link->refresh()->views);
    }
    
    public function test_expired_or_invalid_link_returns_404(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        $link = ShareLink::create(['file_id' => $file->id, 'token' => 'tok123', 'type' => 'one-time', 'views' => 1]); // already viewed

        $response = $this->getJson('/api/share/tok123');
        $response->assertStatus(404);
    }
}
