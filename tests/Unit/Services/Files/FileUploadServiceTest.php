<?php

namespace Tests\Unit\Services\Files;

use App\Models\File;
use App\Models\User;
use App\Services\Files\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use DateTime;

class FileUploadServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_upload_stores_file_and_creates_record(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $uploadedFile = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $service = new FileUploadService();

        $expiresAt = new DateTime('+1 day');

        $fileModel = $service->upload($uploadedFile, $user->id, 'Important doc', $expiresAt);

        $this->assertInstanceOf(File::class, $fileModel);
        $this->assertEquals('document.pdf', $fileModel->name);
        $this->assertEquals('Important doc', $fileModel->comment);
        $this->assertEquals($user->id, $fileModel->user_id);

        Storage::disk('local')->assertExists($fileModel->path);

        $this->assertDatabaseHas('files', [
            'id' => $fileModel->id,
            'name' => 'document.pdf'
        ]);
    }
}
