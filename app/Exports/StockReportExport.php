<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $products;

    public function __construct($products)
    {
        $this->products = $products;
    }

    public function collection()
    {
        return $this->products;
    }

    public function headings(): array
    {
        return [
            'Nama Produk',
            'SKU',
            'Kategori',
            'Stok',
            'Harga',
            'Status Stok',
            'Tanggal Dibuat',
            'Tanggal Diupdate'
        ];
    }

    public function map($product): array
    {
        $status = $product->stock > 10 ? 'Aman' : ($product->stock > 0 ? 'Menipis' : 'Habis');
        
        return [
            $product->name,
            $product->sku,
            $product->category->name ?? 'Tanpa Kategori',
            $product->stock,
            'Rp ' . number_format($product->price, 0, ',', '.'),
            $status,
            $product->created_at->format('d/m/Y H:i'),
            $product->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}