@extends('layouts.app')

@section('title', 'Dashboard Kasir')
@section('subtitle', 'Panel kasir sistem koperasi')

@section('content')
<div class="space-y-8 animate-fade-in-up">
    <!-- Enhanced Welcome Section -->
    <div class="relative overflow-hidden bg-gradient-to-br from-green-600 via-emerald-600 to-teal-700 rounded-3xl p-8 text-white shadow-2xl">
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
                <p class="text-green-100/90 text-lg mb-6 max-w-2xl">Panel kasir - Kelola transaksi dan layanan pelanggan dengan mudah dan efisien</p>
                <div class="flex items-center space-x-6 text-sm">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-white/80">Kasir Aktif</span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow-xl rounded-2xl card-hover border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Transaksi Hari Ini</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $todayTransactions }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pendapatan Hari Ini</p>
                            <p class="text-3xl font-bold text-gray-900">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Status</p>
                            <p class="text-3xl font-bold text-green-600">Aktif</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Quick Actions -->
    <div class="bg-white shadow-xl rounded-2xl card-hover border border-gray-100">
        <div class="px-6 py-6">
            <div class="flex items-center space-x-3 mb-8">
                <div class="w-2 h-8 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900">Aksi Cepat</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('kasir.pos') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-green-50 hover:to-emerald-50 hover:border-green-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 group-hover:text-green-900">Point of Sale</h4>
                        <p class="text-sm text-gray-600">Buat transaksi baru untuk pelanggan</p>
                    </div>
                </a>

                <a href="{{ route('kasir.transactions') }}" class="flex items-center p-6 border-2 border-gray-200 rounded-2xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:border-blue-300 transition-all duration-300 group shadow-sm hover:shadow-lg">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-bold text-gray-900 group-hover:text-blue-900">Riwayat Transaksi</h4>
                        <p class="text-sm text-gray-600">Lihat semua transaksi yang telah dibuat</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

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
            <div class="space-y-3">
                @forelse($recentTransactions as $transaction)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-md hover:bg-gray-100 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
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
                        <p class="mt-1 text-sm text-gray-500">Mulai dengan membuat transaksi baru di Point of Sale</p>
                        <div class="mt-6">
                            <a href="{{ route('kasir.pos') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Buat Transaksi
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Enhanced Tips -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6 shadow-lg">
        <div class="flex items-start space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-blue-900 mb-3">Tips Kasir</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm font-medium text-blue-800">Gunakan fitur pencarian produk untuk transaksi yang cepat</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm font-medium text-blue-800">Periksa stok sebelum melakukan transaksi</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm font-medium text-blue-800">Simpan struk transaksi untuk referensi</span>
                    </div>
                    <div class="flex items-center space-x-3 p-3 bg-white/60 rounded-lg">
                        <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                        <span class="text-sm font-medium text-blue-800">Gunakan fitur pembatalan jika ada kesalahan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
