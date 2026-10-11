# Parfum Finder

Sistem informasi berbasis web untuk membantu menemukan parfum berdasarkan aroma, aktivitas, dan preferensi pribadi.

## Fitur

**Untuk semua pengunjung**
- Landing page dengan parfum pilihan; kartu parfum bisa diklik ke halaman detail.
- Detail parfum: top, middle, dan bottom notes, kategori, harga, dan deskripsi.
- Registrasi dan login memakai username.

**Untuk user yang sudah login**
- Dashboard berisi seluruh koleksi parfum, lengkap dengan pencarian (nama dan notes aroma) dan filter kategori.

**Untuk member Premium**
- Paket 1 bulan, 6 bulan, dan 1 tahun dengan pembayaran QRIS (simulasi) dan halaman status keanggotaan.
- Menulis ulasan dengan rating bintang 1 sampai 5, serta membaca ulasan member lain.
- Chat konsultasi.

## Teknologi

- PHP 8.3 dan Laravel 13
- MySQL
- Bootstrap 5 (lewat CDN, tidak perlu `npm`)
- Laragon (lingkungan pengembangan di Windows)

## Cara menjalankan

Pastikan Laragon sudah terpasang dan Apache serta MySQL menyala (**Start All**).

1. Clone repo ke folder `www` Laragon, lalu masuk ke foldernya:
   ```bash
   cd D:\laragon\www
   git clone https://github.com/danielsetiawan2403/parfum-finder.git
   cd parfum-finder
   ```
2. Pasang dependensi dan siapkan konfigurasi:
   ```bash
   composer install
   copy .env.example .env
   php artisan key:generate
   ```
3. Buat database kosong bernama `parfum_finder` lewat HeidiSQL (tombol **Database** di Laragon).
4. Pastikan bagian database di `.env` seperti ini:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=parfum_finder
   DB_USERNAME=root
   DB_PASSWORD=
   APP_TIMEZONE=Asia/Jakarta
   ```
5. Buat tabel dan isi data parfum:
   ```bash
   php artisan migrate
   php artisan db:seed --class=PerfumeSeeder
   ```
6. Buka `http://parfum-finder.test` (atau jalankan `php artisan serve` lalu buka `http://127.0.0.1:8000`).

### Foto parfum

Foto disimpan di `public/images/parfum/` dengan nama file sesuai kolom `image` di tabel `perfumes` (daftar nama ada di `database/seeders/PerfumeSeeder.php`). Latar halaman login/register bersifat opsional: `public/images/bg-login.jpg`.

### Membuat akun admin

Daftar seperti biasa lewat halaman registrasi, lalu ubah kolom `role` akun tersebut menjadi `admin` di tabel `users` (HeidiSQL).

## Struktur penting

| Lokasi | Isi |
|---|---|
| `routes/web.php` | Daftar route |
| `app/Http/Controllers/` | `AuthController`, `PerfumeController`, `PremiumController`, `ReviewController`, `ChatController` |
| `app/Models/` | `Perfume`, `Subscription`, `Review`, `ChatMessage`, dan `User` |
| `resources/views/` | Tampilan Blade (`layouts/`, `premium/`, `chat/`, dan halaman utama) |
| `public/css/parfum.css` | Gaya tambahan di atas Bootstrap |
| `database/migrations/` | Struktur tabel |
| `database/seeders/PerfumeSeeder.php` | Data awal parfum |

## Catatan

- Pembayaran Premium bersifat **simulasi**: tombol konfirmasi langsung mengaktifkan keanggotaan dan QR dibuat dari data invoice, belum terhubung ke payment gateway.
- Bootstrap, font Poppins, dan pembuat QR dimuat dari CDN, jadi tampilan lengkap membutuhkan koneksi internet.

## Tim

Kelompok Parfum

| Nama | NIM | Peran |
|---|---|---|
| (Daniel) | (1234) | (Fullstack Client) |