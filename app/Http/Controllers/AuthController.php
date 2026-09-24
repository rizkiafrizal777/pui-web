<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Tampilkan form login.
    public function showLogin()
    {
        // Jika sudah login, langsung arahkan ke dashboard/pos sesuai role
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }

        return view('auth.login');
    }
    // Validasi kredensial, simpan session, dan redirect.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            return $this->redirectByRole(Auth::user()->role);
        }
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Hapus session dan redirect ke halaman login.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Helper untuk mengarahkan pengguna sesuai rolenya.
     */
    private function redirectByRole($role)
    {
        if ($role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        } elseif ($role === 'kasir') {
            return redirect()->intended('/pos');
        }

        return redirect('/');
    }
}
