<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AppSetting;
use App\Exports\ReportsExport;
use App\Exports\TransactionsExport;
use App\Exports\StockReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class AutoExportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically export reports based on schedule settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if auto export is enabled
        $autoExportEnabled = AppSetting::getValue('auto_export_enabled', false);
        if (!$autoExportEnabled) {
            $this->info('Auto export is disabled.');
            return;
        }

        $schedule = AppSetting::getValue('auto_export_schedule', 'daily');
        $format = AppSetting::getValue('auto_export_format', 'excel');

        $this->info("Starting auto export with schedule: {$schedule}, format: {$format}");

        try {
            // Export admin reports
            $this->exportAdminReports($format);
            
            // Export cashier transactions
            $this->exportCashierTransactions($format);
            
            // Export stock reports
            $this->exportStockReports($format);

            $this->info('Auto export completed successfully!');
            
            // Send notification email if enabled
            $emailNotifications = AppSetting::getValue('email_notifications', true);
            if ($emailNotifications) {
                $this->sendNotificationEmail($schedule, $format);
            }

        } catch (\Exception $e) {
            $this->error('Auto export failed: ' . $e->getMessage());
        }
    }

    private function exportAdminReports($format)
    {
        $this->info('Exporting admin reports...');
        
        $year = date('Y');
        $month = date('m');
        
        if ($format === 'excel') {
            $filename = "admin_reports_{$year}_{$month}.xlsx";
            Excel::store(new ReportsExport($year, $month), "exports/{$filename}");
        } else {
            $filename = "admin_reports_{$year}_{$month}.pdf";
            $pdf = Pdf::loadView('admin.reports.export-pdf', [
                'transactions' => \App\Models\Transaction::with(['details.product', 'user'])
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'completed')
                    ->get()
            ]);
            Storage::put("exports/{$filename}", $pdf->output());
        }
        
        $this->info("Admin reports exported: {$filename}");
    }

    private function exportCashierTransactions($format)
    {
        $this->info('Exporting cashier transactions...');
        
        $year = date('Y');
        $month = date('m');
        
        if ($format === 'excel') {
            $filename = "cashier_transactions_{$year}_{$month}.xlsx";
            Excel::store(new TransactionsExport($year, $month), "exports/{$filename}");
        } else {
            $filename = "cashier_transactions_{$year}_{$month}.pdf";
            $pdf = Pdf::loadView('kasir.transactions.export-pdf', [
                'transactions' => \App\Models\Transaction::with(['details.product', 'user'])
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->where('status', 'completed')
                    ->get()
            ]);
            Storage::put("exports/{$filename}", $pdf->output());
        }
        
        $this->info("Cashier transactions exported: {$filename}");
    }

    private function exportStockReports($format)
    {
        $this->info('Exporting stock reports...');
        
        $year = date('Y');
        $month = date('m');
        
        if ($format === 'excel') {
            $filename = "stock_reports_{$year}_{$month}.xlsx";
            Excel::store(new StockReportExport(), "exports/{$filename}");
        } else {
            $filename = "stock_reports_{$year}_{$month}.pdf";
            $pdf = Pdf::loadView('gudang.reports.export-pdf', [
                'products' => \App\Models\Product::with('category')->get()
            ]);
            Storage::put("exports/{$filename}", $pdf->output());
        }
        
        $this->info("Stock reports exported: {$filename}");
    }

    private function sendNotificationEmail($schedule, $format)
    {
        $this->info('Sending notification email...');
        
        // Get admin users
        $adminUsers = \App\Models\User::whereHas('role', function($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($adminUsers as $user) {
            try {
                Mail::raw("Auto export completed successfully!\n\nSchedule: {$schedule}\nFormat: {$format}\nTime: " . now(), function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Auto Export Completed - ' . config('app.name'));
                });
            } catch (\Exception $e) {
                $this->warn("Failed to send email to {$user->email}: " . $e->getMessage());
            }
        }
    }
}
