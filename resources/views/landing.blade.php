<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Tasku | Platform Notifikasi Tim</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Manrope:wght@400;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg: #eef8ff;
            --ink: #102022;
            --ink-soft: #52666c;
            --surface: #ffffff;
            --line: #cfe6ff;
            --primary: #00b7ff;
            --primary-dark: #008fe0;
            --accent: #f59a2b;
            --muted-chip: #e6f4ff;
            --radius-xl: 24px;
            --radius-lg: 16px;
            --shadow-soft: 0 20px 40px rgba(9, 55, 44, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 6% 6%, #c8ebff 0, transparent 34%),
                radial-gradient(circle at 92% 15%, #ffe6c8 0, transparent 24%),
                radial-gradient(circle at 80% 86%, #bde3ff 0, transparent 30%),
                var(--bg);
            min-height: 100vh;
        }

        .container {
            width: min(1120px, calc(100% - 40px));
            margin: 0 auto;
        }

        .topbar {
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 100;
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
            gap: 14px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            letter-spacing: 0.01em;
            text-decoration: none;
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
            box-shadow: 0 8px 16px rgba(8, 120, 90, 0.35);
        }

        .main-menu {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .main-menu a {
            text-decoration: none;
            color: #274449;
            font-weight: 700;
            font-size: 0.87rem;
            padding: 8px 12px;
            border-radius: 999px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .main-menu a:hover {
            background: #e9f4ee;
            color: var(--primary-dark);
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .hamburger-btn {
            display: none;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            border: 1px solid #d1e2d8;
            background: #fff;
            color: var(--primary-dark);
            font-size: 1rem;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .mobile-nav {
            display: none;
            margin-top: 10px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid #e0ece5;
            border-radius: 18px;
            box-shadow: 0 14px 28px rgba(14, 52, 44, 0.12);
            padding: 12px;
        }

        .mobile-nav.show {
            display: block;
        }

        .mobile-links {
            display: grid;
            gap: 6px;
            margin-bottom: 10px;
        }

        .mobile-links a {
            text-decoration: none;
            color: #274449;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 10px 12px;
            border-radius: 10px;
            background: #f2f8f5;
            border: 1px solid #e0ece5;
        }

        .mobile-auth {
            display: grid;
            gap: 8px;
        }

        .mobile-auth .btn {
            width: 100%;
            justify-content: center;
        }

        .btn {
            border: 0;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            font-weight: 700;
            font-size: 0.92rem;
            border-radius: 999px;
            padding: 10px 16px;
        }

        .btn-ghost {
            color: var(--ink);
            background: #fff;
            border: 1px solid #d1e2d8;
        }

        .btn-primary {
            color: #fff;
            background: linear-gradient(145deg, var(--primary), #0096ff);
            box-shadow: 0 10px 24px rgba(0, 151, 255, 0.35);
        }

        .hero {
            padding: 56px 0 48px;
        }

        .hero-layout {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 22px;
            align-items: stretch;
        }

        .hero-copy {
            padding: 8px 6px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            background: #e6f4ff;
            color: #0b78c7;
            border: 1px solid #b9dcfb;
            padding: 7px 12px;
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.2rem, 5vw, 4.1rem);
            line-height: 1;
            margin: 18px 0 16px;
            letter-spacing: -0.02em;
            max-width: 16ch;
        }

        .lead {
            color: var(--ink-soft);
            font-size: 1.04rem;
            line-height: 1.72;
            margin: 0 0 24px;
            max-width: 58ch;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .hero-points {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .hero-point {
            border-radius: 12px;
            background: var(--muted-chip);
            border: 1px solid #c7e3ff;
            padding: 10px 12px;
        }

        .hero-point strong {
            display: block;
            font-size: 1.1rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .hero-point span {
            color: #4d7ea8;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .hero-visual {
            border-radius: 28px;
            background: linear-gradient(165deg, #003b73 0%, #0066cc 62%, #00b7ff 100%);
            color: #eaf7ff;
            box-shadow: var(--shadow-soft);
            padding: 28px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .visual-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.16);
            margin-bottom: 14px;
        }

        .visual-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            line-height: 1.2;
            margin: 0 0 20px;
        }

        .flow {
            display: grid;
            gap: 10px;
        }

        .flow-item {
            background: rgba(255, 255, 255, 0.11);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 11px 12px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .flow-number {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            font-size: 0.82rem;
            font-weight: 800;
            background: #9fe6ff;
            color: #004b8f;
        }

        .flow-item p {
            margin: 0;
            color: #eaf7ff;
            font-size: 0.9rem;
            line-height: 1.45;
        }

        .section {
            padding: 28px 0;
        }

        .section h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(1.5rem, 2.8vw, 2.2rem);
            margin: 0 0 8px;
            letter-spacing: -0.01em;
        }

        .section p.section-lead {
            margin: 0;
            color: var(--ink-soft);
            max-width: 68ch;
        }

        .feature-grid {
            margin-top: 18px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .feature-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: var(--radius-lg);
            padding: 18px;
            box-shadow: 0 10px 24px rgba(18, 53, 44, 0.07);
        }

        .feature-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            font-size: 1rem;
            background: #e6f4ff;
            color: var(--primary);
            margin-bottom: 12px;
        }

        .feature-card h3 {
            margin: 0 0 8px;
            font-size: 1rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .feature-card p {
            margin: 0;
            color: #5b6d74;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .cta {
            margin: 24px 0 42px;
            border-radius: var(--radius-xl);
            border: 1px solid #d6e8df;
            background: linear-gradient(110deg, #f0faf5 0%, #fff8ee 100%);
            padding: 26px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .cta h3 {
            margin: 0 0 6px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.4rem;
        }

        .cta p {
            margin: 0;
            color: #4f6369;
        }

        .footer {
            text-align: center;
            color: #6e8288;
            font-size: 0.86rem;
            padding-bottom: 30px;
        }

        @media (max-width: 1024px) {
            .hero-layout {
                grid-template-columns: 1fr;
            }

            h1 {
                max-width: 100%;
            }

            .feature-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                width: min(1120px, calc(100% - 28px));
            }

            .topbar-inner {
                border-radius: 18px;
                justify-content: space-between;
                padding: 14px;
            }

            .main-menu,
            .topbar-actions {
                display: none;
            }

            .hamburger-btn {
                display: inline-flex;
            }

            .hero-points,
            .feature-grid {
                grid-template-columns: 1fr;
            }

            .cta {
                flex-direction: column;
                align-items: flex-start;
                padding: 20px;
            }

            h1 {
                font-size: clamp(2rem, 9vw, 2.8rem);
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="container">
            <div class="topbar-inner">
                <a href="#" class="brand">
                    <span class="logo"><i class="fa-solid fa-bullhorn"></i></span>
                    <span>Tasku</span>
                </a>
                <nav class="main-menu">
                    <a href="#fitur">Fitur</a>
                    <a href="#cara-kerja">Cara Kerja</a>
                    <a href="#mulai">Mulai</a>
                </nav>
                <div class="topbar-actions">
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fa-solid fa-rocket"></i>
                        Daftar Gratis
                    </a>
                </div>
                <button class="hamburger-btn" id="hamburgerBtn" aria-label="Buka menu" aria-controls="mobileNav"
                    aria-expanded="false">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <div class="mobile-nav" id="mobileNav">
                <nav class="mobile-links">
                    <a href="#fitur">Fitur</a>
                    <a href="#cara-kerja">Cara Kerja</a>
                    <a href="#mulai">Mulai</a>
                </nav>
                <div class="mobile-auth">
                    <a href="{{ route('login') }}" class="btn btn-ghost">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">
                        <i class="fa-solid fa-rocket"></i>
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container hero-layout">
                <article class="hero-copy">
                    <span class="hero-tag"><i class="fa-solid fa-sparkles"></i> Platform Notifikasi Multi-Channel</span>
                    <h1>Tasku Bantu Tim Kirim Pengumuman Lebih Cepat dan Rapi</h1>
                    <p class="lead">
                        Kelola pengumuman, polling, role anggota, dan aktivitas grup untuk WhatsApp, Discord, dan
                        Telegram dari satu sistem yang praktis.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('register') }}" class="btn btn-primary">
                            <i class="fa-solid fa-circle-play"></i>
                            Coba Sekarang
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-ghost">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Saya Sudah Punya Akun
                        </a>
                    </div>
                    <div class="hero-points">
                        <div class="hero-point">
                            <strong>3+</strong>
                            <span>Integrasi Platform</span>
                        </div>
                        <div class="hero-point">
                            <strong>24/7</strong>
                            <span>Scheduler Aktif</span>
                        </div>
                        <div class="hero-point">
                            <strong>100%</strong>
                            <span>Kontrol dari Tasku</span>
                        </div>
                    </div>
                </article>

                <aside class="hero-visual" id="cara-kerja">
                    <div class="visual-chip">
                        <i class="fa-solid fa-signal"></i>
                        Automation Ready
                    </div>
                    <h2 class="visual-title">Alur penggunaan yang sederhana</h2>
                    <div class="flow">
                        <div class="flow-item">
                            <span class="flow-number">1</span>
                            <p>Buat grup notifikasi dan atur role sesuai kebutuhan tim.</p>
                        </div>
                        <div class="flow-item">
                            <span class="flow-number">2</span>
                            <p>Hubungkan bot WhatsApp, Discord, atau Telegram ke channel tujuan.</p>
                        </div>
                        <div class="flow-item">
                            <span class="flow-number">3</span>
                            <p>Kirim announcement terjadwal, polling, dan pantau log aktivitas.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        <section class="section" id="fitur">
            <div class="container">
                <h2>Fitur utama Tasku</h2>
                <p class="section-lead">
                    Dirancang untuk operasional komunitas, kelas, dan tim kerja yang butuh notifikasi konsisten dan
                    terukur.
                </p>

                <div class="feature-grid">
                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
                        <h3>Role dan Permission</h3>
                        <p>Atur siapa yang boleh membuat announcement, mengelola member, dan membuat polling.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <h3>Jadwal Berulang</h3>
                        <p>Dukung pengumuman sekali kirim atau berulang harian, mingguan, dan bulanan.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-file-circle-check"></i></div>
                        <h3>Lampiran File</h3>
                        <p>Kirim teks, gambar, dan dokumen dengan alur broadcast yang lebih efisien.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-square-poll-vertical"></i></div>
                        <h3>Polling Interaktif</h3>
                        <p>Buat voting untuk meningkatkan engagement anggota di setiap grup.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-shuffle"></i></div>
                        <h3>Random Picker</h3>
                        <p>Pilih peserta secara acak dari daftar member maupun list custom.</p>
                    </article>

                    <article class="feature-card">
                        <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
                        <h3>Activity Log</h3>
                        <p>Lacak aksi penting sebagai audit trail operasional tim secara real-time.</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="container" id="mulai">
            <div class="cta">
                <div>
                    <h3>Siap mulai pakai Tasku?</h3>
                    <p>Daftar akun, hubungkan bot, lalu kirim pengumuman pertamamu dalam hitungan menit.</p>
                </div>
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fa-solid fa-arrow-right"></i>
                    Buat Akun
                </a>
            </div>
        </section>
    </main>

    <footer class="footer">
        Tasku Notification Platform
    </footer>

    <script>
        const hamburgerBtn = document.getElementById('hamburgerBtn');
        const mobileNav = document.getElementById('mobileNav');

        if (hamburgerBtn && mobileNav) {
            hamburgerBtn.addEventListener('click', function() {
                const isOpen = mobileNav.classList.toggle('show');
                hamburgerBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                hamburgerBtn.innerHTML = isOpen ? '<i class="fa-solid fa-xmark"></i>' :
                    '<i class="fa-solid fa-bars"></i>';
            });

            mobileNav.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    mobileNav.classList.remove('show');
                    hamburgerBtn.setAttribute('aria-expanded', 'false');
                    hamburgerBtn.innerHTML = '<i class="fa-solid fa-bars"></i>';
                });
            });
        }
    </script>
</body>

</html>
