<?php

namespace App\Contracts\Files;

use App\Models\File;

interface FileDeleteServiceInterface
{
    public function delete(File $file): void;
}
