<?php

namespace App\Services\Files;

use App\Models\ShareLink;

use App\Contracts\Files\LinkViewerServiceInterface;

class LinkViewerService implements LinkViewerServiceInterface
{
    /**
     * Record a view for a given share link safely.
     *
     * @param ShareLink $link
     * @return void
     */
    public function recordView(ShareLink $link): void
    {
        $link->increment('views');
    }
}
