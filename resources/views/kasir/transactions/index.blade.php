@extends('layouts.app')

@section('title', 'Riwayat Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Riwayat Transaksi</h1>
            <p class="mt-1 text-sm text-gray-600">Lihat semua transaksi yang telah dibuat</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('kasir.transactions.export', array_merge(request()->query(), ['format' => 'excel'])) }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Excel
            </a>
            <a href="{{ route('kasir.transactions.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('kasir.transactions') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                        <select name="period" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua Periode</option>
                            <option value="today" {{ request('period') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="week" {{ request('period') === 'week' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="month" {{ request('period') === 'month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="custom" {{ request('period') === 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                    
                    <div id="custom-date-range" class="md:col-span-2 {{ request('period') === 'custom' ? '' : 'hidden' }}">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Filter
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['period', 'start_date', 'end_date']))
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600">
                            @if(request('period') === 'today')
                                Menampilkan transaksi hari ini
                            @elseif(request('period') === 'week')
                                Menampilkan transaksi minggu ini
                            @elseif(request('period') === 'month')
                                Menampilkan transaksi bulan ini
                            @elseif(request('period') === 'custom' && request('start_date') && request('end_date'))
                                Menampilkan transaksi dari {{ \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') }} sampai {{ \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') }}
                            @endif
                        </div>
                        <a href="{{ route('kasir.transactions') }}" class="text-sm text-red-600 hover:text-red-800">
                            Hapus Filter
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    @if($transactions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Transaksi</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $transactions->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Pendapatan</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-100 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Rata-rata per Transaksi</p>
                        <p class="text-2xl font-semibold text-gray-900">Rp {{ number_format($transactions->avg('total_amount'), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Transaksi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($transactions as $transaction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->transaction_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('kasir.transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900 mr-3">Detail</a>
                                    @if($transaction->status === 'completed')
                                        <a href="{{ route('kasir.transactions.print', $transaction) }}" target="_blank" class="text-green-600 hover:text-green-800 mr-3">Cetak</a>
                                        <form action="{{ route('kasir.transactions.cancel', $transaction) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin membatalkan transaksi ini?')">Batalkan</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada transaksi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($transactions->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.querySelector('select[name="period"]');
    const customDateRange = document.getElementById('custom-date-range');
    
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection
