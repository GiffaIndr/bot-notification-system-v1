<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PricingComponent;
use App\Models\GroupMember;
use App\Models\Announcement;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private function hasManageAccess(?User $user = null): bool
    {
        $userId = $user?->id ?? auth()->id();

        if (!$userId) {
            return false;
        }

        return GroupMember::where('user_id', $userId)
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->exists();
    }

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
        // Jika belum login, tampilkan landing page
        if (!auth()->check()) {
            $pricing = PricingComponent::pluck('price', 'key');
            return view('landing', compact('pricing'));
        }

        // Jika sudah login dan punya akses manage, ke dashboard
        if ($this->hasManageAccess(auth()->user())) {
            return redirect()->route('dashboard.pages');
        }

        // Jika sudah login tapi bukan owner, tampilkan home page dengan groups
        /** @var \App\Models\User $user */
        return view('pages.home', $this->buildHomePayload(auth()->user()));
    }

    public function homePage()
    {
        /** @var \App\Models\User $user */
        return view('pages.home', $this->buildHomePayload(auth()->user()));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }

    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        $plans        = Plan::orderBy('price')->get();
        $pricing      = PricingComponent::pluck('price', 'key');
        $groups       = $user->groups()->withPivot('role_id')->take(4)->get();
        $totalGroups  = $user->groups()->count();
        $subscription = $user->activeSubscription()->first();
        $groupCount   = GroupMember::where('user_id', auth()->id())
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->count();
        $maxGroup     = $subscription ? $subscription->max_groups : 0;

        return view('pages.dashboard', compact(
            'plans',
            'pricing',
            'groups',
            'totalGroups',
            'subscription',
            'groupCount',
            'maxGroup'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function register()
    {
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function Auth(Request $request)
    {
        $request->validate([
            'email' => 'required|exists:users,email',
            'password' =>  'required'
        ], [
            'email.exists' => 'email tidak tersedia',
            'password.required' => 'password tidak tersedia',
        ]);
        $users = $request->Only('email', 'password');
        if (Auth::attempt($users)) {
            return redirect()->route('home.pages')->with('success', 'berhasil login!');
        } else {
            return redirect()->back()->with('failed', 'gagal login, silahkan cek kembali');
        }
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

        return redirect()->route('home.pages')
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

    /**
     * Show the form for editing the specified resource.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, auth $auth)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(auth $auth)
    {
        //
    }
}
