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

**Untuk admin** (panel di `/admin`)
- Dashboard admin.
- Kelola data parfum: lihat daftar, tambah, ubah, dan hapus.
- Kelola chat konsultasi dari member premium.
- Kelola data pengguna.

## Teknologi

- PHP 8.3 atau lebih baru, dan Laravel 13
- MySQL / MariaDB
- Bootstrap 5 (lewat CDN, tidak perlu `npm`)
- Laragon atau XAMPP (lingkungan pengembangan di Windows)

## Kebutuhan

| Kebutuhan | Keterangan |
|---|---|
| PHP **8.3+** | Laravel 13 tidak bisa berjalan di PHP 8.2 ke bawah. Cek dengan `php -v`. |
| Composer | Untuk memasang dependensi (`composer install`). |
| Git | Untuk clone dan push. |
| MySQL atau MariaDB | Database `parfum_finder`. |

Laragon Full sudah membawa semuanya. Di XAMPP, Composer dan Git harus dipasang sendiri.

## Cara menjalankan

### Langkah 1: ambil proyek

**Laragon:** clone ke folder `www`.
```bash
cd D:\laragon\www
git clone https://github.com/danielsetiawan2403/parfum-finder.git
cd parfum-finder
```

**XAMPP:** clone ke folder mana saja (misalnya `C:\xampp\htdocs`).
```bash
cd C:\xampp\htdocs
git clone https://github.com/danielsetiawan2403/parfum-finder.git
cd parfum-finder
```

### Langkah 2: pasang dependensi dan siapkan konfigurasi

```bash
composer install
copy .env.example .env
php artisan key:generate
```

### Langkah 3: buat database

- **Laragon:** buka HeidiSQL (tombol **Database**), buat database kosong bernama `parfum_finder`.
- **XAMPP:** nyalakan **Apache** dan **MySQL** di XAMPP Control Panel, buka `http://localhost/phpmyadmin`, lalu buat database kosong bernama `parfum_finder`.

### Langkah 4: atur `.env`

Pastikan bagian database seperti ini (user `root` tanpa password adalah bawaan Laragon dan XAMPP):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=parfum_finder
DB_USERNAME=root
DB_PASSWORD=
APP_TIMEZONE=Asia/Jakarta
```

### Langkah 5: buat tabel dan isi data parfum

```bash
php artisan migrate
php artisan db:seed --class=PerfumeSeeder
```

### Langkah 6: jalankan

- **Laragon:** buka `http://parfum-finder.test`.
- **XAMPP** (atau kalau ingin cara yang sama untuk semua): jalankan server bawaan Laravel lalu buka `http://127.0.0.1:8000`.
  ```bash
  php artisan serve
  ```
  Biarkan terminal itu tetap terbuka selama memakai website.

### Catatan khusus XAMPP

- **Versi PHP:** pastikan XAMPP yang dipakai membawa PHP 8.3 atau lebih baru (`php -v`). Kalau lebih rendah, pasang XAMPP versi yang lebih baru atau pakai Laragon.
- **Perintah `php` tidak dikenali:** tambahkan folder PHP XAMPP (biasanya `C:\xampp\php`) ke PATH Windows, lalu buka terminal baru.
- **Composer:** pasang dari getcomposer.org. Installer-nya akan mencari `php.exe` di folder XAMPP.
- **Error soal ekstensi** (misalnya `fileinfo`, `mbstring`, atau `pdo_mysql`): buka `C:\xampp\php\php.ini`, hapus tanda `;` di depan baris `extension=...` yang disebut, lalu restart Apache.
- **MySQL tidak mau menyala karena port 3306 terpakai:** hentikan layanan MySQL lain yang sedang berjalan di komputer.

### Foto parfum

Foto disimpan di `public/images/parfum/` dengan nama file sesuai kolom `image` di tabel `perfumes` (daftar nama ada di `database/seeders/PerfumeSeeder.php`). Latar halaman login/register bersifat opsional: `public/images/bg-login.jpg`.

### Membuat akun admin

Daftar seperti biasa lewat halaman registrasi, lalu ubah kolom `role` akun tersebut menjadi `admin` di tabel `users` (HeidiSQL atau phpMyAdmin). Setelah itu, saat login admin langsung diarahkan ke `/admin`.

## Struktur penting

| Lokasi | Isi |
|---|---|
| `routes/web.php` | Daftar route |
| `app/Http/Controllers/` | `AuthController`, `PerfumeController`, `PremiumController`, `ReviewController`, `ChatController` |
| `app/Http/Controllers/Admin/` | Controller panel admin |
| `app/Http/Middleware/EnsureAdmin.php` | Membatasi `/admin` hanya untuk akun dengan `role = admin` |
| `bootstrap/app.php` | Pendaftaran alias middleware |
| `app/Models/` | `Perfume`, `Subscription`, `Review`, `ChatMessage`, dan `User` |
| `resources/views/` | Tampilan Blade (`layouts/`, `premium/`, `chat/`, dan halaman utama) |
| `resources/views/admin/` | Tampilan panel admin (dashboard, parfum, chat, pengguna) |
| `public/css/parfum.css` | Gaya tambahan di atas Bootstrap untuk situs utama |
| `public/css/admin.css` | Gaya panel admin |
| `database/migrations/` | Struktur tabel |
| `database/seeders/PerfumeSeeder.php` | Data awal parfum |

## Catatan

- Pembayaran Premium bersifat **simulasi**: tombol konfirmasi langsung mengaktifkan keanggotaan dan QR dibuat dari data invoice, belum terhubung ke payment gateway.
- Bootstrap, font Poppins, dan pembuat QR dimuat dari CDN, jadi tampilan lengkap membutuhkan koneksi internet.

## Tim

Kelompok Parfum

| Nama | NIM | Peran |
|---|---|---|
| Daniel | 1234 | Fullstack Client |