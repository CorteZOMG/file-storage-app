<?php

namespace Tests\Unit\Services\Files;

use App\Models\File as FileModel;
use App\Models\User;
use App\Services\Files\FileDeleteService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileDeleteServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_delete_removes_file_from_storage_and_soft_deletes_model(): void
    {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/test.jpg', 'contents');

        $user = User::factory()->create();
        $file = FileModel::create([
            'user_id' => $user->id,
            'name' => 'test.jpg',
            'path' => 'uploads/test.jpg',
        ]);

        $service = new FileDeleteService();
        $service->delete($file);

        Storage::disk('local')->assertMissing('uploads/test.jpg');
        $this->assertSoftDeleted($file);
    }
}
