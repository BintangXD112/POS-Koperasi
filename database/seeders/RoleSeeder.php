<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Super user dengan akses penuh ke semua fitur'
        ]);

        Role::create([
            'name' => 'kasir',
            'display_name' => 'Kasir',
            'description' => 'User untuk melakukan transaksi penjualan'
        ]);

        Role::create([
            'name' => 'gudang',
            'display_name' => 'Gudang',
            'description' => 'User untuk mengelola stok dan produk'
        ]);
    }
}

