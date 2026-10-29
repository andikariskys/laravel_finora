# Finora : Aplikasi Manajemen Keuangan Pribadi

**Finora** (Financial Organizer & Record Assistant) adalah aplikasi web manajemen keuangan pribadi yang ringan, simpel, dan dirancang dengan pendekatan **mobile-first**. Finora membantu Anda melacak arus kas harian, memantau saldo di berbagai rekening bank dan dompet digital (e-wallet), mengelola hutang-piutang rekan, serta mencatat pemotongan otomatis biaya langganan bulanan tanpa kerumitan akuntansi konvensional.

---

## Fitur Utama

- **Multi-Wallet Fleksibel**:
  - Dukungan pembuatan dompet tanpa batas (Uang Tunai, Rekening Bank BCA/Mandiri/BRI, E-Money GoPay/OVO/DANA/ShopeePay, dan Tabungan Khusus).
  - Atur langsung saldo awal saat pertama kali menambahkan e-wallet atau bank baru.
  - Status **Aktif** dan **Non-Aktif**: Dompet non-aktif disembunyikan dari transaksi harian dan total saldo utama.
  - **Reaktivasi & Penyesuaian Saldo**: Saat mengaktifkan kembali dompet yang sebelumnya dinonaktifkan, Anda dapat langsung mengubah dan menyinkronkan saldo terkini secara instan.
- **Pencatatan Pemasukan (Add Balance)**:
  - Tambah saldo dengan informasi spesifik sumber dana (Gaji, Proyek Lepas, Hadiah, dll) dan dompet penerima.
- **Pencatatan Pengeluaran**:
  - Catat pengeluaran harian berdasarkan dompet sumber dan kategori (Makanan, Transportasi, Tagihan, Hiburan, dll).
- **Transfer Antar Dompet & Biaya Admin**:
  - Perpindahan dana antar dompet (misal: Bank ke E-Money).
  - Input opsional untuk biaya admin transfer / top-up saldo yang otomatis dipotong dari dompet asal.
- **Fitur Pinjam (Hutang & Piutang)**:
  - **Piutang (Meminjamkan)**: Catat uang yang dipinjam oleh rekan beserta nama dan wallet sumbernya.
  - **Hutang (Meminjam)**: Catat kewajiban pinjaman dari orang lain ke dompet tujuan.
  - Pelacakan status pelunasan (Belum Lunas, Lunas Sebagian, Lunas) dan riwayat cicilan.
- **Biaya Langganan & Admin Bank Otomatis (Recurring Rules)**:
  - Pemotongan otomatis pengeluaran rutin bulanan (misal sewa server, Spotify, admin rekening pada tanggal 1).
  - Mekanisme **Hybrid**: Otomatis diproses saat user membuka aplikasi (On-Access Trigger) maupun via background scheduler.
- **Reset & Rollover Bulanan**:
  - Periode buku bulanan berganti setiap tanggal 1 (atau dapat diatur sesuai tanggal gajian pada menu Pengaturan).
  - Riwayat transaksi masa lalu tetap tersimpan utuh di database dan dapat dilihat kembali per periode.
  - Sisa saldo riil bulan lalu otomatis menjadi **Saldo Awal (Opening Balance)** di bulan berjalan.
- **Dashboard Ringkas & Minimalis**:
  - Total saldo keseluruhan semua dompet aktif dengan sensor privasi (sembunyikan nominal).
  - Ringkasan statistik bulan ini: Saldo Awal, Total Pemasukan, Total Pengeluaran, dan Arus Kas Bersih.
  - Akses cepat 1 sentuhan (Quick Actions) untuk mencatat transaksi baru.
  - Ringkasan kartu dompet dan mutasi transaksi terbaru.
- **Antarmuka Mobile-First**:
  - Didesain untuk kenyamanan pengoperasian satu tangan di layar ponsel pintar.
  - Menggunakan Tailwind CSS dan DaisyUI dengan dukungan penuh **Tema Gelap** dan **Tema Terang**.
  - Mengikuti standar aksesibilitas kontras warna WCAG AA dan target sentuh minimal 44px.

---

## Tumpukan Teknologi (Tech Stack)

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Blade Templates, Tailwind CSS v4, DaisyUI, Alpine.js
- **Database**: SQLite (default lokal) / MySQL / PostgreSQL
- **Asset Bundler**: Vite

---

## Panduan Instalasi Lokal

### 1. Prasyarat
- PHP >= 8.2 dengan ekstensi `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- Composer 2.x
- Node.js >= 18.x dan npm

### 2. Kloning dan Setup Dependensi
```bash
# Pastikan berada di direktori proyek
cd finora

# Pasang dependensi PHP
composer install

# Salin konfigurasi environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Jalankan migrasi database
php artisan migrate

# Pasang paket frontend
npm install
```

### 3. Menjalankan Aplikasi dalam Mode Pengembangan
Jalankan server backend dan Vite bundler:

```bash
# Opsi 1: Menjalankan semua layanan sekaligus via Composer dev runner
composer run dev

# Opsi 2: Menjalankan secara terpisah di dua terminal berbeda
# Terminal 1 (Laravel Server):
php artisan serve

# Terminal 2 (Vite Assets):
npm run dev
```

Aplikasi dapat diakses melalui peramban di: `http://localhost:8000`

---

## Pemrosesan Biaya Otomatis via Scheduler (Opsional)

Jika aplikasi di-deploy pada server dengan Cron Job, Anda dapat menambahkan baris berikut pada crontab server untuk menjalankan pemotongan tagihan rutin tepat waktu:

```bash
* * * * * cd /path-to-finora && php artisan schedule:run >> /dev/null 2>&1
```

Atau jalankan perintah artisan secara langsung:
```bash
php artisan finora:process-recurring
```

---

## Dokumentasi Proyek Terkait

- [PRD.md](PRD.md) : Dokumen Spesifikasi Kebutuhan Produk lengkap (Alur bisnis, skema ERD database, dan detail fungsional).
- [DESIGN.md](DESIGN.md) : Panduan Desain Sistem & Mobile-First (Palet warna tema gelap/terang, tipografi, dan token UI).

---

## Lisensi

Proyek ini dikembangkan di bawah lisensi terbuka [MIT](LICENSE).
