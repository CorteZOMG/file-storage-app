<?php

namespace App\Services\Files;

use App\Models\File;

use App\Contracts\Files\FileViewServiceInterface;

class FileViewService implements FileViewServiceInterface
{
    public function incrementViewCount(File $file): void
    {
        $file->increment('view_count');
    }
}
