<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppSetting;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ActivityLog;
use App\Models\ChatMessage;
use Carbon\Carbon;

class DataCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old data based on retention settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if data cleanup is enabled
        $dataCleanupEnabled = AppSetting::getValue('data_cleanup_enabled', false);
        if (!$dataCleanupEnabled) {
            $this->info('Data cleanup is disabled.');
            return;
        }

        $retentionDays = (int) AppSetting::getValue('data_retention_days', 365);
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        $this->info("Starting data cleanup for data older than {$retentionDays} days (before {$cutoffDate->format('Y-m-d')})");

        try {
            $deletedCounts = [];

            // Clean up old transactions (keep only completed ones)
            $deletedCounts['transactions'] = Transaction::where('created_at', '<', $cutoffDate)
                ->where('status', '!=', 'completed')
                ->delete();

            // Clean up old transaction details for deleted transactions
            $deletedCounts['transaction_details'] = TransactionDetail::where('created_at', '<', $cutoffDate)
                ->whereNotIn('transaction_id', Transaction::pluck('id'))
                ->delete();

            // Clean up old activity logs
            $deletedCounts['activity_logs'] = ActivityLog::where('created_at', '<', $cutoffDate)
                ->delete();

            // Clean up old chat messages (keep last 30 days for chat)
            $chatCutoffDate = Carbon::now()->subDays(30);
            $deletedCounts['chat_messages'] = ChatMessage::where('created_at', '<', $chatCutoffDate)
                ->delete();

            // Clean up old exports (keep last 7 days)
            $exportCutoffDate = Carbon::now()->subDays(7);
            $this->cleanupOldExports($exportCutoffDate);

            $this->info('Data cleanup completed successfully!');
            $this->info('Deleted records:');
            foreach ($deletedCounts as $type => $count) {
                $this->info("  - {$type}: {$count} records");
            }

        } catch (\Exception $e) {
            $this->error('Data cleanup failed: ' . $e->getMessage());
        }
    }

    private function cleanupOldExports($cutoffDate)
    {
        $this->info('Cleaning up old export files...');
        
        $exportPath = storage_path('app/exports');
        if (!is_dir($exportPath)) {
            return;
        }

        $files = glob($exportPath . '/*');
        $deletedFiles = 0;

        foreach ($files as $file) {
            if (is_file($file) && filemtime($file) < $cutoffDate->timestamp) {
                if (unlink($file)) {
                    $deletedFiles++;
                }
            }
        }

        $this->info("Deleted {$deletedFiles} old export files");
    }
}
