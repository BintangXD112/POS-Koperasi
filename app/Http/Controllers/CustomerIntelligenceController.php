<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CustomerIntelligenceController extends Controller
{
    private $geminiApiKey = 'AIzaSyDf4CxpxcF4QytZoIfpomw5T0rBZPLdzig';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function index()
    {
        $customerData = $this->getCustomerData();
        $segmentationData = $this->getSegmentationData();
        
        return view('admin.customer-intelligence.index', compact('customerData', 'segmentationData'));
    }

    public function generateInsights(Request $request)
    {
        try {
            $customerData = $this->getCustomerData();
            $segmentationData = $this->getSegmentationData();
            $prompt = $this->generateCustomerPrompt($customerData, $segmentationData);
            $aiResponse = $this->callGeminiAI($prompt);
            
            return response()->json([
                'success' => true,
                'insights' => $aiResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Customer Intelligence AI Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan insight: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getCustomerData()
    {
        // Get users who have made transactions (customers)
        $totalCustomers = User::whereHas('transactions')->count();
        $activeCustomers = User::whereHas('transactions', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();

        $customerMetrics = User::with(['transactions' => function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        }])->whereHas('transactions')->get()->map(function($customer) {
            $transactions = $customer->transactions;
            $totalSpent = $transactions->sum('total_amount');
            $transactionCount = $transactions->count();
            $avgTransactionValue = $transactionCount > 0 ? $totalSpent / $transactionCount : 0;
            $lastPurchase = $transactions->max('created_at');
            $daysSinceLastPurchase = $lastPurchase ? now()->diffInDays($lastPurchase) : 999;

            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone ?? 'N/A',
                'total_spent' => $totalSpent,
                'transaction_count' => $transactionCount,
                'avg_transaction_value' => $avgTransactionValue,
                'days_since_last_purchase' => $daysSinceLastPurchase,
                'customer_value' => $this->calculateCustomerValue($totalSpent, $transactionCount, $daysSinceLastPurchase)
            ];
        });

        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'customer_metrics' => $customerMetrics,
            'avg_customer_value' => $customerMetrics->avg('customer_value'),
            'top_customers' => $customerMetrics->sortByDesc('total_spent')->take(10)
        ];
    }

    private function getSegmentationData()
    {
        $customerData = $this->getCustomerData();
        $customers = $customerData['customer_metrics'];

        $segments = [
            'vip' => $customers->where('total_spent', '>=', 1000000)->count(),
            'loyal' => $customers->where('transaction_count', '>=', 10)->count(),
            'at_risk' => $customers->where('days_since_last_purchase', '>', 30)->count(),
            'new' => $customers->where('transaction_count', '<=', 2)->count(),
            'inactive' => $customers->where('days_since_last_purchase', '>', 90)->count()
        ];

        return $segments;
    }

    private function calculateCustomerValue($totalSpent, $transactionCount, $daysSinceLastPurchase)
    {
        $spentScore = min($totalSpent / 100000, 10); // Max 10 points
        $frequencyScore = min($transactionCount / 5, 10); // Max 10 points
        $recencyScore = max(0, 10 - ($daysSinceLastPurchase / 10)); // Max 10 points
        
        return round(($spentScore + $frequencyScore + $recencyScore) / 3, 2);
    }

    private function generateCustomerPrompt($customerData, $segmentationData)
    {
        $prompt = "Analisis data pelanggan koperasi berikut dan berikan insight strategis:\n\n";
        
        $prompt .= "DATA PELANGGAN:\n";
        $prompt .= "Total Pelanggan: {$customerData['total_customers']}\n";
        $prompt .= "Pelanggan Aktif (30 hari): {$customerData['active_customers']}\n";
        $prompt .= "Rata-rata Customer Value: " . number_format($customerData['avg_customer_value'], 2) . "\n\n";
        
        $prompt .= "SEGMENTASI PELANGGAN:\n";
        $prompt .= "VIP Customers (≥1M): {$segmentationData['vip']}\n";
        $prompt .= "Loyal Customers (≥10 transaksi): {$segmentationData['loyal']}\n";
        $prompt .= "At Risk Customers (>30 hari): {$segmentationData['at_risk']}\n";
        $prompt .= "New Customers (≤2 transaksi): {$segmentationData['new']}\n";
        $prompt .= "Inactive Customers (>90 hari): {$segmentationData['inactive']}\n\n";
        
        $prompt .= "TOP 5 CUSTOMERS:\n";
        foreach ($customerData['top_customers']->take(5) as $customer) {
            $prompt .= "- {$customer['name']}: Rp " . number_format($customer['total_spent'], 0, ',', '.') . " ({$customer['transaction_count']} transaksi)\n";
        }
        
        $prompt .= "\n\nBerikan 5 insight strategis untuk meningkatkan customer engagement dalam format JSON:\n";
        $prompt .= '{"insights": ["insight1", "insight2", "insight3", "insight4", "insight5"]}';

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
                        
                        if ($parsed && isset($parsed['insights'])) {
                            return $parsed;
                        }
                    }
                }
            }
            
            // Fallback
            return [
                'insights' => [
                    'Fokus pada customer retention untuk pelanggan VIP',
                    'Implementasikan program loyalitas untuk customer yang at risk',
                    'Buat strategi onboarding untuk new customers',
                    'Lakukan re-engagement campaign untuk inactive customers',
                    'Analisis pola pembelian untuk personalisasi produk'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
