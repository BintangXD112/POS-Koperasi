@extends('layouts.app')

@section('title', 'Laporan Sistem')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Laporan Sistem</h1>
            <p class="mt-1 text-sm text-gray-600">Analisis data dan laporan sistem koperasi</p>
        </div>
        <form method="GET" action="{{ route('admin.reports') }}" class="flex items-end space-x-3">
            <div>
                <label class="block text-xs text-gray-600 mb-1">Tahun</label>
                <select name="year" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ (int)($year ?? date('Y')) === $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-600 mb-1">Bulan</label>
                <select name="month" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (string)($month ?? '') === (string)$m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Terapkan</button>
                <a href="{{ route('admin.reports.export', ['year' => $year ?? date('Y'), 'month' => $month ?? '']) }}"
                   class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                    Export CSV
                </a>
            </div>
        </form>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                {{ empty($month) ? 'Pendapatan Bulanan' : 'Pendapatan Harian' }}
                @if(!empty($month))
                    ({{ date('F', mktime(0, 0, 0, (int)$month, 1)) }} {{ $year }})
                @else
                    (Tahun {{ $year }})
                @endif
            </h3>
            <div class="space-y-3">
                @if(empty($month))
                    @forelse($monthlyRevenue as $revenue)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ date('F', mktime(0, 0, 0, intval($revenue->month), 1)) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">
                                    Rp {{ number_format($revenue->revenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada data pendapatan</p>
                    @endforelse
                @else
                    @forelse($dailyRevenue as $revenue)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($revenue->day)->format('d F Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">
                                    Rp {{ number_format($revenue->revenue, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Belum ada data pendapatan</p>
                    @endforelse
                @endif
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Produk Terlaris</h3>
            <div class="space-y-3">
                @forelse($topProducts as $product)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-blue-600">{{ $product->transaction_details_count }} transaksi</p>
                            <p class="text-xs text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-center py-4">Belum ada data produk</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Transaksi</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalTransactionsFiltered }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pendapatan</dt>
                            <dd class="text-lg font-medium text-gray-900">Rp {{ number_format($totalRevenueSum, 0, ',', '.') }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Produk Aktif</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $topProducts->count() }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
