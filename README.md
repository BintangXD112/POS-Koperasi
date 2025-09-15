# Sistem Koperasi - Laravel Application

Aplikasi sistem koperasi yang dibangun dengan Laravel 11, menampilkan manajemen user dengan 3 role berbeda: Admin, Kasir, dan Gudang. Dilengkapi dengan fitur keamanan lengkap, monitoring aktivitas, dan interface yang modern.

## 🚀 Fitur Utama

### 👑 Admin
- Dashboard dengan statistik lengkap
- Manajemen user (CRUD) dengan validasi lengkap
- Laporan pendapatan dan transaksi
- Monitoring stok produk
- **Log Aktivitas** - Pantau semua aktivitas user
- **Export Data** - Export laporan dalam format CSV
- Akses penuh ke semua fitur sistem

### 💰 Kasir
- Dashboard dengan statistik harian
- Point of Sale (POS) untuk transaksi
- Pencarian produk cepat
- Riwayat transaksi
- Pembatalan transaksi
- **Edit Profil** - Kelola informasi pribadi

### 📦 Gudang
- Dashboard dengan monitoring stok
- Manajemen produk (CRUD)
- Manajemen kategori produk
- Penyesuaian stok
- Laporan stok dan produk
- **Edit Profil** - Kelola informasi pribadi

### 🔐 Fitur Keamanan & Monitoring
- **Activity Logging** - Semua aktivitas user tercatat
- **Session Management** - Kontrol sesi aktif
- **Role-based Access Control** - Akses berdasarkan role
- **Password Security** - Show/hide password dengan validasi
- **Login Monitoring** - Pantau login berhasil/gagal
- **Profile Management** - User dapat edit profil sendiri

## 🛠️ Teknologi yang Digunakan

- **Backend**: Laravel 11
- **Database**: Sqlite
- **Frontend**: Tailwind CSS + Alpine.js
- **Authentication**: Laravel Built-in Auth
- **Middleware**: Custom Role-based Access Control
- **Security**: Activity Logging, Session Management
- **UI/UX**: Responsive Design, Modern Interface

## 📋 Persyaratan Sistem

- PHP 8.2+
- Composer
- Sqlite 3
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
DB_CONNECTION=sqlite
<!-- DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=koperasi_db
DB_USERNAME=root
DB_PASSWORD= -->
```

### 5. Jalankan Migration dan Seeder
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## 🔐 Login Default

Setelah menjalankan seeder, gunakan akun default:

**Admin:**
- Email: `admin@koperasi.com`
- Password: `Admin123`

**Kasir:**
- Email: `kasir@gmail.com`
- Password: `Kasir123`

**Admin:**
- Email: `gudang@gmail.com`
- Password: `Gudang123`

## 📊 Struktur Database

### Tabel Utama:
- `users` - Data pengguna sistem
- `roles` - Role pengguna (admin, kasir, gudang)
- `categories` - Kategori produk
- `products` - Data produk dengan stok
- `transactions` - Header transaksi
- `transaction_details` - Detail item transaksi
- `activity_logs` - Log aktivitas user (login, logout, update profil)
- `chat_rooms` - Room untuk group chat
- `chat_messages` - Pesan dalam group chat

### Relasi:
- User → Role (belongsTo)
- User → ActivityLog (hasMany)
- User → Transaction (hasMany)
- Product → Category (belongsTo)
- Transaction → User (belongsTo)
- Transaction → TransactionDetail (hasMany)
- Product → TransactionDetail (hasMany)
- ChatMessage → User (belongsTo)

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
- `GET /admin/activity-logs` - Log aktivitas user
- `GET /admin/activity-logs/{id}` - Detail log aktivitas
- `GET /admin/activity-logs/export` - Export log aktivitas

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

### Global Routes (Semua Role)
- `GET /profile/edit` - Edit profil user
- `PUT /profile` - Update profil user
- `GET /chat` - Group chat
- `POST /chat` - Kirim pesan chat
- `GET /chat/latest` - Pesan terbaru
- `POST /chat/clear` - Hapus semua pesan
- `DELETE /chat/messages/{id}` - Hapus pesan tertentu

## 🔒 Middleware

### CheckRole Middleware
Middleware custom untuk mengecek role user:
```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    // Routes untuk admin
});
```

### ActiveSession Middleware
Middleware untuk kontrol sesi aktif:
```php
Route::middleware(['auth', 'active.session'])->group(function () {
    // Routes yang memerlukan sesi aktif
});
```

## 🔐 Fitur Keamanan

### Activity Logging
Sistem mencatat semua aktivitas user:
- **Login berhasil/gagal** - Dengan IP address dan user agent
- **Logout** - Waktu dan detail logout
- **Update profil** - Perubahan data user
- **Akses halaman** - Monitoring aktivitas

### Password Security
- **Show/Hide Password** - Toggle visibility dengan sinkronisasi
- **Password Validation** - Konfirmasi password real-time
- **Password Hashing** - Menggunakan Laravel Hash
- **Current Password Verification** - Validasi password lama

### Session Management
- **Session Regeneration** - Mencegah session fixation
- **Session Timeout** - Kontrol waktu sesi aktif
- **Secure Logout** - Invalidate session dengan aman

## 🎨 Frontend

### Layout
- Responsive design dengan Tailwind CSS
- Sidebar navigation yang dapat di-collapse
- Mobile-friendly interface
- **User Card Dropdown** - Informasi user dengan akses edit profil
- **Modern UI Components** - Cards, buttons, forms yang elegan

### Components
- Dashboard cards dengan statistik
- Data tables dengan pagination dan filtering
- Form inputs dengan validation real-time
- Alert messages untuk feedback
- **Password Toggle** - Show/hide password dengan animasi
- **Activity Log Viewer** - Tabel log dengan filter lengkap
- **Profile Editor** - Form edit profil yang user-friendly

### JavaScript Features
- **Alpine.js** - Reactive components
- **SweetAlert2** - Beautiful alerts dan confirmations
- **Real-time Validation** - Form validation tanpa reload
- **Smooth Animations** - Transisi yang halus

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

4. **Activity Log Error (user_id null)**
   - Pastikan migration `activity_logs` sudah dijalankan
   - Kolom `user_id` sudah di-set sebagai `nullable`

5. **Session Timeout**
   - Cek konfigurasi `SESSION_LIFETIME` di `.env`
   - Pastikan middleware `active.session` berfungsi

6. **Password Toggle Tidak Berfungsi**
   - Pastikan Alpine.js sudah dimuat
   - Cek console browser untuk error JavaScript

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

## 📋 Changelog

### v2.0.0 - Latest Update
- ✅ **Fitur Edit Profil** - User dapat mengedit profil sendiri
- ✅ **Show/Hide Password** - Toggle password dengan sinkronisasi
- ✅ **Activity Logging** - Monitoring lengkap aktivitas user
- ✅ **User Card Dropdown** - Interface modern untuk akses profil
- ✅ **Export Data** - Export log aktivitas ke CSV
- ✅ **Enhanced Security** - Session management dan password security
- ✅ **Responsive Design** - Interface yang lebih modern dan responsif

### v1.0.0 - Initial Release
- ✅ **Role-based System** - Admin, Kasir, Gudang
- ✅ **POS System** - Point of Sale untuk transaksi
- ✅ **Inventory Management** - Manajemen produk dan stok
- ✅ **User Management** - CRUD user dengan role
- ✅ **Dashboard** - Statistik dan monitoring
- ✅ **Group Chat** - Komunikasi antar user

## 🎯 Roadmap

### Upcoming Features
- [ ] **Real-time Notifications** - Notifikasi live untuk aktivitas
- [ ] **Advanced Reporting** - Laporan dengan grafik dan chart
- [ ] **Backup System** - Otomatis backup database
- [ ] **API Integration** - REST API untuk mobile app
- [ ] **Multi-language** - Dukungan bahasa Indonesia/English

---

**Dibuat dengan ❤️ menggunakan Laravel 11**

*Sistem Koperasi v2.0.0 - Modern, Secure, dan User-Friendly*
