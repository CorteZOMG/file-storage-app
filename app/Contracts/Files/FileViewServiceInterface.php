<?php

namespace App\Contracts\Files;

use App\Models\File;

interface FileViewServiceInterface
{
    public function incrementViewCount(File $file): void;
}
