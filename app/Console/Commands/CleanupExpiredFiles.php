<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Services\Files\FileDeleteService;
use Illuminate\Console\Command;

class CleanupExpiredFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup files that have passed their expiration date';

    /**
     * Execute the console command.
     */
    public function handle(FileDeleteService $fileDeleteService)
    {
        $this->info('Starting expired files cleanup...');

        $expiredFiles = File::whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        if ($expiredFiles->isEmpty()) {
            $this->info('No expired files found.');
            return;
        }

        /** @var \App\Models\File $file */
        foreach ($expiredFiles as $file) {
            try {
                $fileDeleteService->delete($file);
                $this->info("Deleted file ID: {$file->id}");
            } catch (\Exception $e) {
                $this->error("Failed to delete file ID: {$file->id}. Error: " . $e->getMessage());
            }
        }

        $this->info('Expired files cleanup completed successfully.');
    }
}
