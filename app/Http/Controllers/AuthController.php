<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('auth.login');
    }
    public function dashboard()
    {
         $plans = Plan::all();
        $groups = auth()->user()->groups()->withPivot('role')->take(2)->get();
        $totalGroups  = auth()->user()->groups()->count();
        $subscription = auth()->user()->activeSubscription()->with('plan')->first();
        $groupCount = auth()->user()->groups()->wherePivot('role', 'komti')->count();
        $maxGroup = $subscription ? $subscription->plan->max_group : 0;

        return view('pages.dashboard', compact('groups', 'subscription', 'groupCount', 'maxGroup', 'plans', 'totalGroups'));
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
            return redirect('dashboard')->with('success', 'berhasil login!');
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
                'password' => 'required',
            ],
            [
                'name.required' => 'Nama tidak boleh kosong',
                'email.required' => 'Email tidak boleh kosong',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'password.required' => 'Password tidak boleh kosong',
                'password.min' => 'Password harus minimal 6 karakter',
                'password.confirmed' => 'Konfirmasi password tidak sesuai',
            ]
        );
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        return redirect('/')->with('success', 'berhasil menambah akun, silahkan login');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(auth $auth)
    {
        //
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
