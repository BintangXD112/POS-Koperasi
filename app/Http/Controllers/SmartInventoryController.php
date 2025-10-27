<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmartInventoryController extends Controller
{
    private $geminiApiKey = 'AIzaSyDf4CxpxcF4QytZoIfpomw5T0rBZPLdzig';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function index()
    {
        $products = Product::with('category')->get();
        $inventoryData = $this->getInventoryData();
        
        return view('admin.smart-inventory.index', compact('products', 'inventoryData'));
    }

    public function generateRecommendations(Request $request)
    {
        try {
            $inventoryData = $this->getInventoryData();
            $prompt = $this->generateInventoryPrompt($inventoryData);
            $aiResponse = $this->callGeminiAI($prompt);
            
            return response()->json([
                'success' => true,
                'recommendations' => $aiResponse
            ]);
        } catch (\Exception $e) {
            Log::error('Smart Inventory AI Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghasilkan rekomendasi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getInventoryData()
    {
        $products = Product::with(['category', 'transactionDetails.transaction'])
            ->get()
            ->map(function ($product) {
                $last30Days = now()->subDays(30);
                $salesData = $product->transactionDetails()
                    ->whereHas('transaction', function($query) use ($last30Days) {
                        $query->where('created_at', '>=', $last30Days);
                    })
                    ->get();

                $totalSold = $salesData->sum('quantity');
                $avgDailySales = $totalSold / 30;
                $daysUntilStockout = $avgDailySales > 0 ? $product->stock / $avgDailySales : 999;

                $minStock = 10; // Default minimum stock
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category->name ?? 'Uncategorized',
                    'current_stock' => $product->stock,
                    'min_stock' => $minStock,
                    'price' => $product->price,
                    'total_sold_30_days' => $totalSold,
                    'avg_daily_sales' => round($avgDailySales, 2),
                    'days_until_stockout' => round($daysUntilStockout, 1),
                    'status' => $this->getStockStatus($product->stock, $minStock, $daysUntilStockout)
                ];
            });

        return $products;
    }

    private function getStockStatus($currentStock, $minStock, $daysUntilStockout)
    {
        if ($currentStock <= 0) return 'out_of_stock';
        if ($currentStock <= $minStock) return 'low_stock';
        if ($daysUntilStockout <= 7) return 'critical';
        if ($daysUntilStockout <= 14) return 'warning';
        return 'good';
    }

    private function generateInventoryPrompt($inventoryData)
    {
        $prompt = "Analisis data inventory koperasi berikut dan berikan rekomendasi manajemen stok:\n\n";
        
        $prompt .= "DATA INVENTORY:\n";
        $prompt .= "Total Produk: " . $inventoryData->count() . "\n";
        
        $lowStock = $inventoryData->where('status', 'low_stock')->count();
        $outOfStock = $inventoryData->where('status', 'out_of_stock')->count();
        $critical = $inventoryData->where('status', 'critical')->count();
        
        $prompt .= "Produk Low Stock: {$lowStock}\n";
        $prompt .= "Produk Out of Stock: {$outOfStock}\n";
        $prompt .= "Produk Critical: {$critical}\n\n";
        
        $prompt .= "TOP 10 PRODUK YANG PERLU PERHATIAN:\n";
        foreach ($inventoryData->whereIn('status', ['out_of_stock', 'low_stock', 'critical'])->take(10) as $product) {
            $prompt .= "- {$product['name']} ({$product['category']}): Stok {$product['current_stock']}, Habis dalam {$product['days_until_stockout']} hari\n";
        }
        
        $prompt .= "\n\nBerikan 5 rekomendasi strategis untuk manajemen inventory dalam format JSON:\n";
        $prompt .= '{"recommendations": ["rekomendasi1", "rekomendasi2", "rekomendasi3", "rekomendasi4", "rekomendasi5"]}';

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
                        
                        if ($parsed && isset($parsed['recommendations'])) {
                            return $parsed;
                        }
                    }
                }
            }
            
            // Fallback
            return [
                'recommendations' => [
                    'Lakukan restock segera untuk produk yang low stock',
                    'Implementasikan sistem reorder point otomatis',
                    'Analisis pola penjualan untuk optimasi stok',
                    'Buat strategi promosi untuk produk yang slow moving',
                    'Monitor inventory secara real-time'
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Gemini AI Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
