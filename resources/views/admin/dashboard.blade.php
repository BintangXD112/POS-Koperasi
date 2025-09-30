@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('subtitle', 'Panel administrasi sistem koperasi')

@section('content') 
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Welcome Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-3xl p-8 text-white shadow-2xl">
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
                <p class="text-blue-100/90 text-lg mb-6 max-w-2xl">Panel administrasi sistem koperasi - Kelola semua aspek bisnis Anda dengan mudah dan efisien</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">Sistem Online</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalUsers }}</p>
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
                        <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
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
                        <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Transaksi</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $totalTransactions }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Pendapatan</p>
                            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Recent Transactions & Low Stock -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Enhanced Recent Transactions -->
        <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-8 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-900">Transaksi Terbaru</h3>
                    </div>
                    <a href="{{ route('kasir.transactions') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-all duration-200">
                        Lihat Semua
                        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-gray-100/50 rounded-xl hover:from-blue-50 hover:to-indigo-50 transition-all duration-300 border border-gray-200/50 hover:border-blue-200">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $transaction->transaction_number }}</p>
                                    <p class="text-xs text-gray-600">{{ $transaction->user->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                    {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                       ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada transaksi</h3>
                            <p class="mt-1 text-sm text-gray-500">Transaksi akan muncul di sini</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Enhanced Low Stock Products -->
        <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="px-6 py-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-2 h-8 bg-gradient-to-b from-red-500 to-rose-600 rounded-full"></div>
                        <h3 class="text-xl font-bold text-gray-900">Produk Stok Menipis</h3>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-red-600 font-medium">Perhatian</span>
                    </div>
                </div>
                <div class="space-y-4">
                    @forelse($lowStockProducts as $product)
                        <div class="flex items-center justify-between p-5 bg-gradient-to-r from-red-50 to-rose-50 rounded-xl hover:from-red-100 hover:to-rose-100 transition-all duration-300 border border-red-200/50 hover:border-red-300">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center shadow-md">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-600">{{ $product->category->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">{{ $product->stock }} stok</p>
                                <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Stok Aman</h3>
                            <p class="mt-1 text-sm text-gray-500">Semua produk memiliki stok yang cukup</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
        <div class="px-6 py-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-2 h-8 bg-gradient-to-b from-purple-500 to-pink-600 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="{{ route('admin.users.create') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:border-blue-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-base font-bold text-gray-900 group-hover:text-blue-900">Tambah User</p>
                        <p class="text-sm text-gray-600">Buat user baru</p>
                    </div>
                </a>

                <a href="{{ route('admin.reports') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:border-green-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-base font-bold text-gray-900 group-hover:text-green-900">Lihat Laporan</p>
                        <p class="text-sm text-gray-600">Analisis data</p>
                    </div>
                </a>

                <a href="{{ route('admin.users') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-purple-50 hover:to-violet-50 hover:border-purple-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-base font-bold text-gray-900 group-hover:text-purple-900">Kelola User</p>
                        <p class="text-sm text-gray-600">Edit dan hapus user</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
