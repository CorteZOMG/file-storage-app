<?php

namespace App\Http\Controllers;

use App\Contracts\Reports\ReportServiceInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly ReportServiceInterface $reportService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('reports.index', [
            'totalFiles' => $this->reportService->getTotalFilesCount($user),
            'deletedFiles' => $this->reportService->getDeletedFilesCount($user),
            'linksStats' => $this->reportService->getLinksStats($user),
            'topLinks' => $this->reportService->getTopViewedLinks($user),
        ]);
    }
}
