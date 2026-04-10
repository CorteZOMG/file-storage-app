<?php

namespace App\Services\Files;

use App\Models\File;

class FileViewService
{
    public function incrementViewCount(File $file)
    {
        $file->increment('view_count');
    }
}
