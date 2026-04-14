<?php

namespace App\Services\Files;

use App\Models\File;
use App\Models\ShareLink;
use Illuminate\Support\Str;

class LinkGeneratorService
{
    /**
     * Generate a new share link for a given file.
     * 
     * @param File $file
     * @param string $type ('public' or 'one-time')
     * @return ShareLink
     */
    public function generate(File $file, string $type): ShareLink
    {
        return $file->shareLinks()->create([
            'token' => Str::uuid()->toString(),
            'type' => $type,
            'views' => 0,
        ]);
    }
}
