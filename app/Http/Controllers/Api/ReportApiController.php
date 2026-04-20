<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Reports\ReportServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportApiController extends Controller
{
    public function __construct(private readonly ReportServiceInterface $reportService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'total_files' => $this->reportService->getTotalFilesCount($user),
            'deleted_files' => $this->reportService->getDeletedFilesCount($user),
            'links_stats' => $this->reportService->getLinksStats($user),
            'top_links' => $this->reportService->getTopViewedLinks($user),
        ]);
    }
}
