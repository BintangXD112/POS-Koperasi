<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PredictiveAnalyticsController extends Controller
{
    private $geminiApiKey = 'AIzaSyDf4CxpxcF4QytZoIfpomw5T0rBZPLdzig';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function index()
    {
        $salesData = $this->getSalesData();
        $forecastData = $this->getForecastData();
        $trendData = $this->getTrendData();
        
        return view('admin.predictive-analytics.index', compact('salesData', 'forecastData', 'trendData'));
    }

    public function generateForecast(Request $request)
    {
        try {
            $period = $request->input('period', 30); // days
            $forecastData = $this->getForecastData($period);
            $salesData = $this->getSalesData();
            $prompt = $this->generateForecastPrompt($salesData, $forecastData, $period);
            $aiResponse = $this->callGeminiAI($prompt);
            
            return response()->json([
                'success' => true,
                'forecast' => $aiResponse,
                'data' => $forecastData
            ]);
        } catch (\Exception $e) {
            Log::error('Predictive Analytics AI Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan prediksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSalesData()
    {
        $last30Days = now()->subDays(30);
        $last7Days = now()->subDays(7);
        
        // Daily sales for last 30 days (SQLite compatible)
        $dailySales = Transaction::where('created_at', '>=', $last30Days)
            ->selectRaw('strftime("%Y-%m-%d", created_at) as date, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Weekly sales for last 12 weeks (SQLite compatible)
        $weeklySales = Transaction::where('created_at', '>=', now()->subWeeks(12))
            ->selectRaw('strftime("%Y-W%W", created_at) as week, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        // Product performance
        $productSales = TransactionDetail::with('product')
            ->whereHas('transaction', function($query) use ($last30Days) {
                $query->where('created_at', '>=', $last30Days);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(10)
            ->get();

        return [
            'daily_sales' => $dailySales,
            'weekly_sales' => $weeklySales,
            'product_sales' => $productSales,
            'total_revenue_30_days' => $dailySales->sum('revenue'),
            'total_transactions_30_days' => $dailySales->sum('transactions'),
            'avg_daily_revenue' => $dailySales->avg('revenue'),
            'avg_daily_transactions' => $dailySales->avg('transactions')
        ];
    }

    private function getForecastData($period = 30)
    {
        $salesData = $this->getSalesData();
        $dailySales = $salesData['daily_sales'];
        
        // Simple moving average forecast
        $recentDays = $dailySales->take(7); // Last 7 days
        $avgRevenue = $recentDays->avg('revenue');
        $avgTransactions = $recentDays->avg('transactions');
        
        // Calculate growth rate
        $firstWeek = $dailySales->take(7)->avg('revenue');
        $lastWeek = $dailySales->take(-7)->avg('revenue');
        $growthRate = $firstWeek > 0 ? (($lastWeek - $firstWeek) / $firstWeek) * 100 : 0;
        
        // Generate forecast for next period
        $forecast = [];
        $currentDate = now();
        
        for ($i = 1; $i <= $period; $i++) {
            $forecastDate = $currentDate->copy()->addDays($i);
            $dayOfWeek = $forecastDate->dayOfWeek;
            
            // Adjust for weekend (lower sales on weekends)
            $weekendFactor = in_array($dayOfWeek, [0, 6]) ? 0.7 : 1.0;
            
            $forecastRevenue = $avgRevenue * (1 + ($growthRate / 100) * ($i / 30)) * $weekendFactor;
            $forecastTransactions = $avgTransactions * (1 + ($growthRate / 100) * ($i / 30)) * $weekendFactor;
            
            $forecast[] = [
                'date' => $forecastDate->format('Y-m-d'),
                'day_name' => $forecastDate->format('l'),
                'predicted_revenue' => round($forecastRevenue, 2),
                'predicted_transactions' => round($forecastTransactions, 0)
            ];
        }
        
        return [
            'forecast' => $forecast,
            'growth_rate' => round($growthRate, 2),
            'avg_daily_revenue' => round($avgRevenue, 2),
            'avg_daily_transactions' => round($avgTransactions, 2),
            'total_predicted_revenue' => round(collect($forecast)->sum('predicted_revenue'), 2),
            'total_predicted_transactions' => round(collect($forecast)->sum('predicted_transactions'), 0)
        ];
    }

    private function getTrendData()
    {
        $last90Days = now()->subDays(90);
        
        // Monthly trends (SQLite compatible)
        $monthlyTrends = Transaction::where('created_at', '>=', $last90Days)
            ->selectRaw('strftime("%Y", created_at) as year, strftime("%m", created_at) as month, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get();

        // Hourly patterns (SQLite compatible)
        $hourlyPatterns = Transaction::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('CAST(strftime("%H", created_at) AS INTEGER) as hour, COUNT(*) as transactions, SUM(total_amount) as revenue')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Category trends
        $categoryTrends = TransactionDetail::with('product.category')
            ->whereHas('transaction', function($query) use ($last90Days) {
                $query->where('created_at', '>=', $last90Days);
            })
            ->selectRaw('products.category_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->join('products', 'transaction_details.product_id', '=', 'products.id')
            ->groupBy('products.category_id')
            ->orderByDesc('total_revenue')
            ->get();

        return [
            'monthly_trends' => $monthlyTrends,
            'hourly_patterns' => $hourlyPatterns,
            'category_trends' => $categoryTrends
        ];
    }

    private function generateForecastPrompt($salesData, $forecastData, $period)
    {
        $prompt = "Analisis data penjualan koperasi dan berikan prediksi serta insight strategis:\n\n";
        
        $prompt .= "DATA HISTORIS (30 hari terakhir):\n";
        $prompt .= "Total Revenue: Rp " . number_format($salesData['total_revenue_30_days'], 0, ',', '.') . "\n";
        $prompt .= "Total Transaksi: " . number_format($salesData['total_transactions_30_days']) . "\n";
        $prompt .= "Rata-rata Revenue Harian: Rp " . number_format($salesData['avg_daily_revenue'], 0, ',', '.') . "\n";
        $prompt .= "Rata-rata Transaksi Harian: " . number_format($salesData['avg_daily_transactions'], 1) . "\n\n";
        
        $prompt .= "PREDIKSI ({$period} hari ke depan):\n";
        $prompt .= "Total Prediksi Revenue: Rp " . number_format($forecastData['total_predicted_revenue'], 0, ',', '.') . "\n";
        $prompt .= "Total Prediksi Transaksi: " . number_format($forecastData['total_predicted_transactions']) . "\n";
        $prompt .= "Growth Rate: " . $forecastData['growth_rate'] . "%\n\n";
        
        $prompt .= "TOP 5 PRODUK TERLARIS:\n";
        foreach ($salesData['product_sales']->take(5) as $product) {
            $prompt .= "- {$product->product->name}: {$product->total_sold} unit (Rp " . number_format($product->total_revenue, 0, ',', '.') . ")\n";
        }
        
        $prompt .= "\n\nBerikan 5 prediksi dan rekomendasi strategis dalam format JSON:\n";
        $prompt .= '{"predictions": ["prediksi1", "prediksi2", "prediksi3", "prediksi4", "prediksi5"], "recommendations": ["rekomendasi1", "rekomendasi2", "rekomendasi3", "rekomendasi4", "rekomendasi5"]}';

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
                        
                        if ($parsed && isset($parsed['predictions']) && isset($parsed['recommendations'])) {
                            return $parsed;
                        }
                    }
                }
            }
            
            // Fallback
            return [
                'predictions' => [
                    'Revenue akan meningkat seiring dengan tren positif',
                    'Transaksi harian akan stabil dengan sedikit fluktuasi',
                    'Produk terlaris akan tetap mendominasi penjualan',
                    'Weekend akan menunjukkan penurunan penjualan',
                    'Growth rate akan stabil dalam kisaran positif'
                ],
                'recommendations' => [
                    'Fokus pada produk dengan performa terbaik',
                    'Implementasikan strategi promosi untuk weekend',
                    'Monitor stok produk terlaris secara ketat',
                    'Analisis pola pembelian untuk optimasi',
                    'Siapkan strategi untuk periode high demand'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
