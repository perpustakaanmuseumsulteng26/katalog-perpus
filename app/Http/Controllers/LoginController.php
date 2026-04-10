<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password; // Wajib untuk kirim link
use Illuminate\Support\Facades\Hash;     // Wajib untuk enkripsi password baru
use Illuminate\Support\Str;              // Wajib untuk generate token
use App\Models\User;

class LoginController extends Controller
{
    // Menampilkan halaman login
    public function index()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->with('error', 'Email atau Password yang Anda masukkan salah!');
    }

    // ================================================================
    // --- FITUR LUPA PASSWORD (TAMBAHAN BARU) ---
    // ================================================================

    // 1. Menampilkan Form Minta Email
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Proses Kirim Link Reset ke Gmail
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Mengirim instruksi ke Laravel untuk membuat token dan kirim email via SMTP Gmail
        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Link reset password telah dikirim ke email Anda!')
            : back()->withErrors(['email' => 'Email tidak ditemukan dalam sistem kami.']);
    }

    // 3. Menampilkan Form Input Password Baru (Dari Link Email)
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    // 4. Proses Update Password Baru ke Database
    public function updatePassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password berhasil diubah! Silakan login kembali.')
            : back()->withErrors(['email' => 'Terjadi kesalahan atau token sudah kadaluarsa.']);
    }

    // ================================================================

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); 
    }
}