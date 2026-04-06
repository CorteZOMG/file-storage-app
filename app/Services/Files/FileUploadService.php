<?php

namespace App\Services\Files;

use DateTime;
use Illuminate\Http\UploadedFile;
use App\Models\File;

class FileUploadService
{

    public function upload(UploadedFile $file, int $userId, ?string $comment, ?DateTime $expires_at): File
    {
        $path = $file->store('uploads');

        return File::create([
            'user_id' => $userId,
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'comment' => $comment,
            'expires_at' => $expires_at
        ]);
    }

}