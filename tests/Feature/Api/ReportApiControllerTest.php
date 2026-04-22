<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_reports(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->getJson('/api/reports');

        $response->assertStatus(200)
                 ->assertJsonStructure(['total_files', 'deleted_files', 'links_stats', 'top_links']);
    }
}
