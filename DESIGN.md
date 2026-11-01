# Design System & Direction (DESIGN.md) : Finora

**Aplikasi**: Finora (Personal Finance Assistant)  
**Karakter**: Minimalis, Ringan, Fungsional, Tenang, Responsif  
**Framework**: Tailwind CSS v4, DaisyUI, Alpine.js  

---

## 1. Filosofi & Arah Desain (Design Direction)

Finora dirancang untuk menghilangkan kecemasan finansial (*financial anxiety*). Seluruh antarmuka mengutamakan ketenangan visual, kejujuran data, dan kemudahan pencatatan harian tanpa kerumitan akuntansi konvensional. 

Finora secara tegas menghindari visual generik AI slop seperti gradien ungu-biru neon, kartu mengambang dengan bayangan tebal, ornamen tanpa makna, serta istilah teknis pengembangan yang tidak relevan bagi pengguna akhir.

### Prinsip Utama:
1. **Kejelasan Hierarki Data**: Angka nominal, dompet sumber/tujuan, dan tanggal transaksi terbaca instan dalam sekali lirik.
2. **Kenyamanan Akses & Responsif**: Navigasi dan aksi cepat mudah dijangkau satu tangan pada layar ponsel (area sentuh minimal 44x44px), serta bertransisi menjadi tata letak multi-kolom yang proporsional pada tablet dan desktop.
3. **Ketenangan Visual & Kontras Tinggi**: Palet hijau emerald finansial berpadu dengan latar slate yang matang, menjamin rasio kontras WCAG AA (> 4.5:1) hingga AAA (> 7:1).
4. **Berorientasi Pengguna (User-Centric Copy)**: Seluruh label dan salinan teks berfokus pada solusi finansial nyata, bebas dari jargon teknis (*developer terms*).

### Skala Dial Antislop
- **ENERGY: 2** (Refined, fokus, tenang, percaya diri sebagai utilitas finansial harian).
- **RHYTHM: 2** (Variasi terukur antara kartu ringkasan saldo, deretan dompet, dan daftar mutasi vertikal dengan jarak napas yang proporsional).
- **MOTION: 1** (Transisi tajam dan responsif berdurasi 150-200ms untuk modal, tab, dan toggle dark mode; tanpa animasi melayang atau efek bouncing berlebihan).

---

## 2. Palet Warna & Tema (Light & Dark Themes)

Aplikasi mendukung dua tema resmi yang disesuaikan secara presisi untuk kenyamanan mata di berbagai kondisi pencahayaan:

### 2.1. Tema Terang (Light Theme : `emerald-clean`)
- **Latar Belakang Dasar (Base 100)**: `#F8FAFC` (Slate 50, bersih, sejuk, dan tidak menyilaukan).
- **Latar Belakang Permukaan Kartu (Base 200)**: `#FFFFFF` (Putih murni dengan garis batas tegas `#E2E8F0` / Slate 200).
- **Teks Utama (Base Content)**: `#0F172A` (Slate 900, kontras tinggi).
- **Teks Sekunder**: `#475569` (Slate 600).
- **Warna Aksen Utama (Primary)**: `#059669` (Emerald 600, merepresentasikan ketenangan, stabilitas, dan pertumbuhan uang) / Hover: `#047857` (Emerald 700).
- **Warna Kontras Tombol Utama**: `#FFFFFF`.

### 2.2. Tema Gelap (Dark Theme : `night-slate`)
- **Latar Belakang Dasar (Base 100)**: `#0F172A` (Slate 900, abu-abu slate malam yang tenang dan dalam, bukan hitam pekat AMOLED).
- **Latar Belakang Permukaan Kartu (Base 200)**: `#1E293B` (Slate 800 dengan garis batas tegas `#334155` / Slate 700).
- **Teks Utama (Base Content)**: `#F8FAFC` (Slate 50).
- **Teks Sekunder**: `#94A3B8` (Slate 400).
- **Warna Aksen Utama (Primary)**: `#10B981` (Emerald 500, kontras dan jelas di atas latar gelap).
- **Warna Kontras Tombol Utama**: `#0F172A`.

### 2.3. Warna Semantik Finansial (Universal)
Warna ini memiliki makna fungsional tetap pada kedua tema:
- **Pemasukan (Income / Positive)**:
  - Teks: Emerald (`#059669` pada mode terang, `#34D399` pada mode gelap).
  - Background Badge: Emerald lembut transparan (10%).
- **Pengeluaran (Expense / Negative)**:
  - Teks: Rose (`#E11D48` pada mode terang, `#FB7185` pada mode gelap).
  - Background Badge: Rose lembut transparan (10%).
- **Transfer Antar Dompet (Neutral Movement)**:
  - Teks: Indigo (`#4F46E5` pada mode terang, `#818CF8` pada mode gelap).
- **Pinjaman / Perhatian (Debt / Attention)**:
  - Teks: Amber (`#D97706` pada mode terang, `#FBBF24` pada mode gelap).

---

## 3. Tipografi & Tata Huruf

Finora mengombinasikan font modern yang elegan dan mudah dibaca:
- **Font Utama Antarmuka**: `'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`.
- **Font Angka & Mata Uang (Tabular Numbers)**: Wajib menggunakan utilitas `tabular-nums tracking-tight font-semibold` agar angka memiliki lebar monospaced seragam secara vertikal saat dibandingkan atau dijajarkan.
- **Hierarki Skala Tipografi**:
  - `Hero Headline`: 2.25rem s.d. 3.75rem (36px - 60px), `font-extrabold tracking-tight leading-tight`.
  - `Display / Total Balance`: 2rem s.d. 2.25rem (32px - 36px), `font-extrabold tracking-tight`.
  - `Header H2 / Section Title`: 1.5rem s.d. 2.25rem (24px - 36px), `font-extrabold tracking-tight`.
  - `Card Header / H3`: 1.125rem s.d. 1.25rem (18px - 20px), `font-bold`.
  - `Body Reguler`: 0.875rem s.d. 1rem (14px - 16px), `font-normal leading-relaxed`.
  - `Caption / Metadata`: 0.75rem (12px), `font-medium text-slate-500 dark:text-slate-400`.

---

## 4. Pola Tata Letak Responsif (Responsive Layout)

Finora mengadopsi tata letak adaptif yang proporsional di setiap ukuran layar tanpa membatasi layar lebar menjadi tampilan ponsel tiruan:

### 4.1. Strategi Kontainer & Breakpoints
- **Ponsel (< 768px / breakpoint sm & bawah)**:
  - Kontainer satu kolom penuh (`w-full px-4`).
  - Area navigasi atas memprioritaskan tombol outline **Masuk** dan menu navigasi ringkas.
  - Elemen mengalir vertikal untuk kemudahan navigasi satu tangan.
- **Tablet (768px - 1024px / breakpoint md)**:
  - Kontainer adaptif dua kolom (`max-w-4xl mx-auto px-6 py-6`).
  - Kartu dompet dan mutasi disajikan bersisian secara seimbang.
- **Desktop (>= 1024px / breakpoint lg & xl)**:
  - Kontainer kerja penuh yang terstruktur (`max-w-7xl mx-auto px-8 py-8`).
  - Navigasi penuh dengan tombol outline **Masuk** dan solid **Daftar Akun**.
  - Tata letak multi-kolom yang produktif dengan pemanfaatan ruang layar yang luas untuk visibilitas data.

### 4.2. Area Sentuh & Ergonomi (Touch Targets)
- Setiap tombol, tautan navigasi, dan kontrol form memiliki tinggi minimal **44px** (`min-h-[44px] min-w-[44px]`) di semua mode layar.
- Jarak antar elemen interaktif minimal **8px** untuk mencegah salah klik.

---

## 5. Komponen Inti Antarmuka (Core Components)

### 5.1. Hero Section Berwibawa
- **Latar Belakang**: Menggunakan fotografi otentik terkait uang fisik/meja kerja finansial dengan lisensi bebas komersial (*free for commercial use*).
- **Overlay Kontras Tinggi**: Dilapisi gradien gelap bertingkat (`from-slate-950/95 via-slate-950/90 to-slate-950/80`) sehingga rasio kontras teks putih selalu melampaui 7:1 (WCAG AAA).
- **CTA Terfokus**: Menghindari tombol ganda yang membingungkan. Hero menggunakan satu tombol aksi utama yang tegas (*Mulai Catat Sekarang*).

### 5.2. Hierarki Gaya Tombol (Button Hierarchy)
1. **Tombol Primer (Solid Emerald)**:
   - Kelas: `bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg shadow-sm min-h-[44px]`.
   - Digunakan untuk: CTA utama konversi ("Mulai Catat Sekarang", "Daftar Akun", "Simpan Transaksi").
2. **Tombol Sekunder / Autentikasi (Outline Button)**:
   - Kelas: `border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-400 dark:hover:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg min-h-[44px]`.
   - Digunakan untuk: Tombol "Masuk" di navigasi atas (desktop & mobile) serta tombol alternatif pada banner penutup.

### 5.3. Kartu Saldo & Sensor Privasi (Privacy Mode)
- Menampilkan total saldo gabungan seluruh dompet aktif.
- Dilengkapi tombol sensor mata untuk menyembunyikan nominal di tempat umum.
- **Standar Simbol Sensor**: Wajib menggunakan format asterik standar perbankan (`Rp ********`), **bukan** peluru/bullet dots (`••••••••`).

### 5.4. Pratinjau Dompet & Mutasi (Dashboard Preview Card)
- Latar kartu gelap terstruktur (`bg-slate-900 text-white rounded-2xl border border-slate-800`).
- Menyajikan ringkasan arus kas bulan berjalan (Pemasukan vs Pengeluaran).
- Menampilkan daftar dompet dengan inisial warna pembeda (BCA, GoPay, Tunai).
- Menampilkan mutasi riil dengan penegasan biaya admin transfer yang terpisah dan presisi.

---

## 6. Standar Anti-Slop & Penulisan Teks (Anti-Slop Rules)

### 6.1. Larangan Simbol Peluru / Bullet Dots (`•` & `&bull;`)
- **Aturan**: Hindari penggunaan karakter peluru (`•` atau `&bull;`) sebagai pemisah metadata atau penanda baris teks, karena merupakan penanda visual klise template AI.
- **Solusi**: Gunakan pemisah garis miring bersih (`/`), tanda koma, tanda kurung `( )`, atau manfaatkan pembagian tata letak visual (*whitespace & layout structure*).

### 6.2. Bebas dari Istilah Teknis Pengembangan (Zero Developer Jargon)
- **Aturan**: Jangan pernah menampilkan nama komponen pengembang, pustaka, atau istilah internal proyek pada antarmuka publik yang dibaca pengguna (misalnya: *TailwindCSS*, *DaisyUI*, *Alpine.js*, *Laravel 12*, *PRD 4.2*, *Sprint*, *query*, dsb.).
- **Solusi**: Fokus 100% pada manfaat pengguna:
  - *Bukan*: "Query data diisolasi dengan Auth::id() Laravel 12"  
    *Melainkan*: "Privasi Terjaga Sepenuhnya — Catatan keuangan Anda tersimpan privat dan hanya dapat diakses oleh Anda."
  - *Bukan*: "Dibangun dengan TailwindCSS v4 tanpa overhead JS"  
    *Melainkan*: "Akses Cepat & Ringan — Aplikasi terbuka seketika tanpa jeda pemuatan, hemat kuota internet."

### 6.3. Pembatasan Efek Visual (Visual Restraint)
- **Glassmorphism**: Dibatasi hanya pada bilah navigasi atas yang menempel (`backdrop-blur-md`). Seluruh kartu konten, tabel, dan formulir wajib menggunakan permukaan solid dengan garis tepi yang tegas.
- **Bayangan (Shadows)**: Menggunakan bayangan minimal (`shadow-xs` / `shadow-sm`) sebagai penanda elevasi alami. Hindari bayangan melayang tebal (*overly soft shadows*).
- **Animasi (Motion)**: Durasi transisi 150-200ms (`duration-200 transition-colors`). Tidak menggunakan animasi denyut (*pulsing*) tanpa batas atau ornamen melayang tanpa pemicu pengguna.

### 6.4. Gaya Bahasa & Salinan Teks
- **Bahasa Indonesia Baku & Natural**: Lugas, bersahabat, tanpa nada marketing klise berlebihan ("Revolusionerkan masa depan Anda").
- **Tanpa Tanda Hubung Panjang (Em Dash `—`)**: Menggunakan koma, titik, tanda kurung, atau titik dua.
- **Tombol Aksi Jelas**: Menggunakan kata kerja spesifik ("Mulai Catat Sekarang", "Masuk", "Daftar Akun", "Simpan Transaksi").

---

## 7. Standar Tiga State UI (Mandatory States)

1. **State Kosong (Empty State)**:
   - Jika belum ada data transaksi/dompet: Tidak menampilkan tabel kosong yang kaku, melainkan ikon minimalis terpusat dengan pesan bersahabat: *"Belum ada transaksi di bulan ini. Mulai catat pemasukan atau pengeluaran pertama Anda."* disertai tombol CTA langsung *"Catat Transaksi"*.
2. **State Memuat (Loading State)**:
   - Tombol submit dilengkapi state dinamis `btn-disabled` dan indikator spinner kecil (`loading loading-spinner loading-xs`) untuk mencegah double-submit saat jaringan lambat.
3. **State Galat (Error State)**:
   - Pesan validasi form tampil tepat di bawah field input yang bermasalah dengan warna teks merah semantik yang kontras (`text-rose-600 dark:text-rose-400 text-xs mt-1`).

