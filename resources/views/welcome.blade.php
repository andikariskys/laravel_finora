<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Finora — Financial Organizer & Record Assistant</title>
    <meta name="description" content="Aplikasi pencatatan keuangan pribadi minimalis, multi-wallet, dan transparan. Pantau saldo riil, biaya transfer admin, pinjaman, dan langganan bulanan tanpa kerumitan akuntansi.">

    <!-- Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Script Inline Pencegah Flash Tema Gelap -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body 
    x-data="landingApp()" 
    class="bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans antialiased min-h-screen flex flex-col selection:bg-emerald-500 selection:text-white"
>
    <!-- Navigasi Utama -->
    <header class="sticky top-0 z-50 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo & Brand Finora -->
                <a href="#hero" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 rounded-md p-1">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-xs group-hover:bg-emerald-700 transition-colors duration-200">
                        F
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-none">Finora</span>
                        <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-0.5">Record Assistant</span>
                    </div>
                </a>

                <!-- Navigasi Desktop -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600 dark:text-slate-300">
                    <a href="#fitur" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 py-2">
                        Fitur Utama
                    </a>
                    <a href="#keunggulan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 py-2">
                        Mengapa Finora
                    </a>
                    <a href="#keamanan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors duration-200 py-2">
                        Privasi & Performa
                    </a>
                </nav>

                <!-- Aksi Kanan: Theme Toggle & Autentikasi -->
                <div class="flex items-center gap-3">
                    <!-- Toggle Tema Terang/Gelap -->
                    <button 
                        type="button" 
                        @click="toggleTheme()" 
                        class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 transition-colors duration-200"
                        :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                    >
                        <!-- Ikon Matahari (Mode Terang) -->
                        <svg x-show="darkMode" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                        </svg>
                        <!-- Ikon Bulan (Mode Gelap) -->
                        <svg x-show="!darkMode" class="w-5 h-5 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                        </svg>
                    </button>

                    @auth
                        <!-- Tombol Dashboard jika sudah login -->
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="min-h-[44px] inline-flex items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors duration-200"
                        >
                            Dashboard
                        </a>
                    @else
                        <!-- Tombol Masuk (Outline di Desktop dan Mobile) -->
                        <a 
                            href="{{ route('login') }}" 
                            class="min-h-[44px] inline-flex items-center justify-center px-4 py-2 text-sm font-semibold rounded-lg border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-100 hover:border-slate-400 dark:hover:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 transition-colors duration-200"
                        >
                            Masuk
                        </a>

                        <!-- Tombol Daftar (Khusus Tampilan Desktop sm ke atas) -->
                        <a 
                            href="{{ route('register') }}" 
                            class="hidden sm:inline-flex min-h-[44px] items-center justify-center px-4 sm:px-5 py-2 text-sm font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900 transition-colors duration-200"
                        >
                            Daftar Akun
                        </a>
                    @endauth

                    <!-- Hamburger Button untuk Ponsel -->
                    <button 
                        type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="md:hidden min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500"
                        aria-label="Buka menu navigasi seluler"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Panel Navigasi Mobile -->
            <div 
                x-show="mobileMenuOpen" 
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="md:hidden border-t border-slate-200 dark:border-slate-800 py-4 space-y-2"
            >
                <a href="#fitur" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-md text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Fitur Utama
                </a>
                <a href="#keunggulan" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-md text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Mengapa Finora
                </a>
                <a href="#keamanan" @click="mobileMenuOpen = false" class="block px-3 py-2.5 rounded-md text-base font-medium text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Privasi & Performa
                </a>
                <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full min-h-[44px] inline-flex items-center justify-center px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full min-h-[44px] inline-flex items-center justify-center px-4 py-2 text-sm font-semibold border border-slate-300 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="w-full min-h-[44px] inline-flex items-center justify-center px-4 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-lg">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <!-- Hero Section dengan Background Foto Stock Uang Berlisensi Free Commercial Use -->
        <section id="hero" class="relative overflow-hidden bg-slate-950 text-white min-h-[600px] lg:min-h-[660px] flex items-center">
            <!-- Background Image dengan Lisensi Free Commercial Use (Unsplash: Micheile Henderson / Austin Distel) -->
            <div class="absolute inset-0 z-0">
                <img 
                    src="https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=2000&q=80" 
                    alt="Pencatatan keuangan personal dengan buku anggaran, kalkulator, dan uang tunai" 
                    class="w-full h-full object-cover object-center"
                    loading="eager"
                >
                <!-- Layer Overlay Gradien Berkontras Tinggi (Menjamin Rasio Kontras > 7:1) -->
                <div class="absolute inset-0 bg-gradient-to-r from-slate-950/95 via-slate-950/90 to-slate-950/80"></div>
                <!-- Layer Tambahan Bagian Bawah untuk Transisi Halus -->
                <div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-slate-950 to-transparent"></div>
            </div>

            <!-- Konten Utama Hero -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-24 w-full">
                <div class="max-w-3xl">
                    <!-- Label Status / Pengenal Aplikasi -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-6">
                        <span>Aplikasi Manajemen Keuangan Pribadi</span>
                    </div>

                    <!-- Headline Bernilai Nyata Tanpa Klise AI -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight mb-6">
                        Satu Tempat untuk Semua Dompet. Catat Finansial Riil Secara Simpel & Ringkas.
                    </h1>

                    <!-- Deskripsi Kontekstual Finora -->
                    <p class="text-base sm:text-lg lg:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                        Finora menyatukan rekening bank, e-wallet, dan uang tunai tanpa kerumitan akuntansi. Catat mutasi riil, pantau biaya admin transfer, kelola pinjaman, dan otomatisasi pengeluaran rutin bulanan.
                    </p>

                    <!-- Tombol Aksi Utama -->
                    <div class="mb-12">
                        @auth
                            <a 
                                href="{{ route('dashboard') }}" 
                                class="min-h-[48px] w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-colors duration-200"
                            >
                                Buka Dashboard Saya
                            </a>
                        @else
                            <a 
                                href="{{ route('register') }}" 
                                class="min-h-[48px] w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 text-base font-semibold rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-colors duration-200"
                            >
                                Mulai Catat Sekarang
                            </a>
                        @endauth
                    </div>

                    <!-- Tiga Fakta Nyata Finora -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6 border-t border-slate-800/80 text-slate-300">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mt-2 shrink-0"></div>
                            <div>
                                <span class="block text-sm font-semibold text-white">Multi-Wallet Lengkap</span>
                                <span class="text-xs text-slate-400">Rekening Bank, E-Money, dan Uang Tunai.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mt-2 shrink-0"></div>
                            <div>
                                <span class="block text-sm font-semibold text-white">Biaya Admin Riil</span>
                                <span class="text-xs text-slate-400">Mencatat biaya transfer agar saldo selalu sinkron.</span>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-400 mt-2 shrink-0"></div>
                            <div>
                                <span class="block text-sm font-semibold text-white">Rollover Bulanan</span>
                                <span class="text-xs text-slate-400">Reset fokus tiap bulan tanpa hapus arsip historis.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atribusi Lisensi Foto Komersial Gratis -->
            <div class="absolute bottom-3 right-4 z-10 text-[11px] text-slate-400 bg-slate-950/70 px-2 py-1 rounded border border-slate-800/60">
                Foto latar: Micheile Henderson via Unsplash (Lisensi Bebas Komersial)
            </div>
        </section>

        <!-- Bagian Fitur Utama & Tampilan Nyata Finora (Clean & Focused) -->
        <section id="fitur" class="py-16 sm:py-24 bg-white dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header Section -->
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block mb-2">
                        Fitur Inti Sesuai Kebutuhan Riil
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
                        Sederhana, Cepat, dan Transparan
                    </h2>
                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                        Finora fokus pada pencatatan finansial sehari-hari tanpa kerumitan debit-kredit. Dirancang agar saldo tercatat selalu sinkron dengan kondisi fisik di rekening bank dan e-wallet Anda.
                    </p>
                </div>

                <!-- Preview Visual Antarmuka Finora -->
                <div class="mb-16 max-w-5xl mx-auto bg-slate-900 text-white rounded-2xl p-6 sm:p-8 border border-slate-800 shadow-sm">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between pb-6 border-b border-slate-800 gap-4">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 block mb-1">Total Saldo Tersedia</span>
                            <div class="flex items-center gap-3">
                                <span class="text-3xl sm:text-4xl font-extrabold tracking-tight tabular-nums text-white">
                                    <span x-text="showBalance ? 'Rp 5.950.000' : 'Rp ********'"></span>
                                </span>
                                <!-- Tombol Sensor Mata -->
                                <button 
                                    type="button" 
                                    @click="showBalance = !showBalance" 
                                    class="min-h-[44px] min-w-[44px] flex items-center justify-center rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 transition-colors"
                                    :title="showBalance ? 'Sensor Saldo' : 'Tampilkan Saldo'"
                                >
                                    <svg x-show="showBalance" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg x-show="!showBalance" class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Ringkasan Arus Kas -->
                        <div class="flex items-center gap-6 text-xs sm:text-sm">
                            <div>
                                <span class="text-slate-400 block mb-0.5">Pemasukan Bulan Ini</span>
                                <span class="font-bold text-emerald-400 tabular-nums" x-text="showBalance ? '+ Rp 8.500.000' : 'Rp ********'"></span>
                            </div>
                            <div class="h-8 w-px bg-slate-800"></div>
                            <div>
                                <span class="text-slate-400 block mb-0.5">Pengeluaran Bulan Ini</span>
                                <span class="font-bold text-rose-400 tabular-nums" x-text="showBalance ? '- Rp 4.450.000' : 'Rp ********'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Baris Dompet & Mutasi Terakhir -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
                        <!-- Kolom Dompet -->
                        <div class="space-y-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                Dompet Terhubung
                            </span>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-blue-600 text-white font-bold text-xs flex items-center justify-center">BCA</div>
                                    <div>
                                        <span class="font-semibold text-sm block">BCA Prioritas</span>
                                        <span class="text-[11px] text-slate-400">Rekening Bank</span>
                                    </div>
                                </div>
                                <span class="font-bold text-sm tabular-nums" x-text="showBalance ? 'Rp 3.500.000' : 'Rp ********'"></span>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">GOP</div>
                                    <div>
                                        <span class="font-semibold text-sm block">GoPay</span>
                                        <span class="text-[11px] text-slate-400">E-Money</span>
                                    </div>
                                </div>
                                <span class="font-bold text-sm tabular-nums" x-text="showBalance ? 'Rp 350.000' : 'Rp ********'"></span>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-slate-700 text-white font-bold text-xs flex items-center justify-center">CSH</div>
                                    <div>
                                        <span class="font-semibold text-sm block">Uang Tunai Fisik</span>
                                        <span class="text-[11px] text-slate-400">Dompet Cash</span>
                                    </div>
                                </div>
                                <span class="font-bold text-sm tabular-nums" x-text="showBalance ? 'Rp 250.000' : 'Rp ********'"></span>
                            </div>
                        </div>

                        <!-- Kolom Mutasi Terkini -->
                        <div class="space-y-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                Mutasi Terakhir & Biaya Admin
                            </span>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm block text-white">Transfer BCA ke GoPay</span>
                                    <span class="text-[11px] text-slate-400">Top-up / Biaya Admin Rp 1.000 dicatat riil</span>
                                </div>
                                <span class="font-bold text-sm text-rose-400 tabular-nums">- Rp 101.000</span>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm block text-white">Pembayaran Proyek Lepas</span>
                                    <span class="text-[11px] text-slate-400">Pemasukan / Mandiri</span>
                                </div>
                                <span class="font-bold text-sm text-emerald-400 tabular-nums">+ Rp 3.500.000</span>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-800/80 border border-slate-700/60 flex items-center justify-between">
                                <div>
                                    <span class="font-semibold text-sm block text-white">Sewa Server Cloud (Recurring)</span>
                                    <span class="text-[11px] text-slate-400">Otomatisasi Tagihan / Setiap tanggal 1</span>
                                </div>
                                <span class="font-bold text-sm text-rose-400 tabular-nums">- Rp 185.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grid 4 Pilar Fitur Finora -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Fitur 1: Multi-Wallet & Siklus Hidup Dompet -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-lg mb-6">
                                01
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                                Multi-Wallet & Siklus Dompet Lengkap
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                                Tambahkan dompet tanpa batas: Rekening Bank, E-Money, hingga Uang Tunai fisik. Input saldo awal secara langsung saat dompet baru dibuat tanpa perlu transaksi dummy. Dompet yang sedang tidak dipakai dapat dinonaktifkan sementara dan dapat diaktifkan kembali kapan saja dengan penyesuaian saldo langsung.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-semibold text-emerald-600 dark:text-emerald-400">
                            Fleksibel & Terkendali / Saldo Awal Langsung & Reaktivasi Cepat
                        </div>
                    </div>

                    <!-- Fitur 2: Transparansi Transfer & Biaya Admin -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-lg mb-6">
                                02
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                                Transparansi Biaya Admin Transfer
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                                Perpindahan dana antar dompet (seperti top-up e-wallet atau transfer antar bank) sering memotong biaya admin yang tidak disadari. Finora mencatat biaya transfer tersebut secara eksplisit sebagai pengeluaran terpisah, sehingga saldo tercatat selalu presisi dengan saldo aplikasi perbankan Anda.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                            Pencatatan Presisi / Biaya Transfer & Top-up Selalu Tercatat Riil
                        </div>
                    </div>

                    <!-- Fitur 3: Manajemen Hutang & Piutang (Debt/Loan) -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/80 text-amber-700 dark:text-amber-300 flex items-center justify-center font-bold text-lg mb-6">
                                03
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                                Pencatatan Hutang-Piutang & Cicilan
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                                Sering meminjamkan uang ke rekan atau memiliki pinjaman yang dicicil bertahap? Finora mengelola dua arah transaksi: pinjaman yang diberikan (piutang) dan pinjaman yang diterima (hutang). Lengkap dengan status lunas sebagian, tanggal jatuh tempo, dan riwayat cicilan otomatis.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-semibold text-amber-600 dark:text-amber-400">
                            Kelola Hutang-Piutang / Riwayat Cicilan Bertahap & Jatuh Tempo
                        </div>
                    </div>

                    <!-- Fitur 4: Biaya Rutin Bulanan & Rollover Siklus -->
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-8 border border-slate-200 dark:border-slate-700 shadow-xs flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 flex items-center justify-center font-bold text-lg mb-6">
                                04
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                                Langganan Otomatis & Siklus Bersih
                            </h3>
                            <p class="text-slate-600 dark:text-slate-300 text-sm leading-relaxed mb-6">
                                Konfigurasikan pengeluaran berulang (seperti langganan server VPS, Spotify, atau biaya administrasi bank). Sistem otomatis mengeksekusi pemotongan saldo saat Anda membuka aplikasi pada tanggal jatuh tempo. Tanggal cutoff bulanan fleksibel disesuaikan dengan hari gajian Anda tanpa menghapus arsip masa lalu.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/60 text-xs font-semibold text-rose-600 dark:text-rose-400">
                            Otomatis & Teratur / Tagihan Rutin Terpotong Sesuai Jadwal
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Tabel Perbandingan Bersih: Finora vs Cara Lain -->
        <section id="keunggulan" class="py-16 sm:py-24 bg-slate-50 dark:bg-slate-900 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl mx-auto text-center mb-16">
                    <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-4">
                        Mengapa Menggunakan Finora?
                    </h2>
                    <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                        Bukan spreadsheet yang merepotkan di ponsel, bukan pula aplikasi akuntansi bisnis yang penuh dengan istilah debit-kredit rumit.
                    </p>
                </div>

                <div class="max-w-4xl mx-auto overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-xs">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/75 dark:bg-slate-900/50">
                                <th class="py-4 px-6 font-bold text-slate-900 dark:text-white">Kebutuhan Finansial</th>
                                <th class="py-4 px-6 text-slate-500 dark:text-slate-400">Spreadsheet Manual</th>
                                <th class="py-4 px-6 text-slate-500 dark:text-slate-400">Aplikasi Akuntansi Rumit</th>
                                <th class="py-4 px-6 font-extrabold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Finora</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60 text-slate-700 dark:text-slate-300">
                            <tr>
                                <td class="py-4 px-6 font-semibold">Alur Catat Harian</td>
                                <td class="py-4 px-6 text-slate-500">Lambat & sulit dibuka di layar ponsel</td>
                                <td class="py-4 px-6 text-slate-500">Ribet & berbelit-belit (banyak form wajib)</td>
                                <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Simpel, ringkas, & tanpa ribet</td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-semibold">Biaya Admin & Top-up</td>
                                <td class="py-4 px-6 text-slate-500">Sering lupa diinput terpisah</td>
                                <td class="py-4 px-6 text-slate-500">Harus jurnal ganda akun beban</td>
                                <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Otomatis terhubung & transparan</td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-semibold">Manajemen Dompet Non-Aktif</td>
                                <td class="py-4 px-6 text-slate-500">Kolom manual berantakan</td>
                                <td class="py-4 px-6 text-slate-500">Penyesuaian jurnal rumit</td>
                                <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Dialog penyesuaian saldo instan</td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-semibold">Privasi Nominal di Ruang Publik</td>
                                <td class="py-4 px-6 text-slate-500">Terbuka lebar di layar</td>
                                <td class="py-4 px-6 text-slate-500">Jarang tersedia</td>
                                <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">1-Klik Sensor Mata (Privacy Mode)</td>
                            </tr>
                            <tr>
                                <td class="py-4 px-6 font-semibold">Ergonomi Penggunaan</td>
                                <td class="py-4 px-6 text-slate-500">Perlu zoom & pinch dua tangan</td>
                                <td class="py-4 px-6 text-slate-500">Optimal hanya di desktop</td>
                                <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/50 dark:bg-emerald-950/20">Responsif: Ponsel, Tablet, & Desktop</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Privasi, Kinerja, & Arsitektur Nyata -->
        <section id="keamanan" class="py-16 sm:py-24 bg-white dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-800 transition-colors duration-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Privasi Terjaga Sepenuhnya</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            Data keuangan Anda tersimpan privat dan hanya dapat diakses oleh Anda. Tidak ada data yang dibagikan atau dijual, dan bebas dari pelacak iklan pihak ketiga.
                        </p>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Akses Cepat & Ringan</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            Aplikasi terbuka seketika tanpa jeda pemuatan yang lama. Hemat penggunaan kuota data internet dan tetap lancar digunakan saat mobilitas tinggi.
                        </p>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 sm:p-8 border border-slate-200 dark:border-slate-700 shadow-xs">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 flex items-center justify-center mb-4">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Ergonomis di Layar Ponsel</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                            Tombol berukuran pas untuk sentuhan satu tangan, teks berkontras tinggi yang nyaman di mata, serta tata letak adaptif di ponsel, tablet, maupun komputer.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Banner Aksi Penutup (CTA Section) -->
        <section class="py-16 sm:py-20 bg-slate-900 text-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight mb-4">
                    Mulai Rapikan Keuangan Pribadi Anda Hari Ini
                </h2>
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto mb-8 leading-relaxed">
                    Bebaskan diri dari kebingungan selisih saldo di akhir bulan. Bergabunglah dengan Finora dan rasakan ketenangan pencatatan finansial riil.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    @auth
                        <a 
                            href="{{ route('dashboard') }}" 
                            class="min-h-[48px] w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-base shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-colors"
                        >
                            Buka Dashboard Finora
                        </a>
                    @else
                        <a 
                            href="{{ route('register') }}" 
                            class="min-h-[48px] w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-base shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 transition-colors"
                        >
                            Daftar Akun Gratis
                        </a>
                        <a 
                            href="{{ route('login') }}" 
                            class="min-h-[48px] w-full sm:w-auto inline-flex items-center justify-center px-8 py-3 rounded-lg border border-slate-700 hover:border-slate-500 hover:bg-slate-800 text-slate-200 font-semibold text-base focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 transition-colors"
                        >
                            Masuk ke Finora
                        </a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Bersih & Fungsional -->
    <footer class="bg-white dark:bg-slate-950 border-t border-slate-200 dark:border-slate-800 py-12 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-slate-100 dark:border-slate-800/80">
                <!-- Brand Info -->
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-emerald-600 text-white flex items-center justify-center font-bold text-base">
                        F
                    </div>
                    <div>
                        <span class="font-bold text-slate-900 dark:text-white">Finora</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400 block">Financial Organizer & Record Assistant</span>
                    </div>
                </div>

                <!-- Navigasi Footer Nyata (Bukan Link Mati) -->
                <nav class="flex flex-wrap items-center justify-center gap-6 text-sm text-slate-600 dark:text-slate-400">
                    <a href="#hero" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Beranda</a>
                    <a href="#fitur" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Fitur Utama</a>
                    <a href="#keunggulan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Mengapa Finora</a>
                    <a href="#keamanan" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Privasi</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Masuk</a>
                        <a href="{{ route('register') }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Daftar</a>
                    @endauth
                </nav>
            </div>

            <!-- Baris Bawah: Hak Cipta & Informasi Ringkas -->
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-slate-400 gap-4">
                <span>&copy; {{ date('Y') }} Finora. Seluruh hak cipta dilindungi.</span>
                <span>Pencatatan Keuangan Pribadi yang Bersih, Cepat, dan Transparan.</span>
            </div>
        </div>
    </footer>

    <!-- Logika Antarmuka Pengguna Finora -->
    <script>
        function landingApp() {
            return {
                darkMode: document.documentElement.classList.contains('dark'),
                mobileMenuOpen: false,
                showBalance: true,

                // Toggle Tema Gelap / Terang (Persisten di LocalStorage)
                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    }
                }
            };
        }
    </script>
</body>
</html>
