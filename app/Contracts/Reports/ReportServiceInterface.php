<?php

namespace App\Contracts\Reports;

use App\Models\User;
use Illuminate\Support\Collection;

interface ReportServiceInterface
{
    public function getTotalFilesCount(User $user): int;

    public function getDeletedFilesCount(User $user): int;

    /**
     * @return array<string, array<string, int>>
     */
    public function getLinksStats(User $user): array;

    /**
     * @return Collection<int, \App\Models\ShareLink>
     */
    public function getTopViewedLinks(User $user, int $limit = 5): Collection;
}
