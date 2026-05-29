<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Controller AuthController
 * 
 * Menangani proses autentikasi (login dan logout) pengguna ke dalam sistem.
 */
class AuthController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * Jika pengguna sudah login, akan diarahkan ke dashboard atau halaman transaksi sesuai role.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route(Auth::user()->isAdmin() ? 'dashboard' : 'transactions.index');
        }
        $appName = Setting::get('app_name', 'Sparepart MS');
        return view('auth.login', compact('appName'));
    }

    /**
     * Memproses permintaan login dari pengguna.
     * Melakukan validasi kredensial, memeriksa status aktif pengguna, dan memulai sesi.
     * 
     * @param Request $request
     */
    public function login(Request $request)
    {
        // Validasi input form
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('username', $request->username)->first();

        // Cek apakah username terdaftar
        if (! $user) {
            return back()->withErrors(['username' => 'Username tidak ditemukan.'])->withInput($request->only('username'));
        }

        // Pastikan akun pengguna masih aktif
        if (! $user->active) {
            return back()->withErrors(['username' => 'Akun Anda tidak aktif. Hubungi administrator.'])->withInput($request->only('username'));
        }

        // Proses percobaan autentikasi (password check)
        if (! Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            return back()->withErrors(['username' => 'Password salah.'])->withInput($request->only('username'));
        }

        // Regenerasi session untuk mencegah session fixation
        $request->session()->regenerate();
        $home = auth()->user()->isAdmin() ? route('dashboard') : route('transactions.index');
        return redirect()->intended($home);
    }

    /**
     * Memproses permintaan logout pengguna dan menghapus sesi.
     * 
     * @param Request $request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
