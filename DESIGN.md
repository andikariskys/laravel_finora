# Design System & Direction (DESIGN.md) : Finora

**Aplikasi**: Finora (Personal Finance Assistant)  
**Karakter**: Minimalis, Ringan, Fungsional, Tenang, Responsif  
**Framework**: Tailwind CSS v4, DaisyUI, Alpine.js  

---

## 1. Filosofi & Arah Desain (Design Direction)

Finora dirancang untuk menghilangkan kecemasan finansial (financial anxiety). Desain tidak menggunakan visual generik yang berlebihan seperti gradien ungu-biru neon, kartu mengambang dengan bayangan tebal, atau dekorasi tanpa makna.

Finora mengutamakan:
1. **Kejelasan Hierarki Data**: Angka nominal, dompet sumber/tujuan, dan tanggal transaksi terbaca instan dalam sekali lirik.
2. **Kenyamanan Akses & Responsif**: Navigasi dan aksi cepat mudah dijangkau satu tangan pada layar ponsel (bottom navigation dan bottom sheet), serta bertransisi menjadi tata letak multi-kolom yang proporsional dan terstruktur pada tablet serta desktop.
3. **Ketenangan Visual**: Warna netral yang matang dengan aksen semantik yang tegas dan konsisten.

### Skala Dial Antislop
- **ENERGY: 2** (Refined, fokus, tenang, percaya diri sebagai utilitas finansial harian).
- **RHYTHM: 2** (Variasi terukur antara kartu ringkasan saldo, deretan dompet, dan daftar mutasi vertikal dengan jarak napas yang proporsional).
- **MOTION: 1** (Transisi tajam dan responsif berdurasi 150-200ms untuk modal bottom-sheet/dialog, tab, dan toggle dark mode; tanpa efek melayang atau animasi dekoratif yang memperlambat alur kerja).

---

## 2. Palet Warna & Tema (Light & Dark Themes)

Aplikasi mendukung dua tema resmi DaisyUI yang disesuaikan secara presisi untuk menjamin kontras tinggi (WCAG AA rasio > 4.5:1):

### 2.1. Tema Terang (Light Theme : `emerald-clean`)
- **Latar Belakang Dasar (Base 100)**: `#F8FAFC` (Slate 50, bersih dan lembut untuk mata).
- **Latar Belakang Permukaan Kartu (Base 200)**: `#FFFFFF` (Putih murni dengan batas tepi tegas `#E2E8F0`).
- **Teks Utama (Base Content)**: `#0F172A` (Slate 900, kontras sangat tinggi).
- **Teks Sekunder**: `#475569` (Slate 600).
- **Warna Aksen Utama (Primary)**: `#0F766E` (Teal 700, melambangkan stabilitas finansial dan ketenangan).
- **Warna Kontras Tombol Utama**: `#FFFFFF`.

### 2.2. Tema Gelap (Dark Theme : `night-slate`)
- **Latar Belakang Dasar (Base 100)**: `#090D16` (Deep Midnight Slate, bukan hitam pekat pekat AMOLED murahan).
- **Latar Belakang Permukaan Kartu (Base 200)**: `#131B2E` (Slate 900 pekat dengan garis batas `#1E293B`).
- **Teks Utama (Base Content)**: `#F8FAFC` (Slate 50).
- **Teks Sekunder**: `#94A3B8` (Slate 400).
- **Warna Aksen Utama (Primary)**: `#14B8A6` (Teal 500, tajam dan jelas di atas latar gelap).
- **Warna Kontras Tombol Utama**: `#090D16`.

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

Finora menggunakan tumpukan font sistem modern yang tidak memerlukan download eksternal berat:
- **Font Utama Antarmuka**: `Inter, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`.
- **Font Angka & Mata Uang (Tabular Numbers)**: Diberikan utility class `tabular-nums tracking-tight font-semibold` agar karakter angka memiliki lebar yang seragam secara vertikal saat ditampilkan dalam bentuk daftar.
- **Hierarki Skala Tipografi**:
  - `Display / Hero Balance`: 2rem (32px), `font-extrabold tracking-tight`.
  - `Header H1`: 1.25rem (20px), `font-bold`.
  - `Header H2 / Card Title`: 1rem (16px), `font-semibold`.
  - `Body Reguler`: 0.875rem (14px), `font-normal leading-relaxed`.
  - `Caption / Metadata / Label Dompet`: 0.75rem (12px), `font-medium text-secondary`.

---

## 4. Pola Tata Letak Responsif (Responsive Layout Principles)

Finora mengadopsi tata letak responsif adaptif yang menyesuaikan hierarki informasi secara proporsional di setiap ukuran layar (ponsel, tablet, desktop) tanpa membatasi layar lebar menjadi tampilan ponsel tiruan:

### 4.1. Strategi Kontainer & Breakpoints (Viewport Strategy)
- **Ponsel (< 768px / Mobile)**:
  - Kontainer satu kolom penuh (`w-full px-4 pb-24 pt-4`).
  - Elemen mengalir vertikal untuk kemudahan navigasi satu tangan saat scrolling.
  - Menggunakan bilah navigasi bawah (bottom navigation bar) tetap.
- **Tablet (768px - 1024px / breakpoint md)**:
  - Kontainer adaptif dua kolom (`max-w-4xl mx-auto px-6 py-6`).
  - Ringkasan saldo dan aksi cepat bersisian atau berjejer secara proporsional.
  - Kartu dompet disajikan dalam format grid dua kolom.
- **Desktop (>= 1024px / breakpoint lg & xl)**:
  - Kontainer kerja penuh yang terstruktur (`max-w-7xl mx-auto px-8 py-8`).
  - Tata letak multi-kolom yang produktif: Sidebar navigasi di sisi kiri, kolom utama untuk kartu saldo dan aksi transaksi, serta kolom kanan untuk ringkasan dompet dan pengingat aktif.
  - Ruang layar yang luas dimanfaatkan untuk visibilitas data mutasi tanpa scrolling berlebihan.

### 4.2. Sistem Navigasi Adaptif (Adaptive Navigation)
- **Pada Layar Seluler (< md)**:
  - Menggunakan bilah navigasi bawah tetap (`fixed bottom-0 left-0 right-0 z-30 h-16 bg-base-100/95 backdrop-blur border-t border-base-300`).
  - Tombol cepat **Tambah (+)** di tengah dengan warna aksen primer untuk akses instan ke modal transaksi.
  - Empat menu navigasi: Beranda (Dashboard), Dompet (Wallets), Pinjaman (Loans), Pengaturan (Settings).
- **Pada Layar Tablet & Desktop (>= md)**:
  - Bilah navigasi bawah disembunyikan (`hidden md:flex`).
  - Bertransisi menjadi Sidebar samping atau Navbar atas yang elegan, lengkap dengan label teks jelas, ikon representatif, status aktif indikatif, dan tombol aksi "Tambah Transaksi" yang terintegrasi.

### 4.3. Area Sentuh & Ergonomi (Touch Targets & Ergonomics)
- Setiap tombol, tautan navigasi, dan kontrol form memiliki tinggi minimal **44px** (`min-h-[44px] min-w-[44px]`) di semua mode layar.
- Jarak antar elemen interaktif minimal **8px** untuk mencegah salah klik pada perangkat layar sentuh maupun pointer mouse.

---

## 5. Komponen Inti Antarmuka (Core Components)

### 5.1. Hero Card: Total Saldo
- Latar kartu dengan warna permukaan tinggi (`bg-base-200 border border-base-300`).
- Label kecil: "Total Saldo Tersedia".
- Angka besar terformat: misal `Rp 12.450.000`.
- Ikon mata untuk menyembunyikan nominal menjadi `Rp ••••••••` dengan penyimpanan state di sesi lokal (Alpine.js).
- Indikator ringkas pergerakan bulan ini: Pemasukan (Hijau) vs Pengeluaran (Merah).

### 5.2. Wallet Capsule Cards & Lifecycle State (Daftar Dompet)
- Desain ringkas dan kompak dengan pembeda status visual:
  - **Dompet Aktif**: Warna kartu solid tajam, saldo tercetak tebal, tombol aksi cepat transfer dan opsi non-aktifkan.
  - **Dompet Non-Aktif**: Kartu dengan opasitas redup (opacity-60), badge abu-abu bertuliskan "Non-Aktif", dan tombol aksi utama "Aktifkan Kembali".
- Dilengkapi penanda jenis dompet (Bank, E-Money, Cash) dan inisial warna pembeda.
- Tab filter pada halaman Dompet: "Semua", "Aktif", dan "Non-Aktif".

### 5.3. Item Mutasi Transaksi (Transaction Row)
- Format baris daftar bersih:
  - Sisi kiri: Lingkaran ikon tipe transaksi (Pemasukan: panah turun hijau, Pengeluaran: panah naik merah, Transfer: panah bolak-balik biru, Penyesuaian Saldo: ikon timbangan/koin netral).
  - Bagian tengah: Judul transaksi (nama sumber / pengeluaran / penyesuaian reaktivasi), nama dompet, dan tanggal/jam.
  - Sisi kanan: Nominal (+ / -) dengan format mata uang yang tegas.

### 5.4. Modal & Dialog Interaktif Responsif (DaisyUI Modal)
- Formulir input transaksi dan pengaturan menggunakan kelas adaptif DaisyUI (`modal modal-bottom sm:modal-middle`):
  - **Pada Layar Ponsel**: Tampil sebagai Bottom Sheet yang meluncur dari bawah layar dengan drag handle indikator kecil, mudah dijangkau jempol.
  - **Pada Layar Tablet & Desktop**: Bertransisi otomatis menjadi dialog modal terpusat (*centered modal*) dengan lebar terukur (`sm:max-w-lg mx-auto`) yang fokus dan rapi.
- **Modal Tambah Dompet**:
  - Input Nama Dompet & Jenis (Bank / E-Money / Cash).
  - Input **Saldo Awal** langsung dengan prefix `Rp` dan format angka tabular besar, sehingga saldo awal langsung tercatat saat dompet dibuat.
- **Modal Reaktivasi Dompet (Khusus Dompet Non-Aktif)**:
  - Muncul saat pengguna menekan tombol "Aktifkan Kembali".
  - Menampilkan ringkasan: Nama dompet dan saldo terakhir saat dinonaktifkan.
  - Terdapat input: **Saldo Terkini (Penyesuaian Saldo)** yang otomatis terisi saldo lama dan dapat diedit langsung oleh pengguna agar saldo akun langsung sinkron dengan kondisi riil di bank/e-wallet.
  - Tombol aksi jelas: *"Aktifkan & Perbarui Saldo"*.

---

## 6. Standar Tiga State UI (Mandatory States)

1. **State Kosong (Empty State)**:
   - Jika belum ada transaksi: Tidak menampilkan tabel kosong yang kaku, melainkan ilustrasi ikon minimalis dengan pesan ramah: *"Belum ada transaksi di bulan ini. Mulai catat pemasukan atau pengeluaran pertama Anda."* disertai tombol CTA langsung *"Catat Transaksi"*.
2. **State Memuat (Loading State)**:
   - Form submit dilengkapi tombol `btn-disabled` dan indikator spinner kecil (`loading loading-spinner loading-xs`) untuk mencegah double-submit saat jaringan lambat.
3. **State Galat (Error State)**:
   - Pesan validasi form tampil tepat di bawah field input yang bermasalah dengan warna teks peringatan yang jelas (`text-error text-xs mt-1`).

---

## 7. Salinan Teks & Anti-Slop Writing Rules

- **Bebas dari Em Dash (`—`)**: Menggunakan tanda koma, titik, tanda titik dua, atau tanda kurung.
- **Tombol Aksi Spesifik**: Menghindari tombol generik seperti "Submit" atau "Klik Di Sini". Menggunakan teks tindakan yang jelas: "Simpan Transaksi", "Kirim Transfer", "Catat Pinjaman", "Perbarui Dompet".
- **Bahasa Indonesia Baku & Bersahabat**: Menggunakan istilah yang akrab bagi pengguna perbankan lokal Indonesia (Dompet Digital, Biaya Admin, Saldo Awal, Pelunasan).
