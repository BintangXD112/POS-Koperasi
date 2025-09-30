<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Koperasi</title>
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
        .status-completed {
            color: #28a745;
        }
        .status-pending {
            color: #ffc107;
        }
        .status-cancelled {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Koperasi</h1>
        <p>Periode: {{ $month ? date('F', mktime(0, 0, 0, (int)$month, 1)) . ' ' . $year : 'Tahun ' . $year }}</p>
        <p>Tanggal Export: {{ date('d/m/Y H:i') }}</p>
    </div>

    @forelse($transactions as $transaction)
        <div style="margin-bottom: 20px; border: 1px solid #ddd; padding: 10px;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <div>
                    <strong>No. Transaksi:</strong> {{ $transaction->transaction_number }}<br>
                    <strong>Kasir:</strong> {{ $transaction->user->name ?? 'N/A' }}<br>
                    <strong>Tanggal:</strong> {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
                    <strong>Status:</strong> <span class="status-{{ $transaction->status }}">{{ ucfirst($transaction->status) }}</span>
                </div>
                <div style="text-align: right;">
                    <strong>Total: Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong>
                </div>
            </div>
            
            @if($transaction->notes)
                <div style="margin-bottom: 10px;">
                    <strong>Catatan:</strong> {{ $transaction->notes }}
                </div>
            @endif
            
            <table style="width: 100%; margin-top: 10px;">
                <thead>
                    <tr>
                        <th style="text-align: left;">Produk</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th style="text-align: right;">Harga Satuan</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaction->details as $detail)
                        <tr>
                            <td>{{ $detail->product->name ?? 'N/A' }}</td>
                            <td style="text-align: center;">{{ $detail->quantity }}</td>
                            <td style="text-align: right;">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                            <td style="text-align: right;">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center;">Tidak ada detail produk</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 20px;">
            <p>Tidak ada data transaksi</p>
        </div>
    @endforelse

    @if($transactions->count() > 0)
        <div style="margin-top: 20px; text-align: right;">
            <p><strong>Total Transaksi: {{ $transactions->count() }}</strong></p>
            <p><strong>Total Pendapatan: Rp {{ number_format($transactions->sum('total_amount'), 0, ',', '.') }}</strong></p>
        </div>
    @endif
</body>
</html>
