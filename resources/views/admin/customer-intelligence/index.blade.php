@extends('layouts.app')

@section('title', 'Customer Intelligence')
@section('subtitle', 'Analisis pelanggan cerdas dengan AI')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 via-pink-600 to-rose-700 rounded-3xl p-8 text-white shadow-2xl">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/5 rounded-full"></div>
        
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-2 h-8 bg-white/30 rounded-full"></div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Customer Intelligence</h1>
                </div>
                <p class="text-purple-100/90 text-lg mb-6 max-w-2xl">Analisis mendalam pelanggan dengan segmentasi dan insight AI</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-pink-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">AI Powered</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                        <span class="text-white/80">{{ $customerData['total_customers'] }} Pelanggan</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 sm:mt-0 sm:ml-8">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-4xl">👥</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Customer Insights
                    </h3>
                    <p class="text-sm text-gray-600">Insight strategis untuk meningkatkan customer engagement</p>
                </div>
                <button onclick="generateInsights()" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-semibold rounded-lg shadow-sm hover:bg-purple-700 transition-all duration-300">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Generate AI
                </button>
            </div>
        </div>
        <div class="p-6">
            <div id="ai-insights" class="space-y-4">
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <p>Klik "Generate AI" untuk mendapatkan insight customer</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pelanggan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $customerData['total_customers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pelanggan Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ $customerData['active_customers'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Customer Value</p>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($customerData['avg_customer_value'], 1) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">VIP Customers</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $segmentationData['vip'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Segmentation -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Segmentation Chart -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Customer Segmentation
                </h3>
                <p class="text-sm text-gray-600">Distribusi pelanggan berdasarkan segmentasi</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl border border-yellow-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">👑</span>
                            </div>
                            <span class="font-semibold text-gray-900">VIP Customers</span>
                        </div>
                        <span class="text-2xl font-bold text-yellow-600">{{ $segmentationData['vip'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">💎</span>
                            </div>
                            <span class="font-semibold text-gray-900">Loyal Customers</span>
                        </div>
                        <span class="text-2xl font-bold text-green-600">{{ $segmentationData['loyal'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-red-50 to-pink-50 rounded-xl border border-red-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">⚠️</span>
                            </div>
                            <span class="font-semibold text-gray-900">At Risk</span>
                        </div>
                        <span class="text-2xl font-bold text-red-600">{{ $segmentationData['at_risk'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">🆕</span>
                            </div>
                            <span class="font-semibold text-gray-900">New Customers</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">{{ $segmentationData['new'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-xl border border-gray-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">😴</span>
                            </div>
                            <span class="font-semibold text-gray-900">Inactive</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-600">{{ $segmentationData['inactive'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Customers -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Top 5 Customers
                </h3>
                <p class="text-sm text-gray-600">Pelanggan dengan nilai tertinggi</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($customerData['top_customers']->take(5) as $index => $customer)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $customer['name'] }}</span>
                                    <p class="text-xs text-gray-600">{{ $customer['transaction_count'] }} transaksi</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($customer['total_spent'], 0, ',', '.') }}</span>
                                <p class="text-xs text-gray-600">Value: {{ $customer['customer_value'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generateInsights() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Loading...';
    button.disabled = true;
    
    fetch('{{ route("admin.customer-intelligence.generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayInsights(data.insights);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses permintaan');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function displayInsights(insights) {
    const container = document.getElementById('ai-insights');
    
    if (insights && insights.insights) {
        container.innerHTML = insights.insights.map((insight, index) => `
            <div class="flex items-start p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                <div class="flex-shrink-0 w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-4">
                    <span class="text-white font-semibold text-sm">${index + 1}</span>
                </div>
                <div class="flex-1">
                    <p class="text-gray-800 leading-relaxed">${insight}</p>
                </div>
            </div>
        `).join('');
    } else {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada insight yang tersedia</div>';
    }
}
</script>
@endsection

