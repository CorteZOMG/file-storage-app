<?php

namespace Tests\Unit\Services\Files;

use App\Models\File as FileModel;
use App\Models\ShareLink;
use App\Models\User;
use App\Services\Files\LinkViewerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkViewerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_increments_views(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create([
            'user_id' => $user->id,
            'name' => 'test.jpg',
            'path' => 'uploads/test.jpg',
        ]);
        $link = ShareLink::create([
            'file_id' => $file->id,
            'token' => 't1',
            'type' => 'public',
            'views' => 0
        ]);

        $service = new LinkViewerService();
        $service->recordView($link);

        $this->assertEquals(1, $link->refresh()->views);
    }
}
