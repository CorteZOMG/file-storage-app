<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Files\FileDeleteServiceInterface;
use App\Contracts\Files\FileUploadServiceInterface;
use App\Contracts\Files\FileViewServiceInterface;
use App\Contracts\Files\LinkGeneratorServiceInterface;
use App\Contracts\Files\LinkViewerServiceInterface;
use App\Services\Files\FileDeleteService;
use App\Services\Files\FileUploadService;
use App\Services\Files\FileViewService;
use App\Services\Files\LinkGeneratorService;
use App\Services\Files\LinkViewerService;
use App\Contracts\Reports\ReportServiceInterface;
use App\Services\Reports\ReportService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileUploadServiceInterface::class, FileUploadService::class);
        $this->app->bind(FileDeleteServiceInterface::class, FileDeleteService::class);
        $this->app->bind(FileViewServiceInterface::class, FileViewService::class);
        $this->app->bind(LinkGeneratorServiceInterface::class, LinkGeneratorService::class);
        $this->app->bind(LinkViewerServiceInterface::class, LinkViewerService::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Laravel\Sanctum\Sanctum::getAccessTokenFromRequestUsing(
            function (\Illuminate\Http\Request $request) {
                return $request->bearerToken() ?: $request->query('token');
            }
        );
    }
}
