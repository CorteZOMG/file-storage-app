<?php

namespace Tests\Unit\Models;

use App\Models\File;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareLinkTest extends TestCase
{
    use RefreshDatabase;

    private File $file;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->file = File::create([
            'user_id' => $user->id,
            'name' => 'document.jpg',
            'path' => 'files/document.jpg',
        ]);
    }

    public function test_share_link_belongs_to_file(): void
    {
        $link = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 'random_token',
            'type' => 'public',
        ]);

        $this->assertInstanceOf(File::class, $link->file);
        $this->assertEquals($this->file->id, $link->file->id);
    }

    public function test_link_types(): void
    {
        $oneTime = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 't1',
            'type' => 'one-time',
        ]);
        $this->assertTrue($oneTime->isOneTime());
        $this->assertFalse($oneTime->isPublic());

        $publicLink = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 't2',
            'type' => 'public',
        ]);
        $this->assertTrue($publicLink->isPublic());
        $this->assertFalse($publicLink->isOneTime());
    }

    public function test_is_valid(): void
    {
        // Public link always valid as long as file exists
        $publicLink = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 't3',
            'type' => 'public',
            'views' => 10,
        ]);
        $this->assertTrue($publicLink->isValid());

        // One-time link unviewed
        $oneTimeUnviewed = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 't4',
            'type' => 'one-time',
            'views' => 0,
        ]);
        $this->assertTrue($oneTimeUnviewed->isValid());

        // One-time link viewed
        $oneTimeViewed = ShareLink::create([
            'file_id' => $this->file->id,
            'token' => 't5',
            'type' => 'one-time',
            'views' => 1,
        ]);
        $this->assertFalse($oneTimeViewed->isValid());

        // Orphaned link
        $orphanedLink = new ShareLink([
            'file_id' => 999999,
            'token' => 't6',
            'type' => 'public',
        ]);
        $this->assertFalse($orphanedLink->isValid());
    }
}
