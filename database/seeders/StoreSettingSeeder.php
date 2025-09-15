<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StoreSetting;

class StoreSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StoreSetting::create([
            'store_name' => 'Koperasi Sejahtera',
            'store_address' => 'Jl. Raya Koperasi No. 123\nKelurahan Koperasi, Kecamatan Sejahtera\nKota Jakarta Selatan 12345',
            'store_phone' => '+62 21 1234 5678',
            'store_email' => 'info@koperasisejahtera.com',
            'store_owner' => 'Budi Santoso',
            'store_website' => 'https://koperasisejahtera.com',
            'store_description' => 'Koperasi Sejahtera adalah koperasi yang berfokus pada pemberdayaan ekonomi masyarakat melalui layanan simpan pinjam dan unit usaha yang berkelanjutan.',
            'currency' => 'IDR',
            'timezone' => 'Asia/Jakarta',
            'tax_number' => 'NPWP-123456789012345',
            'footer_text' => '© 2024 Koperasi Sejahtera. Semua hak dilindungi undang-undang.',
            'is_active' => true,
        ]);
    }
}