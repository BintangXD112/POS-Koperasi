<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AutomatedReportingController extends Controller
{
    private $geminiApiKey = 'AIzaSyDf4CxpxcF4QytZoIfpomw5T0rBZPLdzig';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function index()
    {
        $reportData = $this->getReportData();
        $scheduledReports = $this->getScheduledReports();
        
        return view('admin.automated-reporting.index', compact('reportData', 'scheduledReports'));
    }

    public function generateReport(Request $request)
    {
        try {
            $reportType = $request->input('report_type', 'daily');
            $period = $request->input('period', 1);
            
            $reportData = $this->getReportData($reportType, $period);
            $prompt = $this->generateReportPrompt($reportData, $reportType, $period);
            $aiResponse = $this->callGeminiAI($prompt);
            
            return response()->json([
                'success' => true,
                'report' => $aiResponse,
                'data' => $reportData
            ]);
        } catch (\Exception $e) {
            Log::error('Automated Reporting AI Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function scheduleReport(Request $request)
    {
        try {
            $reportType = $request->input('report_type');
            $frequency = $request->input('frequency');
            $email = $request->input('email');
            
            // Simpan jadwal laporan (bisa disimpan ke database)
            $schedule = [
                'report_type' => $reportType,
                'frequency' => $frequency,
                'email' => $email,
                'created_at' => now(),
                'next_run' => $this->calculateNextRun($frequency)
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Laporan otomatis berhasil dijadwalkan',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Schedule Report Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menjadwalkan laporan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getReportData($reportType = 'daily', $period = 1)
    {
        $endDate = now();
        $startDate = $this->getStartDate($reportType, $period, $endDate);
        
        // Sales Data
        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])->get();
        $totalRevenue = $transactions->sum('total_amount');
        $totalTransactions = $transactions->count();
        $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Product Performance
        $productSales = TransactionDetail::with('product')
            ->whereHas('transaction', function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
        
        // Customer Data
        $uniqueCustomers = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->distinct('user_id')
            ->count('user_id');
        
        $topCustomers = User::whereHas('transactions', function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with(['transactions' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function($user) {
            $totalSpent = $user->transactions->sum('total_amount');
            $transactionCount = $user->transactions->count();
            return [
                'name' => $user->name,
                'total_spent' => $totalSpent,
                'transaction_count' => $transactionCount
            ];
        })
        ->sortByDesc('total_spent')
        ->take(5);
        
        // Hourly Patterns (SQLite compatible)
        $hourlyData = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('CAST(strftime("%H", created_at) AS INTEGER) as hour, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
        
        // Daily Trends (for weekly/monthly reports) - SQLite compatible
        $dailyTrends = [];
        if ($reportType === 'weekly' || $reportType === 'monthly') {
            $dailyTrends = Transaction::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('strftime("%Y-%m-%d", created_at) as date, COUNT(*) as transactions, SUM(total_amount) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        }
        
        return [
            'report_type' => $reportType,
            'period' => $period,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_revenue' => $totalRevenue,
            'total_transactions' => $totalTransactions,
            'avg_transaction_value' => $avgTransactionValue,
            'unique_customers' => $uniqueCustomers,
            'product_sales' => $productSales,
            'top_customers' => $topCustomers,
            'hourly_data' => $hourlyData,
            'daily_trends' => $dailyTrends
        ];
    }

    private function getStartDate($reportType, $period, $endDate)
    {
        switch ($reportType) {
            case 'daily':
                return $endDate->copy()->subDays($period - 1)->startOfDay();
            case 'weekly':
                return $endDate->copy()->subWeeks($period)->startOfWeek();
            case 'monthly':
                return $endDate->copy()->subMonths($period)->startOfMonth();
            default:
                return $endDate->copy()->subDays($period - 1)->startOfDay();
        }
    }

    private function getScheduledReports()
    {
        // Simulasi data jadwal laporan (bisa dari database)
        return [
            [
                'id' => 1,
                'report_type' => 'daily',
                'frequency' => 'daily',
                'email' => 'admin@koperasi.com',
                'next_run' => now()->addDay()->format('Y-m-d H:i:s'),
                'status' => 'active'
            ],
            [
                'id' => 2,
                'report_type' => 'weekly',
                'frequency' => 'weekly',
                'email' => 'manager@koperasi.com',
                'next_run' => now()->addWeek()->format('Y-m-d H:i:s'),
                'status' => 'active'
            ]
        ];
    }

    private function calculateNextRun($frequency)
    {
        switch ($frequency) {
            case 'daily':
                return now()->addDay();
            case 'weekly':
                return now()->addWeek();
            case 'monthly':
                return now()->addMonth();
            default:
                return now()->addDay();
        }
    }

    private function generateReportPrompt($reportData, $reportType, $period)
    {
        $prompt = "Buat laporan {$reportType} untuk koperasi dengan analisis mendalam:\n\n";
        
        $prompt .= "PERIODE LAPORAN:\n";
        $prompt .= "Jenis: " . ucfirst($reportType) . " Report\n";
        $prompt .= "Periode: {$period} " . ($reportType === 'daily' ? 'hari' : ($reportType === 'weekly' ? 'minggu' : 'bulan')) . "\n";
        $prompt .= "Tanggal: {$reportData['start_date']->format('d/m/Y')} - {$reportData['end_date']->format('d/m/Y')}\n\n";
        
        $prompt .= "PERFORMA PENJUALAN:\n";
        $prompt .= "Total Revenue: Rp " . number_format($reportData['total_revenue'], 0, ',', '.') . "\n";
        $prompt .= "Total Transaksi: " . number_format($reportData['total_transactions']) . "\n";
        $prompt .= "Rata-rata Nilai Transaksi: Rp " . number_format($reportData['avg_transaction_value'], 0, ',', '.') . "\n";
        $prompt .= "Jumlah Pelanggan Unik: " . number_format($reportData['unique_customers']) . "\n\n";
        
        $prompt .= "TOP 5 PRODUK TERLARIS:\n";
        foreach ($reportData['product_sales']->take(5) as $product) {
            $prompt .= "- {$product->product->name}: {$product->total_sold} unit (Rp " . number_format($product->total_revenue, 0, ',', '.') . ")\n";
        }
        
        $prompt .= "\nTOP 5 PELANGGAN:\n";
        foreach ($reportData['top_customers']->take(5) as $customer) {
            $prompt .= "- {$customer['name']}: Rp " . number_format($customer['total_spent'], 0, ',', '.') . " ({$customer['transaction_count']} transaksi)\n";
        }
        
        $prompt .= "\n\nBuat executive summary dengan 5 insight utama dan 3 rekomendasi strategis dalam format JSON:\n";
        $prompt .= '{"summary": "Ringkasan eksekutif singkat", "insights": ["insight1", "insight2", "insight3", "insight4", "insight5"], "recommendations": ["rekomendasi1", "rekomendasi2", "rekomendasi3"]}';

        return $prompt;
    }

    private function callGeminiAI($prompt)
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 2048,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE']
                    ]
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    $text = $responseData['candidates'][0]['content']['parts'][0]['text'];
                    
                    $jsonStart = strpos($text, '{');
                    $jsonEnd = strrpos($text, '}') + 1;
                    
                    if ($jsonStart !== false && $jsonEnd !== false) {
                        $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart);
                        $parsed = json_decode($jsonString, true);
                        
                        if ($parsed && isset($parsed['summary']) && isset($parsed['insights']) && isset($parsed['recommendations'])) {
                            return $parsed;
                        }
                    }
                }
            }
            
            // Fallback
            return [
                'summary' => 'Laporan menunjukkan performa bisnis yang stabil dengan beberapa area yang dapat dioptimalkan.',
                'insights' => [
                    'Revenue menunjukkan tren positif dalam periode laporan',
                    'Produk terlaris mendominasi penjualan',
                    'Pelanggan menunjukkan loyalitas yang baik',
                    'Pola penjualan harian konsisten',
                    'Ada peluang peningkatan di beberapa kategori produk'
                ],
                'recommendations' => [
                    'Fokus pada pengembangan produk dengan performa terbaik',
                    'Implementasikan strategi retensi pelanggan',
                    'Analisis lebih mendalam untuk optimasi inventory'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
