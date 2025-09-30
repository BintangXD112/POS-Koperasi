<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .status-safe {
            color: #28a745;
        }
        .status-low {
            color: #ffc107;
        }
        .status-out {
            color: #dc3545;
        }
        .summary {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Stok Produk</h1>
        <p>Tanggal Export: {{ date('d/m/Y H:i') }}</p>
        @if(request('category'))
            <p>Kategori: {{ \App\Models\Category::find(request('category'))->name ?? 'Tidak ditemukan' }}</p>
        @endif
        @if(request('stock_status'))
            <p>Status: {{ request('stock_status') === 'safe' ? 'Aman' : (request('stock_status') === 'low' ? 'Menipis' : 'Habis') }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Produk</th>
                <th>SKU</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->category->name ?? 'Tanpa Kategori' }}</td>
                    <td class="text-right">{{ $product->stock }}</td>
                    <td class="text-right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="status-{{ $product->stock > 10 ? 'safe' : ($product->stock > 0 ? 'low' : 'out') }}">
                        {{ $product->stock > 10 ? 'Aman' : ($product->stock > 0 ? 'Menipis' : 'Habis') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data produk</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($products->count() > 0)
        <div class="summary">
            <h3>Ringkasan Laporan Stok</h3>
            <p><strong>Total Produk:</strong> {{ $products->count() }}</p>
            <p><strong>Stok Aman (>10):</strong> {{ $products->where('stock', '>', 10)->count() }}</p>
            <p><strong>Stok Menipis (1-10):</strong> {{ $products->where('stock', '>', 0)->where('stock', '<=', 10)->count() }}</p>
            <p><strong>Stok Habis (0):</strong> {{ $products->where('stock', 0)->count() }}</p>
            <p><strong>Total Nilai Stok:</strong> Rp {{ number_format($products->sum(function($product) { return $product->stock * $product->price; }), 0, ',', '.') }}</p>
        </div>
    @endif
</body>
</html>
