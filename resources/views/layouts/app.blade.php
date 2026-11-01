<!DOCTYPE html>
<html lang="id" data-theme="emerald">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>@yield('title', 'Finora — Financial Organizer & Record Assistant')</title>
    <meta name="description" content="Aplikasi pencatatan keuangan pribadi minimalis, multi-wallet, dan transparan.">

    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Script Inline Pencegah Flash Tema Gelap -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            document.documentElement.setAttribute('data-theme', 'night');
        } else {
            document.documentElement.classList.remove('dark');
            document.documentElement.setAttribute('data-theme', 'emerald');
        }
    </script>
</head>
<body 
    x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.darkMode = !this.darkMode;
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
                document.documentElement.setAttribute('data-theme', 'night');
                localStorage.setItem('theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.setAttribute('data-theme', 'emerald');
                localStorage.setItem('theme', 'light');
            }
        }
    }" 
    class="bg-base-200 text-base-content font-sans antialiased min-h-screen flex flex-col selection:bg-primary selection:text-primary-content"
>
    <!-- Header Navigasi Finora (DaisyUI Navbar) -->
    <header class="navbar bg-base-100/95 border-b border-base-300 sticky top-0 z-40 backdrop-blur-md transition-colors duration-200 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl w-full mx-auto flex items-center justify-between">
            <!-- Brand & Logo -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-lg p-1">
                    <div class="w-10 h-10 rounded-xl bg-primary text-primary-content flex items-center justify-center font-bold text-xl shadow-xs">
                        F
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-extrabold tracking-tight text-base-content leading-none">Finora</span>
                        <span class="text-[11px] font-medium opacity-60 mt-0.5">Financial Organizer</span>
                    </div>
                </a>
            </div>

            <!-- Navigasi Utama Desktop -->
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
                <a 
                    href="{{ route('dashboard') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('dashboard') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Dashboard
                </a>
                <a 
                    href="{{ route('transactions.index') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('transactions.*') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Mutasi
                </a>
                <a 
                    href="{{ route('wallets.index') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('wallets.*') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Dompet & Rekening
                </a>
                <a 
                    href="{{ route('recurring.index') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('recurring.*') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Tagihan
                </a>
                <a 
                    href="{{ route('loans.index') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('loans.*') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Hutang Piutang
                </a>
                <a 
                    href="{{ route('history.index') }}" 
                    class="transition-colors pb-1.5 {{ request()->routeIs('history.*') ? 'text-primary border-b-2 border-primary' : 'text-base-content/70 hover:text-primary' }}"
                >
                    Riwayat
                </a>
            </nav>

            <!-- Aksi Header: Mode Gelap / Profil Pengguna / Logout -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Toggle Tema Terang / Gelap (DaisyUI btn-ghost btn-circle) -->
                <button 
                    type="button" 
                    @click="toggleTheme()" 
                    class="btn btn-ghost btn-circle btn-sm"
                    :aria-label="darkMode ? 'Beralih ke mode terang' : 'Beralih ke mode gelap'"
                >
                    <svg x-show="darkMode" class="w-5 h-5 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                    </svg>
                    <svg x-show="!darkMode" class="w-5 h-5 text-base-content" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                    </svg>
                </button>

                <!-- Profil User (DaisyUI Avatar) -->
                <div class="flex items-center gap-2 pl-2 border-l border-base-300">
                    <div class="avatar avatar-placeholder">
                        <div class="w-8 h-8 rounded-full bg-primary/10 text-primary font-bold text-xs border border-primary/20 flex items-center justify-center">
                            <span class="inline-flex items-center justify-center leading-none text-center select-none">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                    </div>
                    <span class="hidden sm:inline-block text-xs font-bold text-base-content truncate max-w-[140px]">
                        {{ Auth::user()->name }}
                    </span>
                </div>

                <!-- Tombol Keluar (DaisyUI btn-outline) -->
                <form method="POST" action="{{ route('logout') }}" class="ml-1">
                    @csrf
                    <button type="submit" class="btn btn-outline btn-sm">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Konten Halaman Utama -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 pb-24 md:pb-12 space-y-6">
        <!-- Flash Alert Sukses / Galat (DaisyUI alert alert-soft) -->
        @if (session('success'))
            <div role="alert" class="alert alert-success alert-soft shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-semibold">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div role="alert" class="alert alert-error alert-soft shadow-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <div class="text-sm">
                    @foreach ($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bilah Navigasi Bawah Khusus Layar Ponsel (DaisyUI Dock) -->
    <nav class="dock md:hidden bg-base-100 border-t border-base-300 z-40 fixed bottom-0 left-0 right-0" aria-label="Navigasi Bawah">
        <!-- 1. Dashboard -->
        <a 
            href="{{ route('dashboard') }}" 
            class="{{ request()->routeIs('dashboard') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span class="dock-label">Beranda</span>
        </a>

        <!-- 2. Mutasi -->
        <a 
            href="{{ route('transactions.index') }}" 
            class="{{ request()->routeIs('transactions.*') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5 7.5 3m0 0L12 7.5M7.5 3v13.5m13.5 0L16.5 21m0 0L12 16.5m4.5 4.5V7.5" />
            </svg>
            <span class="dock-label">Mutasi</span>
        </a>

        <!-- 3. Dompet -->
        <a 
            href="{{ route('wallets.index') }}" 
            class="{{ request()->routeIs('wallets.*') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
            </svg>
            <span class="dock-label">Dompet</span>
        </a>

        <!-- 4. Tagihan -->
        <a 
            href="{{ route('recurring.index') }}" 
            class="{{ request()->routeIs('recurring.*') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span class="dock-label">Tagihan</span>
        </a>

        <!-- 5. Hutang -->
        <a 
            href="{{ route('loans.index') }}" 
            class="{{ request()->routeIs('loans.*') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span class="dock-label">Hutang</span>
        </a>

        <!-- 6. Riwayat -->
        <a 
            href="{{ route('history.index') }}" 
            class="{{ request()->routeIs('history.*') ? 'dock-active text-primary font-bold' : 'text-base-content/70' }}"
        >
            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <span class="dock-label">Riwayat</span>
        </a>
    </nav>
</body>
</html>
