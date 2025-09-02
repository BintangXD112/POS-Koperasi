# Sistem Koperasi - Laravel Application

Aplikasi sistem koperasi yang dibangun dengan Laravel 11, menampilkan manajemen user dengan 3 role berbeda: Admin, Kasir, dan Gudang.

## 🚀 Fitur Utama

### 👑 Admin
- Dashboard dengan statistik lengkap
- Manajemen user (CRUD)
- Laporan pendapatan dan transaksi
- Monitoring stok produk
- Akses penuh ke semua fitur sistem

### 💰 Kasir
- Dashboard dengan statistik harian
- Point of Sale (POS) untuk transaksi
- Pencarian produk cepat
- Riwayat transaksi
- Pembatalan transaksi

### 📦 Gudang
- Dashboard dengan monitoring stok
- Manajemen produk (CRUD)
- Manajemen kategori produk
- Penyesuaian stok
- Laporan stok dan produk

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Database**: MySQL
- **Frontend**: Tailwind CSS + Alpine.js
- **Authentication**: Laravel Built-in Auth
- **Middleware**: Custom Role-based Access Control

## 📋 Persyaratan Sistem

- PHP 8.2+
- Composer
- MySQL 5.7+
- Web Server (Apache/Nginx) atau PHP Built-in Server

## 🚀 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd Koperasi
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database
Edit file `.env` dan sesuaikan konfigurasi database:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi_db
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan Migration dan Seeder
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`

## 🔐 Login Default

Setelah menjalankan seeder, gunakan akun default:

**Admin:**
- Email: `admin@koperasi.com`
- Password: `password`

## 📊 Struktur Database

### Tabel Utama:
- `users` - Data pengguna sistem
- `roles` - Role pengguna (admin, kasir, gudang)
- `categories` - Kategori produk
- `products` - Data produk dengan stok
- `transactions` - Header transaksi
- `transaction_details` - Detail item transaksi

### Relasi:
- User → Role (belongsTo)
- Product → Category (belongsTo)
- Transaction → User (belongsTo)
- Transaction → TransactionDetail (hasMany)
- Product → TransactionDetail (hasMany)

## 🛣️ Routes

### Admin Routes (`/admin`)
- `GET /admin/dashboard` - Dashboard admin
- `GET /admin/users` - Manajemen user
- `GET /admin/users/create` - Form tambah user
- `POST /admin/users` - Simpan user baru
- `GET /admin/users/{id}/edit` - Form edit user
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Hapus user
- `GET /admin/reports` - Laporan sistem

### Kasir Routes (`/kasir`)
- `GET /kasir/dashboard` - Dashboard kasir
- `GET /kasir/pos` - Point of Sale
- `POST /kasir/transactions` - Buat transaksi
- `GET /kasir/transactions` - Riwayat transaksi
- `GET /kasir/transactions/{id}` - Detail transaksi
- `POST /kasir/transactions/{id}/cancel` - Batalkan transaksi
- `GET /kasir/search-products` - Pencarian produk

### Gudang Routes (`/gudang`)
- `GET /gudang/dashboard` - Dashboard gudang
- `GET /gudang/products` - Manajemen produk
- `GET /gudang/products/create` - Form tambah produk
- `POST /gudang/products` - Simpan produk baru
- `GET /gudang/products/{id}/edit` - Form edit produk
- `PUT /gudang/products/{id}` - Update produk
- `DELETE /gudang/products/{id}` - Hapus produk
- `POST /gudang/products/{id}/adjust-stock` - Penyesuaian stok
- `GET /gudang/categories` - Manajemen kategori
- `GET /gudang/reports/stock` - Laporan stok

## 🔒 Middleware

### CheckRole Middleware
Middleware custom untuk mengecek role user:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Routes untuk admin
});
```

## 🎨 Frontend

### Layout
- Responsive design dengan Tailwind CSS
- Sidebar navigation yang dapat di-collapse
- Mobile-friendly interface

### Components
- Dashboard cards dengan statistik
- Data tables dengan pagination
- Form inputs dengan validation
- Alert messages untuk feedback

## 📱 Responsive Design

Aplikasi didesain responsif untuk berbagai ukuran layar:
- Desktop (lg+): Sidebar tetap terbuka
- Tablet (md): Sidebar dapat di-toggle
- Mobile (sm): Sidebar overlay

## 🚀 Deployment

### Production
1. Set `APP_ENV=production` di `.env`
2. Set `APP_DEBUG=false`
3. Optimize Laravel:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Security
- Gunakan HTTPS di production
- Set `SESSION_SECURE_COOKIE=true`
- Gunakan strong password untuk database
- Regular security updates

## 🐛 Troubleshooting

### Common Issues:

1. **Migration Error**
   ```bash
   php artisan migrate:fresh --seed
   ```

2. **Permission Error**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

3. **Composer Autoload**
   ```bash
   composer dump-autoload
   ```

## 📝 Contributing

1. Fork repository
2. Buat feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Support

Untuk dukungan teknis atau pertanyaan, silakan buat issue di repository ini.

---

**Dibuat dengan ❤️ menggunakan Laravel 11**
