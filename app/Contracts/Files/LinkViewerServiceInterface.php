<?php

namespace App\Contracts\Files;

use App\Models\ShareLink;

interface LinkViewerServiceInterface
{
    public function recordView(ShareLink $link): void;
}
