<?php

namespace App\Services\Files;

use App\Models\File;
use App\Models\ShareLink;
use Illuminate\Support\Str;

use App\Contracts\Files\LinkGeneratorServiceInterface;

class LinkGeneratorService implements LinkGeneratorServiceInterface
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
        /** @var ShareLink $link */
        $link = $file->shareLinks()->create([
            'token' => Str::uuid()->toString(),
            'type' => $type,
            'views' => 0,
        ]);

        return $link;
    }
}
