<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $transactions;

    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'No. Transaksi',
            'Tanggal',
            'Kasir',
            'Produk',
            'Jumlah',
            'Harga Satuan',
            'Subtotal',
            'Total Transaksi',
            'Status',
            'Catatan'
        ];
    }

    public function map($transaction): array
    {
        $rows = [];
        $transaction->load('details.product');
        
        if ($transaction->details->count() > 0) {
            foreach ($transaction->details as $index => $detail) {
                $rows[] = [
                    $index === 0 ? $transaction->transaction_number : '',
                    $index === 0 ? $transaction->created_at->format('d/m/Y H:i') : '',
                    $index === 0 ? ($transaction->user->name ?? 'N/A') : '',
                    $detail->product->name ?? 'N/A',
                    $detail->quantity,
                    'Rp ' . number_format($detail->price, 0, ',', '.'),
                    'Rp ' . number_format($detail->subtotal, 0, ',', '.'),
                    $index === 0 ? 'Rp ' . number_format($transaction->total_amount, 0, ',', '.') : '',
                    $index === 0 ? ucfirst($transaction->status) : '',
                    $index === 0 ? ($transaction->notes ?? '') : ''
                ];
            }
        } else {
            $rows[] = [
                $transaction->transaction_number,
                $transaction->created_at->format('d/m/Y H:i'),
                $transaction->user->name ?? 'N/A',
                'Tidak ada detail',
                '',
                '',
                '',
                'Rp ' . number_format($transaction->total_amount, 0, ',', '.'),
                ucfirst($transaction->status),
                $transaction->notes ?? ''
            ];
        }
        
        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}