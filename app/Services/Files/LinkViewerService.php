<?php

namespace App\Services\Files;

use App\Models\ShareLink;

class LinkViewerService
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
