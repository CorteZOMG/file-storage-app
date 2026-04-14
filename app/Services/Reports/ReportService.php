<?php

namespace App\Services\Reports;

use App\Contracts\Reports\ReportServiceInterface;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService implements ReportServiceInterface
{
    public function getTotalFilesCount(User $user): int
    {
        return $user->files()->count();
    }

    public function getDeletedFilesCount(User $user): int
    {
        return $user->files()->onlyTrashed()->count();
    }

    public function getLinksStats(User $user): array
    {
        $stats = ShareLink::whereHas('file', function ($query) use ($user) {
            $query->where('user_id', $user->id)->withTrashed();
        })
            ->select('type', DB::raw('count(*) as total'), DB::raw('sum(views) as total_views'))
            ->groupBy('type')
            ->get();

        return [
            'public' => [
                'count' => (int) ($stats->where('type', 'public')->first()->total ?? 0),
                'views' => (int) ($stats->where('type', 'public')->first()->total_views ?? 0),
            ],
            'one_time' => [
                'count' => (int) ($stats->where('type', 'one-time')->first()->total ?? 0),
                'views' => (int) ($stats->where('type', 'one-time')->first()->total_views ?? 0),
            ]
        ];
    }

    public function getTopViewedLinks(User $user, int $limit = 5): Collection
    {
        return ShareLink::with('file')->whereHas('file', function ($query) use ($user) {
            $query->where('user_id', $user->id)->withTrashed();
        })
            ->orderByDesc('views')
            ->limit($limit)
            ->get();
    }
}
