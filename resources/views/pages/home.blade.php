@extends('layout.cdn')

@section('content2')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Manrope:wght@400;600;700;800&display=swap');

        :root {
            --bg: #eef8ff;
            --ink: #102022;
            --ink-soft: #52666c;
            --surface: #ffffff;
            --line: #cfe6ff;
            --primary: #00b7ff;
            --primary-dark: #008fe0;
            --muted-chip: #e6f4ff;
        }

        .home-shell {
            min-height: 100vh;
            padding: 28px 12px 40px;
            color: var(--ink);
            font-family: 'Manrope', sans-serif;
            background:
                radial-gradient(circle at 6% 6%, #c8ebff 0, transparent 34%),
                radial-gradient(circle at 92% 15%, #ffe6c8 0, transparent 24%),
                radial-gradient(circle at 80% 86%, #bde3ff 0, transparent 30%),
                var(--bg);
        }

        .home-wrap {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }

        .topbar {
            margin-bottom: 18px;
        }

        .topbar-inner {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #ffffff;
            border-radius: 999px;
            box-shadow: 0 10px 24px rgba(12, 59, 48, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px 10px 18px;
            gap: 12px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            letter-spacing: 0.01em;
            color: #112b26;
        }

        .brand .logo {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            background: linear-gradient(145deg, var(--primary), #4dd2ff);
            color: #fff;
            display: grid;
            place-items: center;
            box-shadow: 0 8px 16px rgba(0, 151, 255, 0.35);
        }

        .btn-home {
            border: 0;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 999px;
            padding: 9px 14px;
        }

        .btn-home:hover {
            transform: translateY(-1px);
        }

        .btn-home-primary {
            color: #fff;
            background: linear-gradient(145deg, var(--primary), #0096ff);
            box-shadow: 0 10px 24px rgba(0, 151, 255, 0.35);
        }

        .btn-home-ghost {
            color: var(--ink);
            background: #fff;
            border: 1px solid #d1e2d8;
        }

        .btn-home-ghost:hover {
            background: #f5fbff;
            border-color: #b9dcfb;
        }

        .home-main {
            padding: 0;
        }

        .welcome {
            margin-bottom: 20px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: var(--muted-chip);
            color: #0b78c7;
            border: 1px solid #b9dcfb;
            padding: 7px 12px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 12px;
        }

        .welcome h5 {
            margin: 0 0 8px;
            font-weight: 700;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(1.45rem, 2.2vw, 2.2rem);
            letter-spacing: -0.02em;
        }

        .welcome p {
            margin: 0;
            color: var(--ink-soft);
            max-width: 68ch;
        }

        .section-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: var(--primary-dark);
        }

        .section-note {
            color: #64748b;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .section-block {
            margin-bottom: 18px;
            padding: 14px 14px 16px;
            border-top: 1px solid #d7e9fb;
            border-left: 4px solid #b8dcfb;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.78), rgba(241, 249, 255, 0.62));
            border-radius: 10px;
        }

        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
        }

        .mini-chip {
            border-radius: 999px;
            background: #e6f4ff;
            color: #0b78c7;
            border: 1px solid #b9dcfb;
            padding: 4px 9px;
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .group-list-title {
            margin: 0 0 12px;
            font-size: 1rem;
            font-weight: 700;
        }

        .group-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #cde3f8;
        }

        .group-item:last-child {
            border-bottom: 0;
        }

        .role-pill {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 4px 10px;
        }

        .home-input {
            border-radius: 12px;
            border: 1px solid #c8def2;
            min-height: 46px;
        }

        .home-input:focus {
            border-color: #8acbff;
            box-shadow: 0 0 0 .2rem rgba(0, 151, 255, 0.17);
        }

        @media (max-width: 768px) {
            .topbar-inner {
                border-radius: 16px;
            }

            .brand span {
                display: none;
            }
        }
    </style>

    <div class="home-shell">
        <div class="home-wrap">
            <div class="topbar">
                <div class="topbar-inner">
                    <div class="brand">
                        <span class="logo"><i class="fa-solid fa-bolt"></i></span>
                        <span>Tasku Home</span>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="btn-home btn-home-ghost" type="submit">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            @if (session('info'))
                <div class="alert alert-info">{{ session('info') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="home-main">
                <div class="welcome">
                    <span class="hero-tag"><i class="fa-solid fa-wave-square"></i> Home Workspace</span>
                    <h5>Welcome, {{ auth()->user()->name }}</h5>
                    <p>Gabung ke group yang sudah ada, atau buat group baru kalau kamu ingin mulai mengelola tim.</p>
                </div>

                <section class="section-block">
                    <div class="section-head">
                        <h6 class="section-title"><i class="fa-solid fa-user-plus"></i> Join Group</h6>
                        <span class="mini-chip">Invite Code</span>
                    </div>
                    <p class="section-note">Masukkan kode undangan dari admin.</p>
                    <form action="/join" method="POST" class="row g-2">
                        @csrf
                        <div class="col-12">
                            <input type="text" name="code" class="form-control home-input"
                                placeholder="Contoh: A1B2C3D4" required>
                        </div>
                        <div class="col-12 d-grid">
                            <button class="btn-home btn-home-primary" type="submit">
                                <i class="fa-solid fa-arrow-right-to-bracket"></i>
                                Join Group
                            </button>
                        </div>
                    </form>
                </section>

                <section class="section-block">
                    <div class="section-head">
                        <h6 class="section-title"><i class="fa-solid fa-layer-group"></i> Create Group</h6>
                        <span class="mini-chip">Manage</span>
                    </div>
                    <p class="section-note">Untuk membuat group baru, lanjutkan ke dashboard manage.</p>
                    <div class="d-grid">
                        <a href="/dashboard" class="btn-home btn-home-ghost">
                            <i class="fa-solid fa-gauge-high"></i>
                            Go to Dashboard
                        </a>
                    </div>
                </section>

                <section>
                    <h6 class="group-list-title">Group Saya</h6>

                    @forelse ($groups as $group)
                        @php
                            $memberRole = \App\Models\GroupMember::where('group_id', $group->id)
                                ->where('user_id', auth()->id())
                                ->with('role')
                                ->first();
                        @endphp

                        <div class="group-item">
                            <div>
                                <div class="fw-semibold">{{ $group->name }}</div>
                                @if ($memberRole?->role)
                                    <span class="role-pill"
                                        style="background-color: {{ $memberRole->role->color }}20; color: {{ $memberRole->role->color }};">
                                        {{ $memberRole->role->name }}
                                    </span>
                                @endif
                            </div>

                            <a href="/groups/{{ $group->id }}" class="btn btn-outline-primary btn-sm">Buka</a>
                        </div>
                    @empty
                        <div class="text-muted">Belum ada group. Join dulu pakai kode, atau buat group baru.</div>
                    @endforelse
                </section>
            </div>
        </div>
    </div>
@endsection
