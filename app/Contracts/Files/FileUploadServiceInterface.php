<?php

namespace App\Contracts\Files;

use App\Models\File;
use DateTime;
use Illuminate\Http\UploadedFile;

interface FileUploadServiceInterface
{
    public function upload(UploadedFile $file, int $userId, ?string $comment, ?DateTime $expires_at): File;
}
