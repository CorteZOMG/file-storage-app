<?php

namespace Tests\Unit\Services\Files;

use App\Models\File as FileModel;
use App\Models\User;
use App\Services\Files\LinkGeneratorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkGeneratorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_generates_share_link(): void
    {
        $user = User::factory()->create();
        $file = FileModel::create([
            'user_id' => $user->id,
            'name' => 'test.jpg',
            'path' => 'uploads/test.jpg',
        ]);

        $service = new LinkGeneratorService();
        $link = $service->generate($file, 'public');

        $this->assertEquals('public', $link->type);
        $this->assertNotNull($link->token);
        $this->assertEquals(0, $link->views);
        $this->assertEquals($file->id, $link->file_id);
    }
}
