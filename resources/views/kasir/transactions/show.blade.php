@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Transaksi</h1>
            <p class="mt-1 text-sm text-gray-600">Informasi lengkap transaksi {{ $transaction->transaction_number }}</p>
        </div>
        <a href="{{ route('kasir.transactions') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Transaction Info -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Informasi Transaksi</h3>
                
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">No. Transaksi:</span>
                        <span class="text-sm text-gray-900">{{ $transaction->transaction_number }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Tanggal:</span>
                        <span class="text-sm text-gray-900">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 
                               ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between">
                        <span class="text-sm font-medium text-gray-500">Total:</span>
                        <span class="text-lg font-bold text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($transaction->notes)
                        <div class="pt-4 border-t">
                            <span class="text-sm font-medium text-gray-500">Catatan:</span>
                            <p class="text-sm text-gray-900 mt-1">{{ $transaction->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Detail Produk</h3>
                
                <div class="space-y-3">
                    @forelse($transaction->details as $detail)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-md">
                            <div>
                                <p class="font-medium text-gray-900">{{ $detail->product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $detail->product->category->name ?? 'Tanpa Kategori' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $detail->quantity }} x Rp {{ number_format($detail->price, 0, ',', '.') }}</p>
                                <p class="text-sm font-bold text-gray-900">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada detail produk</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($transaction->status === 'completed')
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Aksi</h3>
                
                <form action="{{ route('kasir.transactions.cancel', $transaction) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" 
                            onclick="return confirm('Yakin ingin membatalkan transaksi ini? Stok akan dikembalikan.')">
                        Batalkan Transaksi
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
