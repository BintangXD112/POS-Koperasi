@extends('layouts.app')

@section('title', 'Predictive Analytics')
@section('subtitle', 'Prediksi penjualan dan forecasting dengan AI')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-700 rounded-3xl p-8 text-white shadow-2xl">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/5 rounded-full"></div>
        
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-2 h-8 bg-white/30 rounded-full"></div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Predictive Analytics</h1>
                </div>
                <p class="text-indigo-100/90 text-lg mb-6 max-w-2xl">Prediksi penjualan masa depan dengan analisis AI dan forecasting</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-pink-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">AI Powered</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        <span class="text-white/80">Forecasting</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 sm:mt-0 sm:ml-8">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                    <span class="text-4xl">🔮</span>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Forecast Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        AI Forecast & Predictions
                    </h3>
                    <p class="text-sm text-gray-600">Prediksi dan insight strategis berdasarkan data historis</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select id="forecast-period" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="7">7 Hari</option>
                        <option value="30" selected>30 Hari</option>
                        <option value="90">90 Hari</option>
                    </select>
                    <button onclick="generateForecast()" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow-sm hover:bg-indigo-700 transition-all duration-300">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Generate AI
                    </button>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div id="ai-forecast" class="space-y-6">
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                    <p>Pilih periode dan klik "Generate AI" untuk mendapatkan prediksi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sales Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenue 30 Hari</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($salesData['total_revenue_30_days'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Transaksi 30 Hari</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($salesData['total_transactions_30_days']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Revenue Harian</p>
                    <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($salesData['avg_daily_revenue'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Avg Transaksi Harian</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($salesData['avg_daily_transactions'], 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Forecast Data -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Forecast Summary -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Forecast Summary
                </h3>
                <p class="text-sm text-gray-600">Prediksi 30 hari ke depan</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">💰</span>
                            </div>
                            <span class="font-semibold text-gray-900">Prediksi Revenue</span>
                        </div>
                        <span class="text-2xl font-bold text-green-600">Rp {{ number_format($forecastData['total_predicted_revenue'], 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">📊</span>
                            </div>
                            <span class="font-semibold text-gray-900">Prediksi Transaksi</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-600">{{ number_format($forecastData['total_predicted_transactions']) }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-sm">📈</span>
                            </div>
                            <span class="font-semibold text-gray-900">Growth Rate</span>
                        </div>
                        <span class="text-2xl font-bold text-purple-600">{{ $forecastData['growth_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Top 5 Produk Terlaris
                </h3>
                <p class="text-sm text-gray-600">30 hari terakhir</p>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($salesData['product_sales']->take(5) as $index => $product)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold text-sm">{{ $index + 1 }}</span>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-900">{{ $product->product->name }}</span>
                                    <p class="text-xs text-gray-600">{{ $product->total_sold }} unit terjual</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-blue-600">Rp {{ number_format($product->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Forecast Chart -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Forecast Chart (7 Hari Terakhir)
            </h3>
            <p class="text-sm text-gray-600">Prediksi revenue harian</p>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                @foreach($forecastData['forecast']->take(7) as $forecast)
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white font-semibold text-xs">{{ \Carbon\Carbon::parse($forecast['date'])->format('d') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($forecast['date'])->format('l, d M') }}</span>
                                <p class="text-xs text-gray-600">{{ $forecast['predicted_transactions'] }} transaksi</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($forecast['predicted_revenue'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function generateForecast() {
    const button = event.target;
    const originalText = button.innerHTML;
    const period = document.getElementById('forecast-period').value;
    
    // Show loading state
    button.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Loading...';
    button.disabled = true;
    
    fetch('{{ route("admin.predictive-analytics.generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            period: period
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayForecast(data.forecast);
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

function displayForecast(forecast) {
    const container = document.getElementById('ai-forecast');
    
    if (forecast && forecast.predictions && forecast.recommendations) {
        container.innerHTML = `
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Prediksi AI
                    </h4>
                    <div class="space-y-3">
                        ${forecast.predictions.map((prediction, index) => `
                            <div class="flex items-start p-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-200">
                                <div class="flex-shrink-0 w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    <span class="text-white font-semibold text-xs">${index + 1}</span>
                                </div>
                                <p class="text-gray-800 text-sm">${prediction}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        Rekomendasi Strategis
                    </h4>
                    <div class="space-y-3">
                        ${forecast.recommendations.map((recommendation, index) => `
                            <div class="flex items-start p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                                <div class="flex-shrink-0 w-6 h-6 bg-green-500 rounded-full flex items-center justify-center mr-3 mt-0.5">
                                    <span class="text-white font-semibold text-xs">${index + 1}</span>
                                </div>
                                <p class="text-gray-800 text-sm">${recommendation}</p>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
    } else {
        container.innerHTML = '<div class="text-center py-8 text-gray-500">Tidak ada prediksi yang tersedia</div>';
    }
}
</script>
@endsection


