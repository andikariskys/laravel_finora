# Finora — Aplikasi Manajemen Keuangan Pribadi

**Finora** (Financial Organizer & Record Assistant) adalah aplikasi web manajemen keuangan pribadi yang ringan, terstruktur, dan dibangun dengan pendekatan **mobile-first**. Finora membantu Anda mencatat arus kas harian, memantau saldo di berbagai rekening bank dan dompet digital, mengelola hutang-piutang rekan, serta mengeksekusi pemotongan otomatis tagihan rutin tanpa kerumitan akuntansi konvensional.

---

## Fitur-Fitur yang Telah Dibuat

### 1. Dashboard Finansial & Pengaturan Cutoff
- **Total Saldo Aktif**: Akumulasi saldo riil dari seluruh dompet yang berstatus aktif.
- **Sensor Privasi**: Tombol sembunyikan/tampilkan nominal saldo untuk kenyamanan saat membuka aplikasi di ruang publik.
- **Statistik Periode Berjalan**: Menampilkan Saldo Awal (*Opening Balance*), Total Pemasukan, Total Pengeluaran, dan Arus Kas Bersih (*Net Cashflow*).
- **Ringkasan Hutang & Piutang**: Memantau total nominal piutang (uang di luar) dan kewajiban hutang yang belum lunas langsung dari kartu ringkasan.
- **Shortcut Aksi Cepat (*Quick Actions*)**: Modal cepat untuk mencatat transaksi baru tanpa perlu berpindah halaman.
- **Pengaturan Tanggal Cutoff Bulanan**: Pengguna dapat menentukan tanggal reset buku bulanan (tanggal 1 s.d. 28) menyesuaikan siklus gajian.
- **Pratinjau Aktivitas**: Daftar 3 transaksi mutasi terbaru dan 4 tagihan rutin aktif.

### 2. Manajemen Multi-Wallet & Rekening
- **Ragam Tipe Akun**: Mendukung kategori Uang Tunai (`cash`), Rekening Bank (`bank`), Dompet Digital (`ewallet`), dan Investasi (`investment`).
- **Saldo Awal Otomatis**: Input nominal saldo awal saat pertama kali membuat dompet; sistem otomatis mencatat transaksi mutasi `initial_balance`.
- **Status Aktif & Non-Aktif**: Dompet yang dinonaktifkan disembunyikan dari pilihan transaksi harian dan tidak dihitung ke dalam total saldo utama.
- **Penyesuaian Saldo Riil (*Balance Adjustment*)**: Form koreksi saldo manual untuk menyinkronkan saldo aplikasi dengan saldo riil fisik/rekening. Perubahan saldo dicatat sebagai transaksi audit `balance_adjustment` beserta alasan penyesuaian.
- **Transfer Antar Dompet & Biaya Admin**: Perpindahan dana antar dompet (misal: Rekening Bank ke E-Money). Mendukung input biaya admin transfer/top-up yang otomatis dipotong dari dompet asal dan dicatat sebagai transaksi berpasangan (`transfer_out` dan `transfer_in`).
- **Riwayat Aktivitas Dompet**: Log khusus untuk memantau aktivitas transfer, penyesuaian saldo, dan saldo awal pembukaan akun.

### 3. Pencatatan Mutasi Transaksi
- **Pemasukan & Pengeluaran**: Pencatatan transaksi dengan atribut dompet sumber, kategori, nominal, biaya admin tambahan, pihak sumber dana, dan deskripsi.
- **Tanggal Transaksi Fleksibel (*Backdating*)**: Bebas memilih tanggal transaksi di masa lampau untuk mencatat pengeluaran yang terlewat.
- **Filter & Paginasi**: Filter mutasi berdasarkan tipe (Semua, Pemasukan, Pengeluaran) lengkap dengan sistem halaman (20 item per halaman).

### 4. Manajemen Hutang & Piutang (*Loans*)
- **Piutang (*Loan Given*)**: Mencatat dana yang dipinjamkan ke pihak lain. Saldo dompet sumber otomatis terpotong saat data disimpan dan dicatat sebagai mutasi pengeluaran terkait.
- **Hutang (*Debt Taken*)**: Mencatat pinjaman yang diterima dari pihak lain. Saldo dompet penerima otomatis bertambah dan dicatat sebagai mutasi pemasukan terkait.
- **Pelunasan & Cicilan Bertahap**: Formulir pencatatan cicilan dengan validasi agar pembayaran tidak melebihi sisa pokok pinjaman.
- **Status Pinjaman Dinamis**: Status otomatis beralih dari `unpaid` (belum lunas), `partial` (lunas sebagian), hingga `paid` (lunas).
- **Sinkronisasi Saldo Cicilan**: Setiap pembayaran cicilan langsung memperbarui saldo dompet penampung/pembayar serta mencatat histori pembayaran di log transaksi.

### 5. Tagihan & Pengeluaran Rutin Otomatis (*Recurring Rules*)
- **Dukungan 4 Frekuensi Jatuh Tempo**:
  - **Harian (`daily`)**: Pemotongan saldo otomatis setiap hari.
  - **Mingguan (`weekly`)**: Pemotongan otomatis pada hari tertentu dalam sepekan (Senin s.d. Minggu).
  - **Bulanan (`monthly`)**: Pemotongan otomatis pada tanggal yang ditentukan (tanggal 1 s.d. 28) setiap bulan.
  - **Tanggal Khusus (`custom_date`)**: Pemotongan satu kali pada tanggal jatuh tempo spesifik (misal: pajak tahunan), dan otomatis berstatus non-aktif setelah selesai dieksekusi.
- **Estimasi Beban Bulanan**: Kalkulasi total estimasi pengeluaran rutin yang harus dipersiapkan setiap bulan pada header halaman.
- **Kontrol Status**: Fitur toggle aktif/non-aktif dan penghapusan aturan tagihan.
- **Mekanisme Eksekusi Hybrid**:
  - **On-Access Trigger**: Tagihan yang telah jatuh tempo otomatis diproses dan memotong saldo saat pengguna membuka halaman Dashboard.
  - **Background Scheduler**: Perintah konsol artisan dan daemon penjadwalan untuk pemrosesan rutin di latar belakang.

### 6. Laporan & Riwayat Keuangan Bulanan
- **Ikhtisar 6 Bulan Terakhir**: Rangkuman pergerakan kas bulanan (Total Pemasukan, Total Pengeluaran, Arus Kas Bersih, dan Frekuensi Transaksi).
- **Rollover Saldo Transparan**: Data masa lalu tidak dihapus saat pergantian bulan; sisa saldo akhir bulan sebelumnya otomatis menjadi saldo awal bulan berjalan.
- **Ekspor Dokumen**: Tombol aksi pratinjau dan pencetakan/unduhan laporan bulanan dalam format dokumen siap cetak.

### 7. Autentikasi Pengguna & Akun Pengujian
- **Registrasi & Keamanan Kata Sandi**: Validasi kata sandi ketat (minimal 8 karakter, kombinasi huruf besar, huruf kecil, angka, dan simbol).
- **Login, Logout & Remember Me**: Sesi login aman dengan proteksi CSRF.
- **Akun Demo Bawaan**:
  - **Email**: `admin@email.com`
  - **Kata Sandi**: `admin@123`

### 8. Desain Antarmuka Mobile-First & Aksesibilitas
- **Navigasi Responsif**: *Bottom Dock Navigation* ergonomis untuk pengoperasian satu tangan di ponsel pintar, serta *Navbar sticky* dengan navigasi penuh di desktop.
- **Mode Gelap & Terang**: Dukungan penuh tema gelap (`night`) dan tema terang (`emerald`) berbasis DaisyUI dengan script inline pencegah *flash of unstyled theme* (FOUC).
- **Standar Aksesibilitas**: Memenuhi standar kontras warna WCAG AA dan target sentuh tombol minimal 44x44px.

---

## Tumpukan Teknologi (*Tech Stack*)

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS v4, DaisyUI v5, Alpine.js
- **Database**: SQLite (default lingkungan lokal) / MySQL / PostgreSQL
- **Bundler Aset**: Vite

---

## Perintah Menjalankan Scheduler Finora

Finora dilengkapi dengan sistem penjadwalan otomatis untuk mengeksekusi tagihan rutin yang jatuh tempo. Penjadwalan terdaftar pada Laravel Scheduler (`routes/console.php`) dan berjalan setiap hari pada pukul `00:01 WIB`.

### 1. Menjalankan Scheduler di Lingkungan Pengembangan (Lokal)
Jalankan worker scheduler foreground yang akan mengecek dan mengeksekusi tugas terjadwal setiap menit:

```bash
# Opsi 1: Menggunakan skrip Composer
composer run schedule

# Opsi 2: Menggunakan perintah Artisan langsung
php artisan schedule:work
```

### 2. Menjalankan Pemrosesan Tagihan Secara Manual
Anda dapat memicu pemotongan tagihan yang jatuh tempo secara instan tanpa menunggu jam scheduler:

```bash
# Memproses seluruh tagihan yang jatuh tempo untuk semua pengguna
php artisan finora:process-recurring

# Atau via shortcut Composer
composer run process-recurring

# Memproses tagihan khusus untuk satu pengguna tertentu
php artisan finora:process-recurring --user=1
```

### 3. Memeriksa Daftar Jadwal yang Terdaftar
Untuk melihat daftar tugas terjadwal dan jadwal eksekusi berikutnya:

```bash
php artisan schedule:list
```

Output:
```
1 0 * * *  php artisan finora:process-recurring  Next Due: ...
```

### 4. Konfigurasi Scheduler Menggunakan Perintah Cron Job

Pada server Linux, VPS, atau shared hosting (cPanel), penjadwalan otomatis dijalankan melalui fasilitas **Cron Job**. Tersedia dua pendekatan yang dapat digunakan:

#### Langkah Setup Cron Job di Server (Linux / VPS):

1. **Periksa Path Binary PHP dan Direktori Proyek**:
   ```bash
   which php
   # Contoh output: /usr/bin/php

   pwd
   # Contoh output: /var/www/finora
   ```

2. **Buka Editor Crontab**:
   ```bash
   crontab -e
   ```

3. **Tambahkan Baris Perintah Cron Job**:

   - **Opsi A: Laravel Scheduler Standar (Direkomendasikan)**  
     Cron server akan memicu runner Laravel setiap menit, dan Laravel akan mengeksekusi `finora:process-recurring` tepat pada jadwal yang telah ditentukan (`00:01 WIB`):
     ```bash
     * * * * * cd /path/ke/finora && php artisan schedule:run >> /dev/null 2>&1
     ```
     Atau menggunakan path absolut dengan file log output mandiri:
     ```bash
     * * * * * /usr/bin/php /var/www/finora/artisan schedule:run >> /var/www/finora/storage/logs/scheduler.log 2>&1
     ```

   - **Opsi B: Cron Job Khusus Tagihan Finora (Direct Daily Execution)**  
     Jika Anda hanya ingin menjalankan pemotongan tagihan Finora satu kali sehari pada pukul `00:01 WIB` secara langsung tanpa cron per menit:
     ```bash
     1 0 * * * cd /path/ke/finora && php artisan finora:process-recurring >> /path/ke/finora/storage/logs/recurring.log 2>&1
     ```

4. **Verifikasi dan Monitoring Cron Job**:
   - Menampilkan daftar cron job yang sedang aktif:
     ```bash
     crontab -l
     ```
   - Memantau log riwayat eksekusi cron:
     ```bash
     tail -f storage/logs/scheduler.log
     ```

#### Setup pada cPanel / Shared Hosting:
1. Masuk ke **cPanel** > cari menu **Cron Jobs** (*Tugas Cron*).
2. Pada bagian *Common Settings*, pilih interval waktu:
   - `Once Per Minute (* * * * *)` jika menggunakan perintah `php artisan schedule:run`.
   - `Once a Day (0 0 * * *)` jika menggunakan perintah `php artisan finora:process-recurring`.
3. Pada input field *Command*, masukkan:
   ```bash
   /usr/local/bin/php /home/username/public_html/finora/artisan schedule:run >> /dev/null 2>&1
   ```
   *(Sesuaikan path PHP dan direktori instalasi aplikasi Anda).*

> **Catatan (Mekanisme Hybrid)**: Jika Anda tidak menjalankan scheduler latar belakang di lingkungan lokal, tagihan yang jatuh tempo akan tetap otomatis diproses saat pengguna membuka halaman Dashboard melalui *On-Access Trigger*.

---

## Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP >= 8.2 dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer 2.x
- Node.js >= 18.x dan npm

### 2. Setup Proyek
```bash
# 1. Kloning repositori dan masuk ke direktori
cd finora

# 2. Pasang dependensi PHP
composer install

# 3. Buat file environment
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Jalankan migrasi database beserta data seeder awal
php artisan migrate --seed

# 6. Pasang dependensi JavaScript
npm install

# 7. Kompilasi aset frontend
npm run build
```

### 3. Menjalankan Aplikasi
```bash
# Menjalankan seluruh layanan pengembangan sekaligus (Server, Queue, Logs, Vite):
composer run dev

# Atau menjalankan backend secara mandiri:
php artisan serve
```

Buka peramban di: `http://localhost:8000`  
Gunakan akun uji coba: `admin@email.com` / `admin@123`.

---

## Skrip Composer yang Tersedia

| Perintah | Deskripsi |
| :--- | :--- |
| `composer run dev` | Menjalankan server lokal, queue listener, log pail, dan Vite bundler secara bersamaan. |
| `composer run schedule` | Menjalankan scheduler worker foreground (`php artisan schedule:work`). |
| `composer run process-recurring` | Menjalankan pemrosesan tagihan rutin yang jatuh tempo secara langsung. |
| `composer run test` | Membersihkan cache konfigurasi dan menjalankan seluruh test suite PHPUnit. |
| `composer run setup` | Menjalankan otomatisasi setup awal proyek (install, migrate, key generate, build). |

---

## Dokumentasi Terkait

- [PRD.md](PRD.md) : Spesifikasi Kebutuhan Produk (Alur bisnis, skema database, dan aturan validasi).
- [DESIGN.md](DESIGN.md) : Panduan Desain Sistem & Mobile-First (Tipografi, palet tema, dan token UI).

---

## Lisensi

Proyek Finora didistribusikan di bawah lisensi terbuka [MIT](LICENSE).
