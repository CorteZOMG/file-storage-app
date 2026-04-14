<?php

namespace App\Contracts\Files;

use App\Models\File;
use App\Models\ShareLink;

interface LinkGeneratorServiceInterface
{
    public function generate(File $file, string $type): ShareLink;
}
