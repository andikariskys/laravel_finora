<!DOCTYPE html>
<html lang="id" data-theme="emerald">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Akun Baru — Finora</title>
    <meta name="description" content="Pendaftaran akun baru Finora. Kelola dompet, mutasi biaya transfer, dan anggaran bulanan Anda.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

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
    x-data="registerFormApp()" 
    class="bg-base-200 text-base-content font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-primary selection:text-primary-content"
>
    <!-- Navigasi Minimalis Atas -->
    <header class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="btn btn-ghost btn-sm gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            <span>Kembali ke Beranda</span>
        </a>

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
    </header>

    <!-- Konten Utama: Kartu Pendaftaran (DaisyUI Card) -->
    <main class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
        <div class="w-full max-w-md">
            <!-- Brand Logo & Judul -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 mb-4 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary rounded-lg p-1">
                    <div class="w-12 h-12 rounded-xl bg-primary text-primary-content flex items-center justify-center font-bold text-2xl shadow-xs">
                        F
                    </div>
                </a>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-base-content">
                    Buat Akun Finora Baru
                </h1>
                <p class="text-sm text-base-content/70 mt-2">
                    Mulai rapikan pencatatan multi-dompet dan arus kas Anda.
                </p>
            </div>

            <!-- Kartu Formulir Pendaftaran (DaisyUI card) -->
            <div class="card bg-base-100 card-border shadow-sm">
                <div class="card-body p-6 sm:p-8">
                    <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                        @csrf

                        <!-- Field Nama Lengkap -->
                        <div class="space-y-2">
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-base-content/90">
                                Nama Lengkap
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                autocomplete="name"
                                placeholder="Contoh: Rangga Pratama"
                                class="input input-bordered w-full @error('name') input-error @enderror"
                            >
                            @error('name')
                                <p class="text-error text-xs mt-1.5 flex items-center gap-1 font-medium">
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field Alamat Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-base-content/90">
                                Alamat Email
                            </label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autocomplete="email"
                                placeholder="nama@email.com"
                                class="input input-bordered w-full @error('email') input-error @enderror"
                            >
                            @error('email')
                                <p class="text-error text-xs mt-1.5 flex items-center gap-1 font-medium">
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field Kata Sandi -->
                        <div class="space-y-2">
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-base-content/90">
                                Kata Sandi
                            </label>
                            <div class="relative">
                                <input 
                                    :type="showPassword ? 'text' : 'password'" 
                                    id="password" 
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter, ada angka & simbol"
                                    class="input input-bordered w-full pr-11 @error('password') input-error @enderror"
                                >
                                <!-- Tombol Toggle Intip Sandi -->
                                <button 
                                    type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="btn btn-ghost btn-xs btn-circle absolute right-2 top-2.5 text-base-content/60"
                                    :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                >
                                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg x-show="showPassword" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                @foreach ($errors->get('password') as $err)
                                    <p class="text-error text-xs mt-1.5 flex items-center gap-1 font-medium">
                                        <span>{{ $err }}</span>
                                    </p>
                                @endforeach
                            @else
                                <p class="text-xs text-base-content/60 mt-1.5">
                                    Minimal 8 karakter, wajib mengandung angka dan simbol (misal: @, #, $, !).
                                </p>
                            @enderror
                        </div>

                        <!-- Field Konfirmasi Kata Sandi -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-base-content/90">
                                Konfirmasi Kata Sandi
                            </label>
                            <div class="relative">
                                <input 
                                    :type="showPasswordConfirm ? 'text' : 'password'" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Ulangi kata sandi"
                                    class="input input-bordered w-full pr-11"
                                >
                                <!-- Tombol Toggle Intip Sandi Konfirmasi -->
                                <button 
                                    type="button" 
                                    @click="showPasswordConfirm = !showPasswordConfirm" 
                                    class="btn btn-ghost btn-xs btn-circle absolute right-2 top-2.5 text-base-content/60"
                                    :aria-label="showPasswordConfirm ? 'Sembunyikan konfirmasi sandi' : 'Tampilkan konfirmasi sandi'"
                                >
                                    <svg x-show="!showPasswordConfirm" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    <svg x-show="showPasswordConfirm" class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Tombol Submit Pendaftaran (DaisyUI btn-primary) -->
                        <div class="pt-2">
                            <button type="submit" class="btn btn-primary btn-block">
                                Daftar Akun
                            </button>
                        </div>
                    </form>

                    <!-- Tautan ke Halaman Masuk -->
                    <div class="mt-6 pt-6 border-t border-base-300 text-center">
                        <p class="text-sm text-base-content/70">
                            Sudah memiliki akun?
                            <a href="{{ route('login') }}" class="font-bold text-primary hover:underline ml-1">
                                Masuk ke Akun
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan Jaminan Keamanan & Privasi -->
            <p class="text-xs text-center text-base-content/60 mt-6">
                Data keuangan Anda tersimpan aman dan terisolasi secara privat.
            </p>
        </div>
    </main>

    <!-- Footer Ringkas -->
    <footer class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-base-content/50">
        &copy; {{ date('Y') }} Finora. Seluruh hak cipta dilindungi.
    </footer>

    <!-- Alpine.js Form Store -->
    <script>
        function registerFormApp() {
            return {
                darkMode: document.documentElement.classList.contains('dark'),
                showPassword: false,
                showPasswordConfirm: false,

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
            };
        }
    </script>
</body>
</html>
