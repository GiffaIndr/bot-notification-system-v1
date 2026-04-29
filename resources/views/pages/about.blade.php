<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Tasku | Platform Kolaborasi Tim</title>

    <link rel="icon" type="image/png" href="{{ asset('logos/logo_transparan.png') }}" sizes="32x32">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;700&family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap"
        rel="stylesheet">
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

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

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
        .topbar {
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(12px);
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            padding: 8px 10px 8px 20px;
            border-radius: 999px;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--slate-900);
            font-size: 1.2rem;
        }

        .brand img {
            display: block;
            height: 32px;
            width: auto;
            object-fit: contain;
        }

        .nav-links {
            display: flex;
            gap: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--slate-600);
            font-weight: 600;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--primary);
        }

        .topbar-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: var(--primary-dark);
            box-shadow: 0 10px 20px rgba(51, 118, 163, 0.2);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #e2e8f0;
            color: var(--slate-700);
        }

        .btn-outline:hover {
            background: #f8fafc;
        }

        @media (min-width: 992px) {
            .hamburger {
                display: none !important;
            }

            .nav-links {
                gap: 25px !important;
                font-size: 0.9rem !important;
                order: unset !important;
                width: auto !important;
                flex-direction: row !important;
                max-height: none !important;
                overflow: visible !important;
                background: none !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
            }

            .nav-links a {
                padding: 0 !important;
                display: inline !important;
            }

            .topbar-inner {
                flex-wrap: nowrap;
            }
        }

        .hamburger {
            display: none;
            width: 32px;
            height: 32px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            background: none;
            border: none;
            cursor: pointer;
            order: 2;
        }

        .hamburger span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--slate-900);
            border-radius: 1px;
            transition: 0.3s;
        }

        .hamburger.active span:nth-child(1) {
            transform: rotate(45deg) translate(8px, 8px);
        }

        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }

        .hamburger.active span:nth-child(3) {
            transform: rotate(-45deg) translate(7px, -7px);
        }

        /* --- CONTENT --- */
        .about-section {
            padding: 80px 0;
        }

        .about-hero {
            text-align: center;
            margin-bottom: 60px;
        }

        .about-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--slate-900);
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .about-hero p {
            font-size: 1.1rem;
            color: var(--slate-600);
            margin: 0;
            max-width: 600px;
            margin: 0 auto;
        }

        .about-card {
            background: var(--white);
            border: 1px solid #cfe6ff;
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 32px;
            box-shadow: 0 10px 24px rgba(12, 59, 48, 0.08);
        }

        .about-card h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--slate-900);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .about-card h2 i {
            font-size: 1.8rem;
            color: var(--primary);
        }

        .about-card p {
            color: var(--slate-600);
            line-height: 1.8;
            font-size: 0.95rem;
            margin-bottom: 16px;
        }

        .about-card p:last-of-type {
            margin-bottom: 0;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .feature-list li {
            padding: 12px 0;
            padding-left: 32px;
            position: relative;
            color: var(--slate-600);
            line-height: 1.7;
        }

        .feature-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #16a34a;
            font-weight: bold;
            font-size: 1.3rem;
        }

        .security-badge {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            border: 1px solid #86efac;
            border-radius: 14px;
            margin: 20px 0;
        }

        .security-badge i {
            font-size: 1.5rem;
            color: #16a34a;
            flex-shrink: 0;
        }

        .security-badge strong {
            color: #15803d;
        }

        @media (max-width: 991px) {
            .hamburger {
                display: flex;
            }

            .topbar-inner {
                gap: 12px;
                padding: 8px;
                border-radius: 12px;
                flex-wrap: wrap;
            }

            .brand {
                gap: 8px;
                font-size: 1rem;
                flex: 1;
            }

            .brand img {
                height: 24px;
            }

            .nav-links {
                gap: 15px;
                font-size: 0.85rem;
                order: 3;
                width: 100%;
                flex-direction: column;
                gap: 8px;
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
                background: rgba(255, 255, 255, 0.95);
                padding: 0;
                margin: 0;
                border-radius: 8px;
                display: flex !important;
            }

            .nav-links.active {
                max-height: 300px;
                padding: 12px 0;
            }

            .nav-links a {
                padding: 8px 16px;
                display: block;
            }

            .topbar-actions {
                gap: 8px;
                order: 2;
            }

            .btn {
                padding: 8px 16px;
                font-size: 0.8rem;
                white-space: nowrap;
            }

            .about-section {
                padding: 40px 0;
            }

            .about-hero h1 {
                font-size: 1.8rem;
            }

            .about-card {
                padding: 24px;
            }

            .about-card h2 {
                font-size: 1.2rem;
            }

            .feature-list li {
                padding-left: 28px;
                font-size: 0.9rem;
            }

            .security-badge,
            .midtrans-badge {
                flex-direction: column;
                text-align: center;
            }

            .security-badge i,
            .midtrans-badge i {
                margin: 0;
            }

            .midtrans-logo img {
                height: 28px;
            }
        }

        @media (max-width: 640px) {
            .topbar {
                padding: 12px 0;
            }

            .topbar-inner {
                padding: 8px;
                gap: 8px;
            }

            .brand {
                gap: 6px;
                font-size: 0.9rem;
                flex-shrink: 0;
            }

            .brand img {
                height: 20px;
            }

            .btn {
                padding: 6px 12px;
                font-size: 0.75rem;
            }

            .topbar-actions {
                gap: 6px;
            }

            .hamburger {
                width: 28px;
                height: 28px;
            }

            .hamburger span {
                width: 18px;
                height: 1.5px;
            }

            .about-hero h1 {
                font-size: 1.5rem;
            }

            .about-hero p {
                font-size: 0.95rem;
            }

            .about-card {
                padding: 16px;
                margin-bottom: 20px;
            }

            .about-card h2 {
                font-size: 1.1rem;
            }

            .about-card p {
                font-size: 0.9rem;
            }

            .feature-list li {
                padding-left: 24px;
                font-size: 0.85rem;
            }

            .feature-list li:before {
                font-size: 1.1rem;
            }

            .security-badge,
            .midtrans-badge {
                padding: 14px;
                gap: 10px;
                font-size: 0.85rem;
            }

            .security-badge i,
            .midtrans-badge i {
                font-size: 1.2rem;
            }

            .midtrans-logo img {
                height: 24px;
            }

            .cta-section {
                padding: 40px 0;
            }

            .cta-button {
                padding: 12px 28px;
                font-size: 0.9rem;
            }
        }

        .cta-section {
            text-align: center;
            padding: 60px 0;
        }

        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 16px 36px;
            background: linear-gradient(145deg, var(--primary), var(--primary-dark));
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 10px 22px rgba(0, 151, 255, 0.28);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 151, 255, 0.35);
            color: white;
            text-decoration: none;
        }

        .midtrans-badge {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            border: 1px solid #a5b4fc;
            border-radius: 14px;
            margin: 12px 0;
        }

        .midtrans-badge i {
            font-size: 1.5rem;
            color: #4f46e5;
            flex-shrink: 0;
        }

        .midtrans-badge strong {
            color: #312e81;
        }

        .midtrans-logo {
            text-align: center;
            margin: 20px 0;
        }

        .midtrans-logo img {
            height: 32px;
            object-fit: contain;
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="container">
            <div class="topbar-inner">
                <a href="/" class="brand">
                    <img src="{{ asset('logos/tasku_transparan_dengan_nama.png') }}" alt="Tasku"
                        style="height: 32px; width: auto; object-fit: contain;">
                </a>
                <button class="hamburger" id="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <nav class="nav-links" id="navLinks">
                    <a href="/">Beranda</a>
                    <a href="/#fitur">Fitur</a>
                    <a href="/#pricing">Harga</a>
                    <a href="{{ route('about') }}" class="active">Tentang</a>
                </nav>
                <div class="topbar-actions">
                    <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar Gratis</a>
                </div>
            </div>
        </div>
    </header>

    <main>
        <section class="about-section">
            <div class="container">
                <div class="about-hero">
                    <h1>Tentang Tasku</h1>
                    <p>Platform kolaborasi yang aman dan terpercaya untuk manajemen grup dengan notifikasi terintegrasi
                    </p>
                </div>

                <!-- About Tasku -->
                <div class="about-card">
                    <h2>
                        <i class="fas fa-star"></i> Apa itu Tasku?
                    </h2>
                    <p>
                        Tasku adalah platform inovatif yang dirancang untuk memudahkan manajemen grup dan kolaborasi
                        tim.
                        Dengan integrasi bot notifikasi multi-channel, kami membantu Anda tetap terhubung dengan anggota
                        tim melalui Discord, Telegram, dan WhatsApp.
                    </p>
                    <p>
                        Kami percaya bahwa komunikasi yang efektif adalah kunci kesuksesan setiap organisasi. Oleh
                        karena
                        itu, Tasku menyediakan solusi terpadu yang memudahkan Anda untuk mengelola grup, membuat
                        pengumuman,
                        mengatur polling, dan berbagi informasi dengan mudah.
                    </p>
                </div>

                <!-- Features -->
                <div class="about-card">
                    <h2>
                        <i class="fas fa-cube"></i> Fitur Unggulan
                    </h2>
                    <ul class="feature-list">
                        <li><strong>Manajemen Grup Multi-Channel:</strong> Kelola grup Anda dengan integrasi Discord,
                            Telegram, dan WhatsApp</li>
                        <li><strong>Notifikasi Real-Time:</strong> Kirim pengumuman dan notifikasi instan ke semua
                            anggota
                            grup</li>
                        <li><strong>Kategori Pengumuman:</strong> Organisir pengumuman dengan kategori untuk kemudahan
                            pencarian</li>
                        <li><strong>Manajemen Anggota:</strong> Kelola anggota grup dengan role dan permission yang
                            fleksibel</li>
                        <li><strong>Riwayat Aktivitas:</strong> Pantau semua aktivitas grup dalam satu dashboard terpadu
                        </li>
                    </ul>
                </div>

                <!-- Payment Security -->
                <div class="about-card">
                    <h2>
                        <i class="fas fa-lock"></i> Keamanan Pembayaran
                    </h2>
                    <div class="midtrans-logo">
                        <img src="https://iconape.com/wp-content/files/yh/207674/png/midtrans-logo.png"
                            alt="Midtrans" />
                    </div>
                    <p>
                        Pembayaran Anda diproses melalui <strong>Midtrans</strong>, payment gateway terpercaya yang
                        mendukung berbagai metode pembayaran lokal seperti transfer bank, kartu kredit, dan e-wallet.
                        Semua transaksi dilindungi dengan enkripsi tingkat industri untuk keamanan maksimal.
                    </p>
                </div>

                <!-- Pricing & Subscription -->
                <div class="about-card">
                    <h2>
                        <i class="fas fa-receipt"></i> Paket & Harga
                    </h2>
                    <p>
                        Tasku menawarkan paket berlangganan yang fleksibel dan terjangkau. Anda dapat memilih durasi
                        langganan yang sesuai dengan kebutuhan, mulai dari 1 hingga 24 bulan. Setiap paket mencakup
                        akses
                        penuh ke semua fitur dan integrasi bot.
                    </p>
                    <p>
                        Harga yang Anda lihat di halaman pembayaran adalah harga final tanpa pajak tambahan, sehingga
                        Anda
                        dapat merencanakan budget dengan lebih jelas dan transparan.
                    </p>
                </div>

                <!-- Contact & Support -->
                <div class="about-card">
                    <h2>
                        <i class="fas fa-headset"></i> Dukungan & Bantuan
                    </h2>
                    <p>
                        Tim kami siap membantu Anda kapan saja. Jika Anda memiliki pertanyaan, masalah, atau saran,
                        jangan
                        ragu untuk menghubungi kami.
                    </p>
                    <p>
                        Kami berkomitmen untuk memberikan pengalaman terbaik bagi setiap pengguna Tasku dan terus
                        melakukan inovasi untuk meningkatkan layanan kami.
                    </p>
                </div>

                <div class="cta-section">
                    <p style="color: var(--slate-600); margin-bottom: 24px; font-size: 1.1rem;">
                        Siap untuk memulai kolaborasi yang lebih baik?
                    </p>
                    <a href="{{ route('home.pages') }}" class="cta-button">
                        <i class="fas fa-arrow-right"></i> Mulai Sekarang
                    </a>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const hamburger = document.getElementById('hamburger');
        const navLinks = document.getElementById('navLinks');

        hamburger.addEventListener('click', () => {
            hamburger.classList.toggle('active');
            navLinks.classList.toggle('active');
        });

        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            });
        });

        document.addEventListener('click', (e) => {
            if (!e.target.closest('.topbar-inner')) {
                hamburger.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    </script>
</body>

</html>
