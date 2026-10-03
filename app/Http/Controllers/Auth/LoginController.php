<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['pin' => ['required', 'string', 'min:4', 'max:8']]);
        $pin = $data['pin'];

        $user = User::query()
            ->whereNotNull('pin')
            ->where('is_active', true)
            ->get()
            ->first(fn (User $u) => Hash::check($pin, $u->pin));

        if (! $user) {
            return back()->withErrors(['pin' => 'PIN salah. Coba lagi.'])->onlyInput();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->route($user->isBos() ? 'dashboard' : 'laporan.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
