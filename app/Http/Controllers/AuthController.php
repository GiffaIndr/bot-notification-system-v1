<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PricingComponent;
use App\Models\GroupMember;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function home()
    {
        // Jika belum login, tampilkan landing page
        if (!auth()->check()) {
            return view('landing');
        }

        // Jika sudah login dan punya akses manage, ke dashboard
        if ($this->hasManageAccess(auth()->user())) {
            return redirect()->route('dashboard.pages');
        }

        // Jika sudah login tapi bukan owner, tampilkan home page dengan groups
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $groups = $user->groups()->withPivot('role_id')->latest()->get();

        return view('pages.home', compact('groups'));
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

        $pricing      = PricingComponent::pluck('price', 'key');
        $groups       = $user->groups()->withPivot('role_id')->take(4)->get();
        $totalGroups  = $user->groups()->count();
        $subscription = $user->activeSubscription()->first();
        $groupCount   = GroupMember::where('user_id', auth()->id())
            ->whereHas('role', fn($q) => $q->where('is_owner', true))
            ->count();
        $maxGroup     = $subscription ? $subscription->max_groups : 0;

        return view('pages.dashboard', compact(
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
            return redirect()->route('landing')->with('success', 'berhasil login!');
        } else {
            return redirect()->back()->with('failed', 'gagal login, silahkan cek kembali');
        }
    }
    public function registration(Request $request)
    {
        $request->validate(
            [
                'name' => 'required',
                'email' => 'required|email:dns',
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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $phone,
        ]);

        Auth::login($user);

        return redirect()->route('landing')->with('success', 'Akun berhasil dibuat. Selamat datang!');
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
