<?php

namespace App\Services\Files;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileDeleteService
{
    public function delete(File $file)
    {
        if (Storage::exists($file->path)) {
            Storage::delete($file->path);
        }

        $file->delete();
    }
}
