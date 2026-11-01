<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan halaman formulir registrasi.
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pembuatan akun baru pengguna.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                Password::min(8)->numbers()->symbols(),
                'confirmed',
            ],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.numbers' => 'Kata sandi wajib mengandung minimal satu angka.',
            'password.symbols' => 'Kata sandi wajib mengandung minimal satu simbol (misal: !@#$%^&*).',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'monthly_reset_day' => 1,
            'theme_preference' => 'light',
        ]);

        // Otomatis buat dompet Uang Tunai dengan saldo awal Rp 0
        $user->wallets()->create([
            'name' => 'Uang Tunai',
            'type' => 'cash',
            'balance' => 0,
            'color' => 'amber',
            'icon' => 'banknotes',
            'is_active' => true,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Akun berhasil dibuat. Selamat datang di Finora!');
    }
}
