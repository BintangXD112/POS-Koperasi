@extends('layouts.app')

@section('title', 'Buat Analisis Pasar')
@section('subtitle', 'Generate analisis mendalam dengan kecerdasan buatan')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Header Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 via-blue-600 to-indigo-700 rounded-3xl p-8 text-white shadow-2xl">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/5 rounded-full"></div>
        
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-2 h-8 bg-white/30 rounded-full"></div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Buat Analisis Pasar dengan AI</h1>
                </div>
                <p class="text-blue-100/90 text-lg mb-6 max-w-2xl">Pilih tipe analisis dan periode data untuk mendapatkan insight mendalam dari Google Gemini AI</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">Powered by Gemini AI</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <span class="text-white/80">4 Tipe Analisis</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 sm:mt-0 sm:ml-8">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Konfigurasi Analisis</h3>
            <p class="text-sm text-gray-600">Pilih pengaturan untuk analisis pasar Anda</p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('admin.market-analysis.generate') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Form Fields -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Analysis Type -->
                    <div class="space-y-2">
                        <label for="analysis_type" class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                Tipe Analisis
                            </div>
                        </label>
                        <select name="analysis_type" id="analysis_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 @error('analysis_type') border-red-300 @enderror" required>
                            <option value="">Pilih tipe analisis</option>
                            <option value="profit_analysis" {{ old('analysis_type') == 'profit_analysis' ? 'selected' : '' }}>
                                📊 Analisis Profit & Laba
                            </option>
                            <option value="market_trend" {{ old('analysis_type') == 'market_trend' ? 'selected' : '' }}>
                                📈 Trend Pasar & Produk Terlaris
                            </option>
                            <option value="product_performance" {{ old('analysis_type') == 'product_performance' ? 'selected' : '' }}>
                                🎯 Performa Produk & Kategori
                            </option>
                            <option value="customer_behavior" {{ old('analysis_type') == 'customer_behavior' ? 'selected' : '' }}>
                                👥 Perilaku Pelanggan & Pola Pembelian
                            </option>
                        </select>
                        @error('analysis_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Period -->
                    <div class="space-y-2">
                        <label for="period" class="block text-sm font-medium text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Periode Analisis
                            </div>
                        </label>
                        <select name="period" id="period" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 @error('period') border-red-300 @enderror" required>
                            <option value="">Pilih periode</option>
                            <option value="7" {{ old('period') == '7' ? 'selected' : '' }}>
                                📅 7 Hari Terakhir
                            </option>
                            <option value="30" {{ old('period') == '30' ? 'selected' : '' }}>
                                📅 30 Hari Terakhir (1 Bulan)
                            </option>
                            <option value="90" {{ old('period') == '90' ? 'selected' : '' }}>
                                📅 90 Hari Terakhir (3 Bulan)
                            </option>
                            <option value="365" {{ old('period') == '365' ? 'selected' : '' }}>
                                📅 365 Hari Terakhir (1 Tahun)
                            </option>
                        </select>
                        @error('period')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Recommendations Checkbox -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="include_recommendations" id="include_recommendations" 
                               class="w-5 h-5 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2" 
                               value="1" {{ old('include_recommendations') ? 'checked' : 'checked' }}>
                        <label for="include_recommendations" class="ml-3 text-sm font-medium text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                Sertakan rekomendasi strategis dari AI
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-gray-600 mt-2 ml-8">AI akan memberikan saran strategis untuk meningkatkan performa bisnis Anda</p>
                </div>

                <!-- Preview Cards -->
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Preview Analisis
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Profit Analysis Preview -->
                        <div class="analysis-preview border-2 border-transparent rounded-xl p-4 transition-all duration-300 hover:shadow-lg" data-type="profit_analysis" style="display: none;">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">💰</span>
                                </div>
                                <h6 class="font-semibold text-gray-900 mb-2">Analisis Profit</h6>
                                <p class="text-xs text-gray-600">Analisis pendapatan, profit per kategori, dan efisiensi bisnis</p>
                            </div>
                        </div>
                        
                        <!-- Market Trend Preview -->
                        <div class="analysis-preview border-2 border-transparent rounded-xl p-4 transition-all duration-300 hover:shadow-lg" data-type="market_trend" style="display: none;">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-blue-100 to-cyan-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">📈</span>
                                </div>
                                <h6 class="font-semibold text-gray-900 mb-2">Trend Pasar</h6>
                                <p class="text-xs text-gray-600">Produk terlaris, trend kategori, dan peluang pasar</p>
                            </div>
                        </div>
                        
                        <!-- Product Performance Preview -->
                        <div class="analysis-preview border-2 border-transparent rounded-xl p-4 transition-all duration-300 hover:shadow-lg" data-type="product_performance" style="display: none;">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-yellow-100 to-orange-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">🎯</span>
                                </div>
                                <h6 class="font-semibold text-gray-900 mb-2">Performa Produk</h6>
                                <p class="text-xs text-gray-600">Analisis performa produk, kategori terbaik, dan optimasi</p>
                            </div>
                        </div>
                        
                        <!-- Customer Behavior Preview -->
                        <div class="analysis-preview border-2 border-transparent rounded-xl p-4 transition-all duration-300 hover:shadow-lg" data-type="customer_behavior" style="display: none;">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl">👥</span>
                                </div>
                                <h6 class="font-semibold text-gray-900 mb-2">Perilaku Pelanggan</h6>
                                <p class="text-xs text-gray-600">Pola pembelian, segmentasi customer, dan jam sibuk</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AI Info -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Powered by Gemini AI</h4>
                            <p class="text-sm text-gray-700">Analisis akan dibuat menggunakan kecerdasan buatan Google Gemini untuk memberikan insight mendalam dan rekomendasi strategis yang dapat membantu meningkatkan performa bisnis Anda.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        Generate Analisis dengan AI
                    </button>
                    <a href="{{ route('admin.market-analysis.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-4 bg-gray-100 text-gray-700 font-semibold rounded-xl shadow-sm hover:shadow-md hover:bg-gray-200 transition-all duration-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const analysisTypeSelect = document.getElementById('analysis_type');
    const previewCards = document.querySelectorAll('.analysis-preview');
    
    function showPreview() {
        // Hide all previews
        previewCards.forEach(card => {
            card.style.display = 'none';
        });
        
        // Show selected preview
        const selectedType = analysisTypeSelect.value;
        if (selectedType) {
            const selectedCard = document.querySelector(`[data-type="${selectedType}"]`);
            if (selectedCard) {
                selectedCard.style.display = 'block';
                // Add highlight effect
                selectedCard.classList.add('border-purple-300', 'bg-purple-50');
            }
        }
    }
    
    analysisTypeSelect.addEventListener('change', showPreview);
    
    // Show preview on page load if there's a selected value
    showPreview();
});
</script>
@endpush
