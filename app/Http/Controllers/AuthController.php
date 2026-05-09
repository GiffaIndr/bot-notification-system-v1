<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PricingComponent;
use App\Models\GroupMember;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    private function buildHomePayload(User $user): array
    {
        $groups = $user->groups()
            ->withPivot('role_id')
            ->withCount('announcements')
            ->latest()
            ->get();

        $groupIds = $groups->pluck('id');

        $groupMemberships = GroupMember::with('role')
            ->where('user_id', $user->id)
            ->whereIn('group_id', $groupIds)
            ->get()
            ->keyBy('group_id');

        $latestAnnouncement = Announcement::with('group')
            ->whereIn('group_id', $groupIds)
            ->latest()
            ->first();

        return compact('groups', 'groupMemberships', 'latestAnnouncement');
    }

    public function home()
    {
        if (!auth()->check()) {
            $pricing = PricingComponent::cachedPrices();

            return view('landing', compact('pricing'));
        }

        return redirect()->route('groups.index');
    }

    public function index()
    {
        return view('auth.login');
    }

    public function dashboard()
    {
        return redirect()->route('groups.index');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function Auth(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ], [
            'email.exists' => 'email tidak tersedia',
            'password.required' => 'password tidak tersedia',
        ]);

        $users = $request->only('email', 'password');

        if (Auth::attempt($users)) {
            return redirect()->route('groups.index')->with('success', 'berhasil login!');
        }

        return redirect()->back()->with('failed', 'gagal login, silahkan cek kembali');
    }

    public function registration(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email:dns|unique:users,email',
                'password' => 'required|min:6|confirmed',
                'phone' => 'required|string|regex:/^62[0-9]{9,13}$/'
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password tidak boleh kosong',
                'phone.required' => 'Nomber whatsapp tidak boleh kosong',
                'password.min' => 'Password harus minimal 6 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sesuai',
            ]
        );

        $phone = $request->phone;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $verificationCode = (string) random_int(100000, 999999);

        $request->session()->put('pending_registration', [
            'name' => $request->name,
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'phone' => $phone,
        ]);
        $request->session()->put('register_email_verification', [
            'code' => $verificationCode,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]);

        $this->sendRegistrationVerificationCode(
            strtolower(trim($request->email)),
            $verificationCode,
            $request->name
        );

        return redirect()->route('register.verify.form')
            ->with('success', 'Kode verifikasi telah dikirim ke email kamu.');
    }

    public function showVerifyEmailForm(Request $request)
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return redirect()->route('register')
                ->with('failed', 'Sesi pendaftaran tidak ditemukan. Silakan isi formulir lagi.');
        }

        return view('auth.verify-email-code', [
            'email' => $pending['email'],
        ]);
    }

    public function verifyEmailCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode verifikasi wajib diisi.',
            'code.digits' => 'Kode verifikasi harus 6 digit.',
        ]);

        $pending = $request->session()->get('pending_registration');
        $verification = $request->session()->get('register_email_verification');

        if (!$pending || !$verification) {
            return redirect()->route('register')
                ->with('failed', 'Sesi verifikasi tidak ditemukan. Silakan daftar ulang.');
        }

        if (now()->greaterThan($verification['expires_at'])) {
            return back()->withErrors([
                'code' => 'Kode verifikasi sudah kedaluwarsa. Kirim ulang kode baru.',
            ]);
        }

        if ((string) $request->code !== (string) $verification['code']) {
            return back()->withErrors([
                'code' => 'Kode verifikasi tidak sesuai.',
            ]);
        }

        if (User::where('email', $pending['email'])->exists()) {
            $request->session()->forget(['pending_registration', 'register_email_verification']);

            return redirect()->route('login')
                ->with('failed', 'Email sudah terdaftar. Silakan login.');
        }

        $user = User::create([
            'name' => $pending['name'],
            'email' => $pending['email'],
            'password' => $pending['password'],
            'phone' => $pending['phone'],
            'email_verified_at' => now(),
        ]);

        $request->session()->forget(['pending_registration', 'register_email_verification']);

        Auth::login($user);

        return redirect()->route('groups.index')
            ->with('success', 'Registrasi berhasil. Email kamu sudah terverifikasi.');
    }

    public function resendVerificationCode(Request $request)
    {
        $pending = $request->session()->get('pending_registration');

        if (!$pending) {
            return redirect()->route('register')
                ->with('failed', 'Sesi pendaftaran tidak ditemukan. Silakan daftar ulang.');
        }

        $verificationCode = (string) random_int(100000, 999999);

        $request->session()->put('register_email_verification', [
            'code' => $verificationCode,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]);

        $this->sendRegistrationVerificationCode(
            $pending['email'],
            $verificationCode,
            $pending['name']
        );

        return back()->with('success', 'Kode verifikasi baru sudah dikirim ke email kamu.');
    }

    private function sendRegistrationVerificationCode(string $email, string $code, string $name): void
    {
        $subject = 'Kode Verifikasi Registrasi Tasku';
        $body = "Halo {$name},\n\n" .
            "Berikut kode verifikasi registrasi akun Tasku kamu:\n\n" .
            "Kode: {$code}\n\n" .
            "Kode berlaku 10 menit. Jangan bagikan kode ini ke siapa pun.\n\n" .
            "Salam,\nTim Tasku";

        Mail::raw($body, function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });
    }

    private function sendProfileUpdateVerificationCode(string $email, string $code, string $name): void
    {
        $subject = 'Kode OTP Verifikasi Perubahan Akun Tasku';
        $body = "Halo {$name},\n\n" .
            "Berikut kode OTP verifikasi perubahan data akun Tasku kamu:\n\n" .
            "Kode: {$code}\n\n" .
            "Kode berlaku 10 menit. Abaikan email ini jika kamu tidak melakukan perubahan akun.\n\n" .
            "Salam,\nTim Tasku";

        Mail::raw($body, function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });
    }

    public function profile(Request $request)
    {
        $pendingUpdate = $request->session()->get('pending_profile_update');
        $verification = $request->session()->get('profile_update_verification');

        return view('pages.account-profile', [
            'user' => auth()->user(),
            'pendingUpdate' => $pendingUpdate,
            'verification' => $verification,
        ]);
    }

    public function requestProfileUpdateOtp(Request $request)
    {
        $user = User::findOrFail(auth()->id());

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email:dns',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => 'required|string|regex:/^62[0-9]{9,13}$/',
            'new_password' => 'nullable|string|min:6|confirmed',
            'current_password' => 'required_with:new_password|string',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'phone.required' => 'Nomor WhatsApp wajib diisi.',
            'phone.regex' => 'Nomor WhatsApp harus diawali 62 dan formatnya valid.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak sesuai.',
            'current_password.required_with' => 'Password saat ini wajib diisi untuk ganti password.',
        ]);

        if ($request->filled('new_password') && !Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'Password saat ini tidak sesuai.',
            ])->withInput();
        }

        $normalizedEmail = strtolower(trim((string) $request->email));
        $phone = (string) $request->phone;
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $hasIdentityChange =
            $request->name !== $user->name ||
            $normalizedEmail !== strtolower((string) $user->email) ||
            $phone !== (string) $user->phone;
        $hasPasswordChange = $request->filled('new_password');

        if (!$hasIdentityChange && !$hasPasswordChange) {
            return back()->with('info', 'Tidak ada perubahan data untuk disimpan.');
        }

        $verificationCode = (string) random_int(100000, 999999);
        $otpTargetEmail = $normalizedEmail !== strtolower((string) $user->email)
            ? $normalizedEmail
            : (string) $user->email;

        $request->session()->put('pending_profile_update', [
            'name' => $request->name,
            'email' => $normalizedEmail,
            'phone' => $phone,
            'password' => $hasPasswordChange ? Hash::make((string) $request->new_password) : null,
        ]);

        $request->session()->put('profile_update_verification', [
            'code' => $verificationCode,
            'target_email' => $otpTargetEmail,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]);

        $this->sendProfileUpdateVerificationCode(
            $otpTargetEmail,
            $verificationCode,
            $user->name
        );

        return back()->with('success', 'Kode OTP verifikasi perubahan akun telah dikirim ke email tujuan.');
    }

    public function verifyProfileUpdateOtp(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ], [
            'code.required' => 'Kode OTP wajib diisi.',
            'code.digits' => 'Kode OTP harus 6 digit.',
        ]);

        $pendingUpdate = $request->session()->get('pending_profile_update');
        $verification = $request->session()->get('profile_update_verification');

        if (!$pendingUpdate || !$verification) {
            return back()->with('failed', 'Sesi verifikasi perubahan akun tidak ditemukan. Silakan kirim ulang OTP.');
        }

        if (now()->greaterThan($verification['expires_at'])) {
            return back()->withErrors([
                'code' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang OTP.',
            ]);
        }

        if ((string) $request->code !== (string) $verification['code']) {
            return back()->withErrors([
                'code' => 'Kode OTP tidak sesuai.',
            ]);
        }

        $user = User::findOrFail(auth()->id());

        if ($pendingUpdate['email'] !== $user->email && User::where('email', $pendingUpdate['email'])->exists()) {
            $request->session()->forget(['pending_profile_update', 'profile_update_verification']);

            return back()->with('failed', 'Email tujuan sudah digunakan akun lain.');
        }

        $user->name = $pendingUpdate['name'];
        $user->email = $pendingUpdate['email'];
        $user->phone = $pendingUpdate['phone'];

        if (!empty($pendingUpdate['password'])) {
            $user->password = $pendingUpdate['password'];
        }

        $user->save();

        $request->session()->forget(['pending_profile_update', 'profile_update_verification']);

        return back()->with('success', 'Profil berhasil diperbarui dan diverifikasi OTP.');
    }

    public function resendProfileUpdateOtp(Request $request)
    {
        $pendingUpdate = $request->session()->get('pending_profile_update');
        $verification = $request->session()->get('profile_update_verification');

        if (!$pendingUpdate || !$verification) {
            return back()->with('failed', 'Tidak ada permintaan perubahan akun yang menunggu verifikasi.');
        }

        $verificationCode = (string) random_int(100000, 999999);
        $targetEmail = (string) ($verification['target_email'] ?? auth()->user()->email);

        $request->session()->put('profile_update_verification', [
            'code' => $verificationCode,
            'target_email' => $targetEmail,
            'expires_at' => now()->addMinutes(10)->toDateTimeString(),
        ]);

        $this->sendProfileUpdateVerificationCode(
            $targetEmail,
            $verificationCode,
            auth()->user()->name
        );

        return back()->with('success', 'Kode OTP baru sudah dikirim ke email tujuan.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function update(Request $request, auth $auth)
    {
        //
    }

    public function destroy(auth $auth)
    {
        //
    }
}
