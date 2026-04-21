<?php

namespace Tests\Feature\Api;

use App\Models\File as FileModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_files(): void
    {
        $user = User::factory()->create();
        FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        
        $response = $this->actingAs($user)->getJson('/api/files');
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }

    public function test_can_upload_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('photo.jpg');

        $response = $this->actingAs($user)->postJson('/api/files', [
            'file' => $file,
            'comment' => 'My photo'
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'photo.jpg');
    }

    public function test_can_view_file_details_and_increments_views(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1', 'view_count' => 0]);

        $response = $this->actingAs($user)->getJson('/api/files/' . $file->id);
        $response->assertStatus(200);
        $this->assertEquals(1, $file->refresh()->view_count);
    }
    
    public function test_cannot_view_others_files(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $file = FileModel::create(['user_id' => $user1->id, 'name' => 'f1', 'path' => 'p1']);

        $response = $this->actingAs($user2)->getJson('/api/files/' . $file->id);
        $response->assertStatus(403);
    }

    public function test_can_delete_file(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        $file = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);

        $response = $this->actingAs($user)->deleteJson('/api/files/' . $file->id);
        $response->assertStatus(200);
        $this->assertSoftDeleted($file);
    }
}
