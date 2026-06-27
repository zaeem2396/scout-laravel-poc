<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanupLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=7 : Delete log files older than this many days}';

    protected $description = 'Remove stale application log files';

    public function handle(): int
    {
        $logPath = storage_path('logs');
        $cutoff = now()->subDays((int) $this->option('days'))->getTimestamp();
        $removed = 0;

        if (! File::isDirectory($logPath)) {
            $this->warn('Log directory does not exist.');

            return self::SUCCESS;
        }

        foreach (File::files($logPath) as $file) {
            if ($file->getMTime() < $cutoff && $file->getExtension() === 'log') {
                File::delete($file->getPathname());
                $removed++;
            }
        }

        Log::info('Log cleanup completed', [
            'removed_files' => $removed,
            'older_than_days' => (int) $this->option('days'),
        ]);

        $this->info("Removed {$removed} log file(s).");

        return self::SUCCESS;
    }
}
