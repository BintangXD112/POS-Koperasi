@extends('layouts.app')

@section('title', 'Dashboard Gudang')
@section('subtitle', 'Panel manajemen gudang sistem koperasi')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Welcome Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-orange-600 via-amber-600 to-yellow-700 rounded-3xl p-8 text-white shadow-2xl">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-white/5 rounded-full"></div>
        
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-2 h-8 bg-white/30 rounded-full"></div>
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">Selamat Datang, {{ auth()->user()->name }}!</h1>
                </div>
                <p class="text-orange-100/90 text-lg mb-6 max-w-2xl">Panel manajemen gudang - Kelola stok dan inventori produk dengan mudah dan efisien</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">Gudang Aktif</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-white/80">{{ now()->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-6 sm:mt-0 sm:ml-8">
                <div class="w-24 h-24 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shadow-xl">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Produk</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Menipis</p>
                            <p class="text-3xl font-bold text-yellow-600">{{ $lowStockProducts }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Habis Stok</p>
                            <p class="text-3xl font-bold text-red-600">{{ $outOfStockProducts }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Kategori</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalCategories }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
        <div class="px-6 py-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-2 h-8 bg-gradient-to-b from-orange-500 to-amber-600 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('gudang.products.create') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:border-green-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 group-hover:text-green-900">Tambah Produk</h4>
                        <p class="text-sm text-gray-600">Buat produk baru</p>
                    </div>
                </a>

                <a href="{{ route('gudang.categories.create') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:border-blue-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-900">Tambah Kategori</h4>
                        <p class="text-sm text-gray-600">Buat kategori baru</p>
                    </div>
                </a>

                <a href="{{ route('gudang.reports.stock') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-purple-50 hover:to-violet-50 hover:border-purple-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 group-hover:text-purple-900">Laporan Stok</h4>
                        <p class="text-sm text-gray-600">Lihat laporan stok</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Products & Low Stock -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Enhanced Recent Products -->
        <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-900">Produk Terbaru</h3>
                    </div>
                    <a href="{{ route('gudang.products') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        Lihat Semua
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($recentProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $product->stock }} stok</p>
                                <p class="text-xs text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada produk</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Low Stock Products -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Produk Stok Menipis</h3>
                    <!-- <a href="{{ route('gudang.products') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">Kelola Stok</a> -->
                <div class="space-y-3">
                    @forelse($lowStockList as $product)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $product->category->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-red-600">{{ $product->stock }} stok</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Semua produk memiliki stok yang cukup</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stock Management Tips -->
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-2 border-yellow-200 rounded-2xl p-6 shadow-lg">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-yellow-900 mb-3">Tips Manajemen Stok</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yellow-800">Periksa stok secara berkala untuk menghindari kehabisan</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yellow-800">Gunakan fitur penyesuaian stok untuk koreksi</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yellow-800">Kelompokkan produk berdasarkan kategori untuk memudahkan pengelolaan</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                        <span class="text-sm font-medium text-yellow-800">Buat laporan stok secara rutin untuk analisis</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
