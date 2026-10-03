<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.google.client_id')) {
            return redirect()->route('login')->withErrors(['pin' => 'Login Google belum dikonfigurasi.']);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        try {
            $google = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('login')->withErrors(['pin' => 'Gagal masuk dengan Google. Coba lagi.']);
        }

        $user = User::where('email', $google->getEmail())->first();

        if (! $user) {
            return redirect()->route('login')->withErrors(['pin' => 'Email Google belum terdaftar. Hubungi Admin.']);
        }

        if (! $user->is_active) {
            return redirect()->route('login')->withErrors(['pin' => 'Akun ini tidak aktif.']);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($user->isBos() ? 'dashboard' : 'laporan.index');
    }
}
