<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OAT;
use App\Contracts\Reports\ReportServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

#[OAT\Tag(name: "Reports", description: "User file statistics")]
class ReportApiController extends Controller
{
    public function __construct(private readonly ReportServiceInterface $reportService)
    {
    }

    #[OAT\Get(path: "/reports", summary: "Get user file statistics", tags: ["Reports"], security: [["bearerAuth" => []]])]
    #[OAT\Response(response: 200, description: "Report data")]
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
