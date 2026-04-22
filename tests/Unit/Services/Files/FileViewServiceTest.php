<?php

namespace Tests\Unit\Services\Files;

use App\Models\File as FileModel;
use App\Models\User;
use App\Services\Files\FileViewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FileViewServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_increments_view_count(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create([
            'user_id' => $user->id,
            'name' => 'test.jpg',
            'path' => 'uploads/test.jpg',
            'view_count' => 0
        ]);

        $service = new FileViewService();
        $service->incrementViewCount($file);

        $this->assertEquals(1, $file->refresh()->view_count);
    }
}
