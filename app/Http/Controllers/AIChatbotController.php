<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\AiChatMessage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIChatbotController extends Controller
{
    private $geminiApiKey = 'AIzaSyDf4CxpxcF4QytZoIfpomw5T0rBZPLdzig';
    private $geminiApiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent';

    public function index(Request $request)
    {
        $sessionId = $request->get('session_id');
        $chatSession = null;
        $chatHistory = [];
        
        if ($sessionId) {
            $chatSession = ChatSession::where('session_id', $sessionId)
                ->where('user_id', auth()->id())
                ->with('messages')
                ->first();
            
            if ($chatSession) {
                $chatHistory = $chatSession->messages()->orderBy('created_at')->get();
                $chatSession->updateActivity();
            }
        }
        
        // Get recent sessions for sidebar
        $recentSessions = ChatSession::forUser(auth()->id())
            ->active()
            ->recent(30)
            ->orderBy('last_activity', 'desc')
            ->limit(10)
            ->get();
        
        $quickActions = $this->getQuickActions();
        
        return view('admin.ai-chatbot.index', compact('chatSession', 'chatHistory', 'recentSessions', 'quickActions'));
    }

    public function sendMessage(Request $request)
    {
        try {
            $message = $request->input('message');
            $sessionId = $request->input('session_id');
            
            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'response' => 'Pesan tidak boleh kosong. Silakan ketik pesan Anda.'
                ]);
            }
            
            // Get or create chat session
            $chatSession = $this->getOrCreateSession($sessionId);
            
            // Save user message
            $userMessage = $chatSession->messages()->create([
                'sender_type' => 'user',
                'message' => $message,
                'context' => null
            ]);
            
            // Determine context based on message content and conversation history
            $context = $this->determineContext($message, $chatSession);
            
            // Get relevant data based on context
            $data = $this->getContextData($context);
            
            // Generate AI response with enhanced reasoning
            $aiResponse = $this->generateAIResponse($message, $data, $context, $chatSession);
            
            // Save AI response
            $aiMessage = $chatSession->messages()->create([
                'sender_type' => 'ai',
                'message' => $aiResponse['response'],
                'context' => $context,
                'metadata' => $aiResponse['metadata'] ?? null
            ]);
            
            // Update session activity and title if needed
            $this->updateSessionTitle($chatSession, $message);
            $chatSession->updateActivity();
            
            return response()->json([
                'success' => true,
                'response' => $aiResponse['response'],
                'context' => $context,
                'session_id' => $chatSession->session_id,
                'reasoning' => $aiResponse['reasoning'] ?? null,
                'confidence' => $aiResponse['confidence'] ?? null
            ]);
        } catch (\Exception $e) {
            Log::error('AI Chatbot Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'response' => 'Maaf, terjadi kesalahan saat memproses pesan Anda. Silakan coba lagi.'
            ]);
        }
    }

    public function quickAction(Request $request)
    {
        try {
            $action = $request->input('action');
            $data = $this->getQuickActionData($action);
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => $this->getQuickActionMessage($action)
            ]);
        } catch (\Exception $e) {
            Log::error('Quick Action Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses aksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getChatHistory()
    {
        // Simulasi chat history (bisa dari database)
        return [
            [
                'id' => 1,
                'type' => 'user',
                'message' => 'Halo, bagaimana performa penjualan hari ini?',
                'timestamp' => now()->subMinutes(5),
                'context' => 'sales'
            ],
            [
                'id' => 2,
                'type' => 'ai',
                'message' => 'Halo! Berdasarkan data terbaru, penjualan hari ini menunjukkan performa yang baik dengan total revenue Rp 2.500.000 dari 15 transaksi.',
                'timestamp' => now()->subMinutes(4),
                'context' => 'sales'
            ]
        ];
    }

    private function getQuickActions()
    {
        return [
            [
                'id' => 'sales_summary',
                'title' => 'Sales Summary',
                'description' => 'Ringkasan penjualan hari ini',
                'icon' => '📊',
                'color' => 'blue'
            ],
            [
                'id' => 'inventory_status',
                'title' => 'Inventory Status',
                'description' => 'Status stok produk',
                'icon' => '📦',
                'color' => 'green'
            ],
            [
                'id' => 'top_products',
                'title' => 'Top Products',
                'description' => 'Produk terlaris',
                'icon' => '🏆',
                'color' => 'yellow'
            ],
            [
                'id' => 'customer_insights',
                'title' => 'Customer Insights',
                'description' => 'Insight pelanggan',
                'icon' => '👥',
                'color' => 'purple'
            ],
            [
                'id' => 'revenue_forecast',
                'title' => 'Revenue Forecast',
                'description' => 'Prediksi revenue',
                'icon' => '🔮',
                'color' => 'indigo'
            ],
            [
                'id' => 'help',
                'title' => 'Help & Support',
                'description' => 'Bantuan dan dukungan',
                'icon' => '❓',
                'color' => 'gray'
            ]
        ];
    }

    private function getOrCreateSession($sessionId = null)
    {
        if ($sessionId) {
            $session = ChatSession::where('session_id', $sessionId)
                ->where('user_id', auth()->id())
                ->first();
            
            if ($session) {
                return $session;
            }
        }
        
        // Create new session
        return ChatSession::create([
            'session_id' => Str::uuid(),
            'user_id' => auth()->id(),
            'title' => 'Chat Baru',
            'is_active' => true,
            'last_activity' => now()
        ]);
    }

    private function determineContext($message, $chatSession = null)
    {
        $message = strtolower($message);
        
        // Check conversation history for context clues
        if ($chatSession && $chatSession->messages()->count() > 0) {
            $recentMessages = $chatSession->messages()
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            
            foreach ($recentMessages as $msg) {
                if ($msg->context && $msg->context !== 'general') {
                    // Continue conversation in same context if related
                    if ($this->isContextRelated($message, $msg->context)) {
                        return $msg->context;
                    }
                }
            }
        }
        
        // Sales related keywords
        if (strpos($message, 'penjualan') !== false || strpos($message, 'transaksi') !== false || 
            strpos($message, 'revenue') !== false || strpos($message, 'omzet') !== false ||
            strpos($message, 'jual') !== false || strpos($message, 'beli') !== false) {
            return 'sales';
        }
        
        // Inventory related keywords
        if (strpos($message, 'stok') !== false || strpos($message, 'inventory') !== false || 
            strpos($message, 'barang') !== false || strpos($message, 'gudang') !== false ||
            strpos($message, 'persediaan') !== false) {
            return 'inventory';
        }
        
        // Product related keywords
        if (strpos($message, 'produk') !== false || strpos($message, 'terlaris') !== false || 
            strpos($message, 'best seller') !== false || strpos($message, 'item') !== false) {
            return 'products';
        }
        
        // Customer related keywords
        if (strpos($message, 'pelanggan') !== false || strpos($message, 'customer') !== false || 
            strpos($message, 'pembeli') !== false || strpos($message, 'member') !== false) {
            return 'customers';
        }
        
        // Forecast related keywords
        if (strpos($message, 'prediksi') !== false || strpos($message, 'forecast') !== false || 
            strpos($message, 'ramalan') !== false || strpos($message, 'proyeksi') !== false) {
            return 'forecast';
        }
        
        // Report related keywords
        if (strpos($message, 'laporan') !== false || strpos($message, 'report') !== false || 
            strpos($message, 'analisis') !== false || strpos($message, 'data') !== false) {
            return 'reports';
        }
        
        return 'general';
    }

    private function isContextRelated($message, $context)
    {
        $message = strtolower($message);
        
        $contextKeywords = [
            'sales' => ['penjualan', 'transaksi', 'revenue', 'omzet', 'jual', 'beli'],
            'inventory' => ['stok', 'inventory', 'barang', 'gudang', 'persediaan'],
            'products' => ['produk', 'terlaris', 'best seller', 'item'],
            'customers' => ['pelanggan', 'customer', 'pembeli', 'member'],
            'forecast' => ['prediksi', 'forecast', 'ramalan', 'proyeksi'],
            'reports' => ['laporan', 'report', 'analisis', 'data']
        ];
        
        if (isset($contextKeywords[$context])) {
            foreach ($contextKeywords[$context] as $keyword) {
                if (strpos($message, $keyword) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }

    private function getContextData($context)
    {
        try {
            switch ($context) {
                case 'sales':
                    return $this->getSalesData();
                case 'inventory':
                    return $this->getInventoryData();
                case 'products':
                    return $this->getProductsData();
                case 'customers':
                    return $this->getCustomersData();
                case 'forecast':
                    return $this->getForecastData();
                default:
                    return $this->getGeneralData();
            }
        } catch (\Exception $e) {
            Log::error('Error getting context data: ' . $e->getMessage());
            return $this->getGeneralData();
        }
    }

    private function getSalesData()
    {
        $today = now()->startOfDay();
        $transactions = Transaction::where('created_at', '>=', $today)->get();
        
        return [
            'total_revenue' => $transactions->sum('total_amount'),
            'total_transactions' => $transactions->count(),
            'avg_transaction_value' => $transactions->count() > 0 ? $transactions->sum('total_amount') / $transactions->count() : 0,
            'period' => 'hari ini'
        ];
    }

    private function getInventoryData()
    {
        $products = Product::with('category')->get();
        $lowStock = $products->where('stock', '<=', 10)->count();
        $outOfStock = $products->where('stock', '<=', 0)->count();
        
        return [
            'total_products' => $products->count(),
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'total_stock_value' => $products->sum(function($product) {
                return $product->stock * $product->price;
            })
        ];
    }

    private function getProductsData()
    {
        $topProducts = TransactionDetail::with('product')
            ->whereHas('transaction', function($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(subtotal) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();
        
        return [
            'top_products' => $topProducts,
            'period' => '7 hari terakhir'
        ];
    }

    private function getCustomersData()
    {
        $totalCustomers = User::whereHas('transactions')->count();
        $activeCustomers = User::whereHas('transactions', function($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        })->count();
        
        return [
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'period' => '30 hari terakhir'
        ];
    }

    private function getForecastData()
    {
        // Simple forecast based on recent data
        $recentRevenue = Transaction::where('created_at', '>=', now()->subDays(7))->sum('total_amount');
        $avgDailyRevenue = $recentRevenue / 7;
        
        return [
            'avg_daily_revenue' => $avgDailyRevenue,
            'predicted_weekly_revenue' => $avgDailyRevenue * 7,
            'growth_trend' => 'positive'
        ];
    }

    private function getGeneralData()
    {
        return [
            'system_status' => 'operational',
            'last_update' => now()->format('d/m/Y H:i'),
            'total_products' => Product::count(),
            'total_customers' => User::whereHas('transactions')->count()
        ];
    }

    private function getQuickActionData($action)
    {
        switch ($action) {
            case 'sales_summary':
                return $this->getSalesData();
            case 'inventory_status':
                return $this->getInventoryData();
            case 'top_products':
                return $this->getProductsData();
            case 'customer_insights':
                return $this->getCustomersData();
            case 'revenue_forecast':
                return $this->getForecastData();
            default:
                return $this->getGeneralData();
        }
    }

    private function getQuickActionMessage($action)
    {
        $messages = [
            'sales_summary' => 'Berikut adalah ringkasan penjualan terbaru:',
            'inventory_status' => 'Status inventory saat ini:',
            'top_products' => 'Produk terlaris berdasarkan data:',
            'customer_insights' => 'Insight pelanggan terbaru:',
            'revenue_forecast' => 'Prediksi revenue berdasarkan tren:',
            'help' => 'Saya siap membantu Anda! Anda bisa bertanya tentang penjualan, inventory, produk, atau hal lainnya.'
        ];
        
        return $messages[$action] ?? 'Data berhasil diambil.';
    }

    private function generateAIResponse($message, $data, $context, $chatSession = null)
    {
        $prompt = $this->buildEnhancedPrompt($message, $data, $context, $chatSession);
        $response = $this->callGeminiAI($prompt);
        
        // Parse response for reasoning and confidence
        $parsedResponse = $this->parseAIResponse($response);
        
        return $parsedResponse;
    }

    private function buildEnhancedPrompt($message, $data, $context, $chatSession = null)
    {
        $prompt = "Anda adalah asisten AI cerdas untuk sistem POS Koperasi yang komprehensif. Anda memiliki pengetahuan mendalam tentang seluruh aplikasi dan dapat memberikan analisis yang akurat dan actionable.\n\n";
        
        $prompt .= "PENGETAHUAN SISTEM POS KOPERASI:\n";
        $prompt .= "Aplikasi ini adalah sistem Point of Sale (POS) untuk koperasi dengan fitur-fitur berikut:\n\n";
        
        $prompt .= "1. STRUKTUR ROLE DAN AKSES:\n";
        $prompt .= "- Admin: Akses penuh ke semua fitur, manajemen user, laporan, monitoring aktivitas\n";
        $prompt .= "- Kasir: Dashboard harian, Point of Sale (POS), transaksi, riwayat transaksi\n";
        $prompt .= "- Gudang: Dashboard monitoring stok, manajemen produk/kategori, penyesuaian stok, laporan stok\n\n";
        
        $prompt .= "2. FITUR UTAMA SISTEM:\n";
        $prompt .= "- Dashboard dengan statistik real-time untuk setiap role\n";
        $prompt .= "- Point of Sale (POS) dengan pencarian produk cepat\n";
        $prompt .= "- Manajemen produk dan kategori dengan stok tracking\n";
        $prompt .= "- Sistem transaksi lengkap dengan detail item\n";
        $prompt .= "- Activity logging untuk monitoring keamanan\n";
        $prompt .= "- Export data dalam format CSV dan PDF\n";
        $prompt .= "- Chat group untuk komunikasi internal\n";
        $prompt .= "- Profile management untuk semua user\n\n";
        
        $prompt .= "3. FITUR AI DAN ANALISIS LANJUTAN:\n";
        $prompt .= "- AI Chatbot (fitur ini) untuk customer service dan bantuan\n";
        $prompt .= "- Market Analysis: Analisis profit, tren pasar, performa produk, perilaku customer\n";
        $prompt .= "- Predictive Analytics: Prediksi revenue, forecasting, analisis tren\n";
        $prompt .= "- Smart Inventory: Manajemen stok cerdas dengan rekomendasi\n";
        $prompt .= "- Customer Intelligence: Analisis perilaku dan segmentasi pelanggan\n";
        $prompt .= "- Automated Reporting: Laporan otomatis dengan scheduling\n\n";
        
        $prompt .= "4. STRUKTUR DATA:\n";
        $prompt .= "- Users: Admin, Kasir, Gudang dengan role-based access\n";
        $prompt .= "- Products: Nama, deskripsi, harga, stok, SKU, kategori\n";
        $prompt .= "- Categories: Kategori produk untuk organisasi\n";
        $prompt .= "- Transactions: Header transaksi dengan total amount dan status\n";
        $prompt .= "- Transaction Details: Detail item transaksi dengan quantity dan subtotal\n";
        $prompt .= "- Activity Logs: Log semua aktivitas user untuk keamanan\n";
        $prompt .= "- Store Settings: Konfigurasi toko (nama, alamat, logo, dll)\n";
        $prompt .= "- Chat Sessions & Messages: Sistem chat AI dengan context\n";
        $prompt .= "- Market Analysis: Data analisis pasar yang dihasilkan AI\n\n";
        
        $prompt .= "5. NAVIGASI DAN MENU:\n";
        $prompt .= "- Admin: Dashboard, Users, Reports, Activity Logs, AI Chatbot, Market Analysis, Predictive Analytics, Smart Inventory, Customer Intelligence, Automated Reporting, Store Settings\n";
        $prompt .= "- Kasir: Dashboard, Point of Sale, Transactions, Profile\n";
        $prompt .= "- Gudang: Dashboard, Products, Categories, Stock Reports, Profile\n";
        $prompt .= "- Global: Chat, Profile Edit (semua role)\n\n";
        
        $prompt .= "KONTEKS PERCAKAPAN: {$context}\n";
        
        // Add conversation history for context
        if ($chatSession && $chatSession->messages()->count() > 0) {
            $prompt .= "RIWAYAT PERCAKAPAN:\n";
            $recentMessages = $chatSession->messages()
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->reverse();
            
            foreach ($recentMessages as $msg) {
                $sender = $msg->sender_type === 'user' ? 'User' : 'AI';
                $prompt .= "{$sender}: {$msg->message}\n";
            }
            $prompt .= "\n";
        }
        
        $prompt .= "DATA TERKINI SISTEM:\n";
        
        switch ($context) {
            case 'sales':
                $prompt .= "Total Revenue: Rp " . number_format($data['total_revenue'], 0, ',', '.') . "\n";
                $prompt .= "Total Transaksi: {$data['total_transactions']}\n";
                $prompt .= "Rata-rata Nilai Transaksi: Rp " . number_format($data['avg_transaction_value'], 0, ',', '.') . "\n";
                $prompt .= "Periode: {$data['period']}\n";
                break;
            case 'inventory':
                $prompt .= "Total Produk: {$data['total_products']}\n";
                $prompt .= "Low Stock: {$data['low_stock']}\n";
                $prompt .= "Out of Stock: {$data['out_of_stock']}\n";
                $prompt .= "Total Nilai Stok: Rp " . number_format($data['total_stock_value'], 0, ',', '.') . "\n";
                break;
            case 'products':
                $prompt .= "Top 5 Produk Terlaris ({$data['period']}):\n";
                foreach ($data['top_products'] as $product) {
                    $prompt .= "- {$product->product->name}: {$product->total_sold} unit (Rp " . number_format($product->total_revenue, 0, ',', '.') . ")\n";
                }
                break;
            case 'customers':
                $prompt .= "Total Pelanggan: {$data['total_customers']}\n";
                $prompt .= "Pelanggan Aktif: {$data['active_customers']}\n";
                $prompt .= "Periode: {$data['period']}\n";
                break;
            case 'forecast':
                $prompt .= "Rata-rata Revenue Harian: Rp " . number_format($data['avg_daily_revenue'], 0, ',', '.') . "\n";
                $prompt .= "Prediksi Revenue Mingguan: Rp " . number_format($data['predicted_weekly_revenue'], 0, ',', '.') . "\n";
                $prompt .= "Growth Trend: {$data['growth_trend']}\n";
                break;
            case 'reports':
                $prompt .= "Sistem memiliki berbagai laporan: Analisis Pasar, Smart Inventory, Customer Intelligence, Predictive Analytics, dan Automated Reporting.\n";
                break;
            case 'general':
                $prompt .= "Status Sistem: {$data['system_status']}\n";
                $prompt .= "Update Terakhir: {$data['last_update']}\n";
                $prompt .= "Total Produk: {$data['total_products']}\n";
                $prompt .= "Total Pelanggan: {$data['total_customers']}\n";
                break;
        }
        
        $prompt .= "\nPERTANYAAN USER: {$message}\n\n";
        $prompt .= "INSTRUKSI RESPONSE:\n";
        $prompt .= "1. Berikan jawaban yang ramah, informatif, dan actionable berdasarkan pengetahuan sistem yang lengkap\n";
        $prompt .= "2. Gunakan data di atas untuk mendukung jawaban dengan angka dan fakta yang akurat\n";
        $prompt .= "3. Berikan insight mendalam dan rekomendasi yang berguna untuk bisnis\n";
        $prompt .= "4. Jika relevan, sarankan fitur lain dalam sistem yang dapat membantu\n";
        $prompt .= "5. Jelaskan cara mengakses fitur yang disebutkan dengan rute yang tepat\n";
        $prompt .= "6. Berikan konteks tentang role user dan akses yang sesuai\n";
        $prompt .= "7. Jawab dalam format JSON dengan struktur:\n";
        $prompt .= '{"response": "jawaban utama yang komprehensif", "reasoning": "penjelasan reasoning berdasarkan data", "confidence": "tingkat kepercayaan (1-10)", "suggestions": ["saran actionable"], "related_features": ["fitur terkait dengan rute"], "next_steps": ["langkah selanjutnya yang bisa dilakukan"]}';
        
        return $prompt;
    }

    private function parseAIResponse($response)
    {
        // Try to parse as JSON first
        $jsonStart = strpos($response, '{');
        $jsonEnd = strrpos($response, '}') + 1;
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonString = substr($response, $jsonStart, $jsonEnd - $jsonStart);
            $parsed = json_decode($jsonString, true);
            
            if ($parsed && isset($parsed['response'])) {
                return [
                    'response' => $parsed['response'],
                    'reasoning' => $parsed['reasoning'] ?? null,
                    'confidence' => $parsed['confidence'] ?? 8,
                    'suggestions' => $parsed['suggestions'] ?? [],
                    'related_features' => $parsed['related_features'] ?? [],
                    'metadata' => [
                        'reasoning' => $parsed['reasoning'] ?? null,
                        'confidence' => $parsed['confidence'] ?? 8,
                        'suggestions' => $parsed['suggestions'] ?? [],
                        'related_features' => $parsed['related_features'] ?? []
                    ]
                ];
            }
        }
        
        // Fallback to plain text response
        return [
            'response' => $response,
            'reasoning' => 'Response generated based on available data and context',
            'confidence' => 7,
            'suggestions' => [],
            'related_features' => [],
            'metadata' => [
                'reasoning' => 'Response generated based on available data and context',
                'confidence' => 7,
                'suggestions' => [],
                'related_features' => []
            ]
        ];
    }

    private function callGeminiAI($prompt)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->geminiApiUrl . '?key=' . $this->geminiApiKey, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 1024,
                    ],
                    'safetySettings' => [
                        ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE']
                    ]
                ]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                    return $responseData['candidates'][0]['content']['parts'][0]['text'];
                }
            }
            
            // Fallback response based on context
            return $this->getFallbackResponse($prompt);
        } catch (\Exception $e) {
            Log::error('Gemini AI Error: ' . $e->getMessage());
            return $this->getFallbackResponse($prompt);
        }
    }

    private function getFallbackResponse($prompt)
    {
        $userRole = auth()->user()->role->name ?? 'unknown';
        
        // Extract context from prompt
        if (strpos($prompt, 'KONTEKS PERCAKAPAN: sales') !== false) {
            $response = "Berdasarkan data penjualan terbaru, sistem menunjukkan performa yang stabil. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda dapat mengakses laporan penjualan lengkap di menu Admin > Reports atau menggunakan fitur Automated Reporting untuk laporan otomatis.";
            } elseif ($userRole === 'kasir') {
                $response .= "Sebagai Kasir, Anda dapat melihat riwayat transaksi di menu Kasir > Transactions dan menggunakan Point of Sale untuk transaksi baru.";
            } else {
                $response .= "Silakan hubungi Admin untuk akses laporan penjualan.";
            }
            return $response;
        } elseif (strpos($prompt, 'KONTEKS PERCAKAPAN: inventory') !== false) {
            $response = "Status inventory saat ini dapat dilihat di menu Gudang. ";
            if ($userRole === 'gudang') {
                $response .= "Sebagai staff Gudang, Anda dapat mengelola stok produk di menu Gudang > Products, menyesuaikan stok, dan melihat laporan stok di Gudang > Stock Reports. Gunakan fitur Smart Inventory untuk rekomendasi manajemen stok yang optimal.";
            } elseif ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda dapat memantau status stok di dashboard Admin dan menggunakan fitur Smart Inventory untuk analisis mendalam.";
            } else {
                $response .= "Silakan hubungi staff Gudang atau Admin untuk informasi stok.";
            }
            return $response;
        } elseif (strpos($prompt, 'KONTEKS PERCAKAPAN: products') !== false) {
            $response = "Informasi produk terlaris tersedia di berbagai menu. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda dapat mengakses Market Analysis untuk analisis produk mendalam, atau Smart Inventory untuk rekomendasi produk. Dashboard Admin juga menampilkan statistik produk terbaru.";
            } elseif ($userRole === 'gudang') {
                $response .= "Sebagai staff Gudang, Anda dapat mengelola produk di menu Gudang > Products dan melihat laporan stok di Gudang > Stock Reports.";
            } elseif ($userRole === 'kasir') {
                $response .= "Sebagai Kasir, Anda dapat melihat semua produk yang tersedia di Point of Sale dan melakukan pencarian produk cepat.";
            }
            return $response;
        } elseif (strpos($prompt, 'KONTEKS PERCAKAPAN: customers') !== false) {
            $response = "Data pelanggan dapat diakses melalui berbagai fitur. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda dapat menggunakan Customer Intelligence untuk analisis perilaku pelanggan mendalam, atau melihat data transaksi di menu Reports.";
            } elseif ($userRole === 'kasir') {
                $response .= "Sebagai Kasir, Anda dapat melihat riwayat transaksi pelanggan di menu Kasir > Transactions.";
            } else {
                $response .= "Silakan hubungi Admin untuk akses data pelanggan.";
            }
            return $response;
        } elseif (strpos($prompt, 'KONTEKS PERCAKAPAN: forecast') !== false) {
            $response = "Prediksi dan forecasting tersedia di fitur AI. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda dapat menggunakan Predictive Analytics untuk prediksi revenue dan analisis tren, atau Market Analysis untuk analisis pasar mendalam.";
            } else {
                $response .= "Fitur prediksi hanya tersedia untuk Admin. Silakan hubungi Admin untuk akses.";
            }
            return $response;
        } elseif (strpos($prompt, 'KONTEKS PERCAKAPAN: reports') !== false) {
            $response = "Sistem memiliki berbagai jenis laporan. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda memiliki akses ke semua laporan: Reports (laporan dasar), Market Analysis, Predictive Analytics, Smart Inventory, Customer Intelligence, dan Automated Reporting.";
            } elseif ($userRole === 'gudang') {
                $response .= "Sebagai staff Gudang, Anda dapat mengakses laporan stok di menu Gudang > Stock Reports.";
            } elseif ($userRole === 'kasir') {
                $response .= "Sebagai Kasir, Anda dapat melihat riwayat transaksi di menu Kasir > Transactions.";
            }
            return $response;
        } else {
            $response = "Halo! Saya AI Assistant untuk sistem POS Koperasi. ";
            if ($userRole === 'admin') {
                $response .= "Sebagai Admin, Anda memiliki akses penuh ke semua fitur: Dashboard, User Management, Reports, AI Chatbot, Market Analysis, Predictive Analytics, Smart Inventory, Customer Intelligence, dan Automated Reporting. Silakan tanyakan apa yang ingin Anda ketahui!";
            } elseif ($userRole === 'kasir') {
                $response .= "Sebagai Kasir, Anda dapat menggunakan Point of Sale untuk transaksi, melihat riwayat transaksi, dan mengakses dashboard harian. Bagaimana saya bisa membantu Anda hari ini?";
            } elseif ($userRole === 'gudang') {
                $response .= "Sebagai staff Gudang, Anda dapat mengelola produk dan stok, melihat laporan stok, dan memantau inventory. Ada yang bisa saya bantu?";
            } else {
                $response .= "Saya siap membantu Anda dengan pertanyaan tentang sistem POS Koperasi. Silakan tanyakan tentang penjualan, inventory, produk, atau fitur lainnya!";
            }
            return $response;
        }
    }

    private function updateSessionTitle($chatSession, $message)
    {
        // Update title if it's still the default title and this is the first user message
        if ($chatSession->title === 'Chat Baru' && $chatSession->messages()->userMessages()->count() === 1) {
            $title = $this->generateSessionTitle($message);
            $chatSession->update(['title' => $title]);
        }
    }

    private function generateSessionTitle($message)
    {
        // Generate a meaningful title from the first message
        $words = explode(' ', $message);
        $title = implode(' ', array_slice($words, 0, 5)); // Take first 5 words
        
        if (strlen($title) > 50) {
            $title = substr($title, 0, 47) . '...';
        }
        
        return $title ?: 'Chat Baru';
    }

    public function createSession(Request $request)
    {
        try {
            $chatSession = ChatSession::create([
                'session_id' => Str::uuid(),
                'user_id' => auth()->id(),
                'title' => 'Chat Baru',
                'is_active' => true,
                'last_activity' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'session_id' => $chatSession->session_id,
                'redirect_url' => route('admin.ai-chatbot.index', ['session_id' => $chatSession->session_id])
            ]);
        } catch (\Exception $e) {
            Log::error('Create Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat session baru'
            ], 500);
        }
    }

    public function closeSession(Request $request)
    {
        try {
            $sessionId = $request->input('session_id');
            
            $chatSession = ChatSession::where('session_id', $sessionId)
                ->where('user_id', auth()->id())
                ->first();
            
            if ($chatSession) {
                $chatSession->update(['is_active' => false]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Session berhasil ditutup'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Session tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Close Session Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup session'
            ], 500);
        }
    }

    public function getSessions(Request $request)
    {
        try {
            $sessions = ChatSession::forUser(auth()->id())
                ->active()
                ->recent(30)
                ->orderBy('last_activity', 'desc')
                ->limit(20)
                ->get();
            
            return response()->json([
                'success' => true,
                'sessions' => $sessions
            ]);
        } catch (\Exception $e) {
            Log::error('Get Sessions Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar session'
            ], 500);
        }
    }
}
