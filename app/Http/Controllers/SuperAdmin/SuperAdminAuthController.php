<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SuperAdminAuthController extends Controller
{
    /**
     * Tampilkan form login super admin
     */
    public function loginForm()
    {
        if (auth()->check() && auth()->user()->is_super_admin) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.auth.login');
    }

    /**
     * Handle login super admin
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $validated['email'])->first();

        // Validasi user dan password
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        // Cek apakah user adalah super admin
        if (!$user->is_super_admin) {
            return back()->withErrors([
                'email' => 'Akun ini bukan super admin.',
            ])->onlyInput('email');
        }

        // Cek email verification
        if (!$user->email_verified_at) {
            auth()->login($user);
            return redirect()->route('superadmin.verification.notice');
        }

        // Login user
        auth()->login($user, $request->boolean('remember'));

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Login berhasil! Selamat datang, ' . $user->name);
    }

    /**
     * Logout super admin
     */
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('superadmin.login')
            ->with('success', 'Logout berhasil.');
    }

    /**
     * Tampilkan form verifikasi email
     */
    public function verificationNotice()
    {
        if (!auth()->check()) {
            return redirect()->route('superadmin.login');
        }

        if (auth()->user()->email_verified_at) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('superadmin.auth.verify-email');
    }

    /**
     * Kirim ulang verification email
     */
    public function resendVerificationEmail(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('superadmin.login');
        }

        $user = auth()->user();

        if ($user->email_verified_at) {
            return redirect()->route('superadmin.dashboard');
        }

        // Kirim email verification
        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Email verifikasi telah dikirim. Silakan cek inbox Anda.');
    }

    /**
     * Verifikasi email via link
     */
    public function verifyEmail(Request $request)
    {
        if (!hash_equals(
            (string) $request->route('hash'),
            hash('sha256', $request->user()->getEmailForVerification())
        )) {
            return redirect()->route('superadmin.dashboard')
                ->with('error', 'Link verifikasi tidak valid.');
        }

        if ($request->user()->markEmailAsVerified()) {
            \Illuminate\Auth\Events\Verified::dispatch($request->user());
        }

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Email berhasil diverifikasi!');
    }

    /**
     * Recovery akun - ubah password
     */
    public function changePasswordForm()
    {
        return view('superadmin.auth.change-password');
    }

    /**
     * Handle ubah password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[a-zA-Z\d@$!%*?&]/',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return redirect()->route('superadmin.dashboard')
            ->with('success', 'Password berhasil diubah.');
    }
}
