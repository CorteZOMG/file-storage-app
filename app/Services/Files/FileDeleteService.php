<?php

namespace App\Services\Files;

use App\Models\File;
use Illuminate\Support\Facades\Storage;
use App\Contracts\Files\FileDeleteServiceInterface;

class FileDeleteService implements FileDeleteServiceInterface
{
    public function delete(File $file): void
    {
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        $file->delete();
    }
}
