<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasku | Akses Grup & Notifikasi Tim Multi-Platform</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/tasku-theme.css') }}">

    <style>
        :root {
            --primary: var(--tasku-primary);
            --primary-dark: var(--tasku-deep);
            --slate-900: var(--tasku-deep);
            --slate-700: #2a5876;
            --slate-600: #446c85;
            --slate-400: #7da3b8;
            --bg-light: var(--tasku-bg);
            --white: #ffffff;
            --radius-lg: 24px;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--slate-900);
            background-color: var(--bg-light);
            line-height: 1.6;
        }

        .container {
            width: min(1200px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* --- NAVIGASI --- */
        .topbar { padding: 20px 0; position: sticky; top: 0; z-index: 1000; background: rgba(248, 250, 252, 0.8); backdrop-filter: blur(12px); }
        .topbar-inner { display: flex; align-items: center; justify-content: space-between; background: var(--white); padding: 8px 10px 8px 20px; border-radius: 999px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; font-family: 'Space Grotesk', sans-serif; font-weight: 700; color: var(--slate-900); font-size: 1.2rem; }
        .brand img { display: block; }

        .nav-links { display: flex; gap: 25px; }
        .nav-links a { text-decoration: none; color: var(--slate-600); font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .nav-links a:hover { color: var(--primary); }

        /* --- TOMBOL --- */
        .btn { padding: 12px 24px; border-radius: 999px; font-weight: 700; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; cursor: pointer; border: none; }
        .btn-primary { background: var(--primary); color: var(--white); }
        .btn-primary:hover { transform: translateY(-2px); background: var(--primary-dark); box-shadow: 0 10px 20px rgba(51, 118, 163, 0.2); }
        .btn-outline { background: transparent; border: 1px solid #e2e8f0; color: var(--slate-700); }

        /* --- HERO --- */
        .hero { padding: 80px 0; text-align: center; }
        .hero-badge { background: #e9f4fa; color: var(--primary); padding: 6px 16px; border-radius: 99px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 24px; }
        .hero h1 { font-family: 'Space Grotesk', sans-serif; font-size: clamp(2.5rem, 6vw, 4rem); line-height: 1.1; margin: 0 auto 24px; letter-spacing: -2px; }
        .hero p { font-size: 1.15rem; color: var(--slate-600); max-width: 60ch; margin: 0 auto 40px; }

        /* --- MOCKUP VISUAL --- */
        .mockup-container { margin-top: 50px; background: #fff; border-radius: var(--radius-lg); padding: 10px; border: 1px solid #e2e8f0; box-shadow: 0 40px 100px rgba(0,0,0,0.06); }
        .mockup-header { height: 40px; background: #f1f5f9; border-radius: 16px 16px 0 0; display: flex; align-items: center; padding: 0 20px; gap: 8px; }
        .dot { width: 10px; height: 10px; border-radius: 50%; background: #cbd5e1; }
        .mockup-body { padding: 40px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .mock-card { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 16px; padding: 24px; text-align: left; }
        .mock-card.center { border: 2px solid var(--primary); background: #f5f7ff; transform: scale(1.05); z-index: 2; }

        /* --- PENGINGAT (REMINDER) --- */
        .reminder-section { padding: 100px 0; background: var(--white); border-top: 1px solid #f1f5f9; }
        .reminder-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .reminder-preview { background: #f8fafc; border: 1px solid var(--primary); border-radius: 24px; padding: 32px; position: relative; box-shadow: 0 20px 40px rgba(51, 118, 163, 0.08); }
        .reminder-ad-badge { position: absolute; top: -15px; right: 20px; background: #ef4444; color: #fff; padding: 4px 14px; border-radius: 99px; font-size: 0.7rem; font-weight: 800; }

        /* --- HARGA (PRICING) --- */
        .pricing { padding: 100px 0; background-color: var(--bg-light); }
        .pricing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-top: 50px; }
        .p-card { background: #fff; border: 1px solid #e2e8f0; padding: 40px 30px; border-radius: var(--radius-lg); display: flex; flex-direction: column; transition: 0.3s; }
        .p-card:hover { border-color: var(--primary); transform: translateY(-5px); }
        .p-card.featured { background: var(--slate-900); color: #fff; border: none; position: relative; }
        .p-card.featured .price { color: var(--primary); }
        .p-card.featured::after { content: "REKOMENDASI"; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: var(--primary); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 4px 12px; border-radius: 99px; }

        .price { font-size: 2.8rem; font-weight: 800; font-family: 'Space Grotesk', sans-serif; margin-bottom: 5px; }
        .price span { font-size: 1rem; font-weight: 400; color: var(--slate-600); }
        .p-card.featured .price span { color: #94a3b8; }
        .feature-list { list-style: none; padding: 0; margin: 25px 0; flex-grow: 1; text-align: left; }
        .feature-list li { margin-bottom: 12px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; }
        .feature-list i { color: #22c55e; }

        @media (max-width: 991px) {
            .mockup-body, .reminder-layout, .pricing-grid { grid-template-columns: 1fr; }
            .reminder-layout { text-align: center; gap: 40px; }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="container">
            <div class="topbar-inner">
                <a href="#" class="brand">
                    <img src="{{ asset('logos/tasku_transparan_dengan_nama.png') }}" alt="Tasku"
                        style="height: 32px; width: auto; object-fit: contain;">
                </a>
                <nav class="nav-links d-none d-md-flex">
                    <a href="#fitur">Fitur</a>
                    <a href="#pricing">Harga</a>
                </nav>
                <div class="topbar-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <span class="hero-badge">⚡ Platform Siaran Tim No. 1</span>
                <h1>Satu Klik, Semua Grup Terupdate.<br>Tanpa Info yang Terlewat.</h1>
                <p>Tasku adalah pusat kendali informasi tim Anda. Kirim pengumuman serentak ke WhatsApp, Discord, dan Telegram tanpa perlu menyalin pesan satu per satu.</p>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 18px 48px; font-size: 1.1rem;">Mulai Sekarang — Gratis</a>

                <div class="mockup-container">
                    <div class="mockup-header">
                        <div class="dot"></div><div class="dot"></div><div class="dot"></div>
                    </div>
                    <div class="mockup-body">
                        <div class="mock-card">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                                <i class="fa-brands fa-whatsapp" style="color: #25D366; font-size: 1.5rem;"></i>
                                <span style="font-size:10px; font-weight:800; color:#22c55e;">BERHASIL</span>
                            </div>
                            <h6 style="font-weight:700; margin-bottom:5px;">Grup WhatsApp</h6>
                            <p style="font-size:0.8rem; color:var(--slate-600);">"Halo tim, info rapat hari ini diundur ke jam 2 ya!"</p>
                        </div>
                        <div class="mock-card center">
                            <span class="badge" style="background:var(--primary); color:#fff; font-size:10px; padding:4px 10px; border-radius:5px; margin-bottom:15px; display:inline-block;">SISTEM UTAMA</span>
                            <h5 style="font-weight:800; margin-bottom:10px;">Menyiarkan...</h5>
                            <p style="font-size:0.85rem; font-weight:600;">Mengirim ke 3 Platform sekaligus secara instan.</p>
                        </div>
                        <div class="mock-card">
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px;">
                                <i class="fa-brands fa-discord" style="color: #5865f2; font-size: 1.5rem;"></i>
                                <span style="font-size:10px; font-weight:800; color:var(--primary);">DITERIMA</span>
                            </div>
                            <h6 style="font-weight:700; margin-bottom:5px;">Kanal #info</h6>
                            <p style="font-size:0.8rem; color:var(--slate-600);">"Halo tim, info rapat hari ini diundur ke jam 2 ya!"</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="reminder-section" id="fitur">
            <div class="container">
                <div class="reminder-layout">
                    <div>
                        <span style="color:var(--primary); font-weight:800; font-size:0.85rem; letter-spacing:1px; text-transform:uppercase;">Fitur Unggulan</span>
                        <h2 style="font-family:'Space Grotesk', sans-serif; font-size:2.5rem; line-height:1.2; margin:20px 0;">Tugas Penting,<br>Takkan Terlewat Lagi.</h2>
                        <p style="color:var(--slate-600); margin-bottom:30px;">Kini Anda bisa memasang <b>Tenggat Waktu</b> pada setiap pengumuman. Aktifkan fitur <b>Pengingat Pintar</b> untuk kirim ulang notifikasi secara otomatis di H-1.</p>
                        <div style="display:grid; gap:15px;">
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div style="background:#eef2ff; color:var(--primary); width:40px; height:40px; border-radius:50%; display:grid; place-items:center;"><i class="fa fa-bell"></i></div>
                                <span style="font-weight:700; font-size:0.95rem;">Pengingat Otomatis H-1 & H-3</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div style="background:#f0fdf4; color:#22c55e; width:40px; height:40px; border-radius:50%; display:grid; place-items:center;"><i class="fa fa-tags"></i></div>
                                <span style="font-weight:700; font-size:0.95rem;">Kategorisasi Berdasarkan Prioritas</span>
                            </div>
                        </div>
                    </div>
                    <div class="reminder-preview">
                        <div class="reminder-ad-badge">PENGINGAT AKTIF</div>
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
                            <span class="badge" style="background:var(--slate-900); color:#fff; padding:5px 12px; border-radius:5px; font-size:10px;">TUGAS MENDESAK</span>
                            <i class="fa fa-sync fa-spin" style="color:var(--primary);"></i>
                        </div>
                        <h4 style="font-weight:800; margin-bottom:10px;">Laporan Progres Mingguan</h4>
                        <p style="font-size:0.9rem; color:var(--slate-600); margin-bottom:25px;">Jangan lupa kumpulkan tautan dokumen ke tabel sebelum besok malam.</p>
                        <div style="background:#fff; border-radius:12px; padding:15px; border:1px solid #e2e8f0; display:flex; justify-content:space-between;">
                            <div>
                                <small style="display:block; color:var(--slate-400); font-weight:700; font-size:9px; text-transform:uppercase;">Tenggat Waktu</small>
                                <span style="font-weight:800; color:#ef4444; font-size:0.95rem;">Besok, 23:59 WIB</span>
                            </div>
                            <div style="text-align:right;">
                                <small style="display:block; color:var(--slate-400); font-weight:700; font-size:9px; text-transform:uppercase;">Status</small>
                                <span class="badge" style="background:#eef2ff; color:var(--primary); font-size:10px;">KIRIM ULANG H-1</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pricing" id="pricing">
            <div class="container" style="text-align:center;">
                <h2 style="font-family:'Space Grotesk', sans-serif; font-size:2.5rem; margin-bottom:15px;">Investasi Sesuai Kebutuhan</h2>
                <p style="color:var(--slate-600);">Pilih paket yang paling sesuai dengan skala tim Anda saat ini.</p>

                <div class="pricing-grid">
                    <div class="p-card">
                        <h4>Paket Dasar</h4>
                        <div class="price">Rp 15rb <span>/bln</span></div>
                        <ul class="feature-list">
                            <li><i class="fa-solid fa-circle-check"></i> Maksimal 10 Anggota</li>
                            <li><i class="fa-solid fa-circle-check"></i> 1 Grup Terdedikasi</li>
                            <li><i class="fa-solid fa-circle-check"></i> Dasbor Manajemen</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline w-100" style="justify-content:center;">Pilih Dasar</a>
                    </div>

                    <div class="p-card featured">
                        <h4>Paket Kustom</h4>
                        <div class="price" style="font-size:2.2rem;">Bangun Sendiri</div>
                        <ul class="feature-list">
                            <li><i class="fa-solid fa-plus" style="color:var(--primary)"></i> Tambah per 5 Anggota</li>
                            <li><i class="fa-solid fa-plus" style="color:var(--primary)"></i> Pengingat Pintar Aktif</li>
                            <li><i class="fa-solid fa-plus" style="color:var(--primary)"></i> Pajak PPN 10% Transparan</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-primary w-100" style="justify-content:center; background:var(--primary);">Cek Kalkulator</a>
                    </div>

                    <div class="p-card">
                        <h4>Integrasi Bot</h4>
                        <div class="price">Rp 10rb<span>-an</span></div>
                        <ul class="feature-list">
                            <li><i class="fa-brands fa-whatsapp" style="color:#25d366"></i> WhatsApp (Segera Hadir)</li>
                            <li><i class="fa-brands fa-discord" style="color:#5865f2"></i> Discord (Rp 10rb)</li>
                            <li><i class="fa-brands fa-telegram" style="color:#0088cc"></i> Telegram (Rp 10rb)</li>
                        </ul>
                        <a href="{{ route('register') }}" class="btn btn-outline w-100" style="justify-content:center;">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </section>

        <section style="padding: 100px 0; text-align: center; background: var(--slate-900); color: #fff;">
            <div class="container">
                <h2 style="font-family:'Space Grotesk', sans-serif; font-size:2.5rem; margin-bottom:20px;">Siap Mengelola Tim Lebih Cepat?</h2>
                <p style="color:var(--slate-400); margin-bottom:40px;">Daftar sekarang dan nikmati kemudahan koordinasi tanpa hambatan.</p>
                <a href="{{ route('register') }}" class="btn btn-primary" style="padding: 20px 60px; font-size: 1.2rem; background:var(--primary);">Daftar Akun Tasku Sekarang</a>
            </div>
        </section>
    </main>

    <footer style="padding:60px 0; text-align:center; border-top:1px solid #e2e8f0; color:var(--slate-400); font-size:0.85rem;">
        <div class="container">
            <p>&copy; 2026 Platform Notifikasi Tasku. Dibuat untuk Kecepatan dan Produktivitas.</p>
        </div>
    </footer>
</body>
</html>
