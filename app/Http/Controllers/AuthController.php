<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'dashboard' : 'transactions.index');
        }
        $appName = Setting::get('app_name', 'Sparepart MS');
        return view('auth.login', compact('appName'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        if (! $user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput($request->only('username'));
        }

        if (! $user->active) {
            return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi administrator.'])->withInput($request->only('username'));
        }

        if (! Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return back()->withErrors(['username' => 'Password salah.'])->withInput($request->only('username'));
        }

        $request->session()->regenerate();
        $home = auth()->user()->isAdmin() ? route('dashboard') : route('transactions.index');
        return redirect()->intended($home);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
