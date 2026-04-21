<?php

namespace Tests\Unit\Services\Reports;

use App\Models\File as FileModel;
use App\Models\ShareLink;
use App\Models\User;
use App\Services\Reports\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_total_files_count(): void
    {
        $user = User::factory()->create();
        FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        FileModel::create(['user_id' => $user->id, 'name' => 'f2', 'path' => 'p2']);

        $service = new ReportService();
        $this->assertEquals(2, $service->getTotalFilesCount($user));
    }

    public function test_get_deleted_files_count(): void
    {
        $user = User::factory()->create();
        $f1 = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        $f2 = FileModel::create(['user_id' => $user->id, 'name' => 'f2', 'path' => 'p2']);
        $f1->delete();

        $service = new ReportService();
        $this->assertEquals(1, $service->getDeletedFilesCount($user));
    }

    public function test_get_links_stats(): void
    {
        $user = User::factory()->create();
        $f = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        ShareLink::create(['file_id' => $f->id, 'token' => 't1', 'type' => 'public', 'views' => 10]);
        ShareLink::create(['file_id' => $f->id, 'token' => 't2', 'type' => 'public', 'views' => 20]);
        ShareLink::create(['file_id' => $f->id, 'token' => 't3', 'type' => 'one-time', 'views' => 1]);

        $service = new ReportService();
        $stats = $service->getLinksStats($user);

        $this->assertEquals(2, $stats['public']['count']);
        $this->assertEquals(30, $stats['public']['views']);
        $this->assertEquals(1, $stats['one_time']['count']);
        $this->assertEquals(1, $stats['one_time']['views']);
    }

    public function test_get_top_viewed_links(): void
    {
        $user = User::factory()->create();
        $f = FileModel::create(['user_id' => $user->id, 'name' => 'f1', 'path' => 'p1']);
        ShareLink::create(['file_id' => $f->id, 'token' => 't1', 'type' => 'public', 'views' => 10]);
        $topLink = ShareLink::create(['file_id' => $f->id, 'token' => 't2', 'type' => 'public', 'views' => 50]);
        ShareLink::create(['file_id' => $f->id, 'token' => 't3', 'type' => 'one-time', 'views' => 5]);

        $service = new ReportService();
        $links = $service->getTopViewedLinks($user, 2);

        $this->assertCount(2, $links);
        $this->assertEquals($topLink->id, $links->first()->id);
    }
}
