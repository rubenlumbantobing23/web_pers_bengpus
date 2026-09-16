<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Informasi Personalia Bengpuskomlekad - Portal Pelayanan Administrasi Cuti, Izin Nikah, Nominatif Personel, dan Arsip Surat Intern Bengpuskomlekad TNI AD.">
    <title>Sistem Informasi Personalia (SIPERS) - Bengpus Puskomlekad</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            /* Diselaraskan dengan dashboard app.blade.php */
            --bg-dark: #0a0f1d;
            --bg-surface: #111827;
            --bg-card: #131b2e;
            --bg-card-hover: #1c2742;
            --border-color: rgba(255, 255, 255, 0.09);
            --border-gold: rgba(245, 158, 11, 0.4);
            --border-emerald: rgba(16, 185, 129, 0.35);
            
            --primary: #059669;
            --primary-light: #10b981;
            --primary-glow: rgba(16, 185, 129, 0.35);
            
            --accent-gold: #d97706;
            --accent-gold-light: #fbbf24;
            --accent-gold-glow: rgba(245, 158, 11, 0.3);
            
            --accent-blue: #3b82f6;
            
            --text-main: #f8fafc;
            --text-sub: #cbd5e1;
            --text-muted: #94a3b8;
            
            --glass-bg: rgba(19, 27, 46, 0.92);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.01em;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Glassmorphism & Card utility */
        .glass-box {
            background: linear-gradient(135deg, rgba(19, 27, 46, 0.95) 0%, rgba(11, 17, 32, 0.95) 100%);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        /* Container constraint - FULL WIDTH */
        .container {
            width: 100%;
            max-width: 100%;
            margin: 0 auto;
            padding: 0 32px;
        }

        @media (max-width: 768px) {
            .container { padding: 0 16px; }
        }

        /* Navbar */
        header.navbar-sticky {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(10, 15, 29, 0.97);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-gold);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
            transition: all 0.3s ease;
        }

        .nav-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 72px;
        }

        .brand-logo-group {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-shrink: 0;
        }

        .brand-emblem-wrapper {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.5));
        }

        .brand-emblem-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title-text h2 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.1;
            letter-spacing: 0.03em;
        }

        .brand-title-text span {
            font-size: 0.7rem;
            color: var(--accent-gold-light);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.09em;
        }

        .nav-menu-links {
            display: flex;
            align-items: center;
            gap: 24px;
            list-style: none;
        }

        .nav-menu-links a {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-sub);
            transition: all 0.2s ease;
        }

        .nav-menu-links a:hover {
            color: var(--accent-gold-light);
        }

        .nav-auth-buttons {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-nav-outline {
            padding: 9px 18px;
            border: 1px solid var(--border-gold);
            border-radius: 8px;
            color: var(--accent-gold-light);
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            background: rgba(245, 158, 11, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-outline:hover {
            background: rgba(245, 158, 11, 0.25);
            border-color: var(--accent-gold-light);
            color: #ffffff;
        }

        .btn-nav-primary {
            padding: 9px 18px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: 1px solid var(--accent-gold);
            border-radius: 8px;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--primary-glow);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        /* Hamburger Button */
        .hamburger-btn {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            padding: 8px;
        }

        .hamburger-btn span {
            display: block;
            width: 20px;
            height: 2px;
            background: var(--text-sub);
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Mobile Nav Menu */
        .mobile-nav-menu {
            display: none;
            position: fixed;
            top: 72px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(10, 15, 29, 0.98);
            backdrop-filter: blur(20px);
            z-index: 999;
            padding: 24px 24px;
            flex-direction: column;
            gap: 8px;
            overflow-y: auto;
            border-top: 1px solid var(--border-gold);
        }

        .mobile-nav-menu.open {
            display: flex;
        }

        .mobile-nav-link {
            display: flex;
            align-items: center;
            padding: 14px 18px;
            border-radius: 10px;
            color: var(--text-sub);
            font-size: 1rem;
            font-weight: 600;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .mobile-nav-link:hover {
            background: rgba(255,255,255,0.05);
            border-color: var(--border-color);
            color: var(--accent-gold-light);
        }

        /* Hero Section */
        .hero-section {
            padding: 80px 0 70px 0;
            position: relative;
            overflow: hidden;
            background: radial-gradient(ellipse at top center, rgba(16, 185, 129, 0.12) 0%, rgba(10, 15, 29, 1) 70%);
        }

        .hero-bg-glow {
            position: absolute;
            top: -120px;
            right: 5%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-bg-glow-left {
            position: absolute;
            bottom: -150px;
            left: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-content-full {
            width: 100%;
            max-width: 100%;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 8px 20px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid var(--border-gold);
            border-radius: 30px;
            color: var(--accent-gold-light);
            font-size: 0.82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 24px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }

        .hero-heading {
            font-size: clamp(1.9rem, 4.5vw, 3.6rem);
            font-weight: 900;
            line-height: 1.15;
            color: #ffffff;
            margin-bottom: 22px;
            letter-spacing: -0.02em;
        }

        .hero-heading span.text-emerald {
            background: linear-gradient(135deg, #34d399 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-heading span.text-gold {
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-lead-text {
            font-size: 1.05rem;
            color: var(--text-sub);
            line-height: 1.8;
            margin-bottom: 36px;
            max-width: 900px;
        }

        .hero-cta-group {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 48px;
        }

        .btn-cta-large {
            padding: 15px 32px;
            font-size: 0.975rem;
            border-radius: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            transition: all 0.25s ease;
            letter-spacing: 0.02em;
        }

        .btn-cta-emerald {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            box-shadow: 0 6px 25px var(--primary-glow);
            border: 1px solid var(--accent-gold);
        }

        .btn-cta-emerald:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 35px var(--primary-glow);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-cta-gold {
            background: rgba(245, 158, 11, 0.13);
            color: var(--accent-gold-light);
            border: 1px solid var(--accent-gold);
        }

        .btn-cta-gold:hover {
            transform: translateY(-2px);
            background: rgba(245, 158, 11, 0.25);
            border-color: var(--accent-gold-light);
            color: #ffffff;
        }

        /* Hero Feature Pillars */
        .hero-pillars-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-top: 10px;
        }

        .hero-pillar-card {
            padding: 18px 22px;
            background: linear-gradient(135deg, rgba(19, 27, 46, 0.9) 0%, rgba(11, 17, 32, 0.9) 100%);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--accent-gold);
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.3);
        }

        .hero-pillar-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: rgba(245, 158, 11, 0.14);
            border: 1px solid var(--border-gold);
            color: var(--accent-gold-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .hero-pillar-title {
            font-weight: 800;
            color: #ffffff;
            font-size: 0.95rem;
            line-height: 1.2;
        }

        .hero-pillar-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Stats Bar */
        .stats-strip {
            padding: 40px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            background: rgba(17, 24, 39, 0.85);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .stat-card {
            padding: 24px;
            text-align: center;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background: rgba(19, 27, 46, 0.6);
            border-top: 3px solid var(--primary-light);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }

        .stat-number-value {
            font-size: 2.2rem;
            font-weight: 900;
            color: #ffffff;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-label-text {
            font-size: 0.82rem;
            color: var(--text-muted);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* Section Headings */
        .section-header-box {
            text-align: center;
            max-width: 760px;
            margin: 0 auto 50px auto;
        }

        .section-subtitle-tag {
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--accent-gold-light);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 10px;
            display: block;
        }

        .section-main-title {
            font-size: 2.1rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 14px;
            letter-spacing: -0.01em;
        }

        .section-desc-text {
            color: var(--text-muted);
            font-size: 0.975rem;
            line-height: 1.6;
        }

        /* Visi Misi Section */
        .visimisi-section {
            padding: 90px 0;
            position: relative;
        }

        .visimisi-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        .visi-card {
            padding: 36px;
            border-left: 5px solid var(--primary-light);
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.13) 0%, var(--glass-bg) 100%);
            border-radius: 14px;
        }

        .misi-card {
            padding: 36px;
            border-left: 5px solid var(--accent-gold-light);
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, var(--glass-bg) 100%);
            border-radius: 14px;
        }

        .card-icon-header {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
        }

        .misi-list-items {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 14px;
            margin-top: 18px;
        }

        .misi-list-items li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.925rem;
            color: var(--text-sub);
        }

        .misi-list-items li i {
            color: var(--accent-gold-light);
            margin-top: 4px;
            font-size: 0.85rem;
        }

        /* Layanan Utama Section */
        .layanan-section {
            padding: 90px 0;
            background: rgba(17, 24, 39, 0.6);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }

        .layanan-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .layanan-card {
            padding: 30px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            border-top: 2px solid transparent;
        }

        .layanan-card:hover {
            transform: translateY(-5px);
            border-color: var(--border-gold);
            border-top-color: var(--accent-gold-light);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.6);
        }

        .layanan-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        /* Alur Pelayanan Section */
        .alur-section {
            padding: 90px 0;
        }

        .step-flow-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            position: relative;
        }

        .step-flow-card {
            padding: 28px 22px;
            text-align: center;
            position: relative;
            border-radius: 14px;
        }

        .step-badge-number {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border: 2px solid var(--accent-gold-light);
            color: #ffffff;
            font-weight: 800;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
        }

        /* Footer */
        footer.footer-main {
            background: #060b16;
            border-top: 1px solid var(--border-gold);
            padding: 70px 0 30px 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 36px;
            margin-bottom: 48px;
        }

        .footer-col-title {
            font-size: 1rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 18px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .footer-links-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .footer-links-list a {
            color: var(--text-muted);
            font-size: 0.875rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-links-list a:hover {
            color: var(--accent-gold-light);
            padding-left: 4px;
        }

        .footer-bottom-bar {
            padding-top: 28px;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.85rem;
            color: var(--text-muted);
            flex-wrap: wrap;
            gap: 16px;
        }

        /* =============================
           RESPONSIVE BREAKPOINTS
           ============================= */
        @media (max-width: 1280px) {
            .hero-pillars-grid { grid-template-columns: 1fr 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
        }

        @media (max-width: 1024px) {
            .hero-pillars-grid { grid-template-columns: 1fr 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .visimisi-grid { grid-template-columns: 1fr; }
            .step-flow-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .nav-menu-links { gap: 16px; }
        }

        @media (max-width: 768px) {
            .nav-menu-links { display: none; }
            .nav-auth-buttons { display: none; }
            .hamburger-btn { display: flex; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .step-flow-grid { grid-template-columns: 1fr 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .hero-pillars-grid { grid-template-columns: 1fr; }
            .hero-section { padding: 50px 0 40px 0; }
            .visimisi-grid { grid-template-columns: 1fr; }
            .layanan-cards-grid { grid-template-columns: 1fr; }
            .hero-lead-text { font-size: 0.95rem; }
            .section-main-title { font-size: 1.7rem; }
            .visi-card, .misi-card { padding: 24px; }
        }

        @media (max-width: 480px) {
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .step-flow-grid { grid-template-columns: 1fr; }
            .hero-cta-group { flex-direction: column; align-items: stretch; }
            .btn-cta-large { justify-content: center; }
            .hero-tag { font-size: 0.72rem; padding: 6px 14px; }
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <header class="navbar-sticky">
        <div class="container">
            <div class="nav-wrapper">
                <a href="{{ route('home') }}" class="brand-logo-group">
                    <div class="brand-emblem-wrapper">
                        <img src="{{ asset('images/logo.png') }}" alt="Bengpuskomlekad Logo">
                    </div>
                    <div class="brand-title-text">
                        <h2>BENGPUSKOMLEKAD TNI AD</h2>
                        <span>Sistem Informasi Personalia</span>
                    </div>
                </a>

                <ul class="nav-menu-links">
                    <li><a href="#beranda">Beranda</a></li>
                    <li><a href="#visimisi">Visi & Misi</a></li>
                    <li><a href="#layanan">Layanan Utama</a></li>
                    <li><a href="#alur">Alur Pengajuan</a></li>
                    <li><a href="#kontak">Kontak</a></li>
                </ul>

                <div class="nav-auth-buttons">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-nav-primary">
                                <i class="fa-solid fa-user-gear"></i> Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="btn-nav-primary">
                                <i class="fa-solid fa-house-user"></i> Dashboard Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="btn-nav-outline">
                            <i class="fa-solid fa-user-plus"></i> Registrasi
                        </a>
                        <a href="{{ route('login') }}" class="btn-nav-primary">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk Sistem
                        </a>
                    @endauth
                </div>

                <!-- Hamburger Button (Mobile) -->
                <button class="hamburger-btn" id="hamburger-btn" onclick="toggleMobileMenu()" aria-label="Toggle menu">
                    <span id="ham-line1"></span>
                    <span id="ham-line2"></span>
                    <span id="ham-line3"></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation Menu -->
    <div class="mobile-nav-menu" id="mobile-nav-menu">
        <a href="#beranda" class="mobile-nav-link" onclick="closeMobileMenu()"><i class="fa-solid fa-house" style="color: var(--accent-gold-light); width: 20px;"></i> Beranda</a>
        <a href="#visimisi" class="mobile-nav-link" onclick="closeMobileMenu()"><i class="fa-solid fa-eye" style="color: var(--accent-gold-light); width: 20px;"></i> Visi &amp; Misi</a>
        <a href="#layanan" class="mobile-nav-link" onclick="closeMobileMenu()"><i class="fa-solid fa-shield-halved" style="color: var(--accent-gold-light); width: 20px;"></i> Layanan Utama</a>
        <a href="#alur" class="mobile-nav-link" onclick="closeMobileMenu()"><i class="fa-solid fa-list-check" style="color: var(--accent-gold-light); width: 20px;"></i> Alur Pengajuan</a>
        <a href="#kontak" class="mobile-nav-link" onclick="closeMobileMenu()"><i class="fa-solid fa-phone" style="color: var(--accent-gold-light); width: 20px;"></i> Kontak</a>

        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color); display: flex; flex-direction: column; gap: 10px;">
            @auth
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn-nav-primary" style="justify-content: center; padding: 14px;">
                        <i class="fa-solid fa-user-gear"></i> Dashboard Admin
                    </a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="btn-nav-primary" style="justify-content: center; padding: 14px;">
                        <i class="fa-solid fa-house-user"></i> Dashboard Saya
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}" class="btn-nav-outline" style="justify-content: center; padding: 14px;">
                    <i class="fa-solid fa-user-plus"></i> Registrasi Anggota Baru
                </a>
                <a href="{{ route('login') }}" class="btn-nav-primary" style="justify-content: center; padding: 14px;">
                    <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Sistem
                </a>
            @endauth
        </div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section" id="beranda">
        <div class="hero-bg-glow"></div>
        <div class="hero-bg-glow-left"></div>

        <div class="container">
            <div class="hero-content-full">
                <div class="hero-tag">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 24px; height: 24px; object-fit: contain;">
                    <span>Bengkel Pusat Komunikasi dan Elektronika TNI Angkatan Darat</span>
                </div>

                <h1 class="hero-heading">
                    Sistem Informasi <span class="text-emerald">Personalia</span> <span class="text-gold">Bengpus Puskomlekad</span>
                </h1>

                <p class="hero-lead-text">
                    Portal pelayanan administrasi personalia resmi Bengpuskomlekad TNI AD. Menyelenggarakan pengajuan cuti dinas, permohonan izin nikah anggota, pemantauan data nominatif personel, serta pengarsipan surat intern secara teratur, transparan, dan akuntabel guna mendukung tugas pokok TNI Angkatan Darat.
                </p>

                <div class="hero-cta-group">
                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn-cta-large btn-cta-emerald">
                                <i class="fa-solid fa-user-gear"></i> Buka Dashboard Admin
                            </a>
                        @else
                            <a href="{{ route('user.dashboard') }}" class="btn-cta-large btn-cta-emerald">
                                <i class="fa-solid fa-house-user"></i> Buka Dashboard Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn-cta-large btn-cta-emerald">
                            <i class="fa-solid fa-right-to-bracket"></i> Masuk ke Portal Login
                        </a>
                        <a href="{{ route('register') }}" class="btn-cta-large btn-cta-gold">
                            <i class="fa-solid fa-user-plus"></i> Registrasi Anggota Baru
                        </a>
                    @endauth
                </div>

                <!-- Hero Feature Pillars Horizontal Bar -->
                <div class="hero-pillars-grid">
                    <div class="hero-pillar-card">
                        <div class="hero-pillar-icon">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <div>
                            <div class="hero-pillar-title">Pelayanan Administrasi Resmi</div>
                            <div class="hero-pillar-desc">Verifikasi berjenjang Staf Personalia Bengpuskomlekad</div>
                        </div>
                    </div>

                    <div class="hero-pillar-card">
                        <div class="hero-pillar-icon">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>
                        <div>
                            <div class="hero-pillar-title">Kalkulasi Cuti Otomatis</div>
                            <div class="hero-pillar-desc">Penghitungan hari kerja 12 hari/tahun bebas hari libur</div>
                        </div>
                    </div>

                    <div class="hero-pillar-card">
                        <div class="hero-pillar-icon">
                            <i class="fa-solid fa-file-shield"></i>
                        </div>
                        <div>
                            <div class="hero-pillar-title">Arsip Digital Terintegrasi</div>
                            <div class="hero-pillar-desc">Pengarsipan surat intern & data nominatif personel</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Strip -->
    <div class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number-value" style="color: #34d399;">12 Hari</div>
                    <div class="stat-label-text">Jatah Cuti Tahunan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number-value" style="color: #fbbf24;">100%</div>
                    <div class="stat-label-text">Digital & Terintegrasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number-value" style="color: #60a5fa;">Realtime</div>
                    <div class="stat-label-text">Kalkulasi Hari Kerja</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number-value" style="color: #a78bfa;">Terarsip</div>
                    <div class="stat-label-text">Surat Intern & Permohonan</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visi & Misi Section -->
    <section class="visimisi-section" id="visimisi">
        <div class="container">
            <div class="section-header-box">
                <span class="section-subtitle-tag">Pedoman & Komitmen Pelayanan</span>
                <h2 class="section-main-title">Visi & Misi Staf Personalia</h2>
                <p class="section-desc-text">
                    Landasan kerja Staf Personalia Bengpuskomlekad dalam memberikan pelayanan administrasi yang profesional dan terbaik untuk seluruh prajurit dan PNS.
                </p>
            </div>

            <div class="visimisi-grid">
                <!-- Visi Card -->
                <div class="glass-box visi-card">
                    <div class="card-icon-header" style="background: rgba(5, 150, 105, 0.2); color: #34d399;">
                        <i class="fa-solid fa-eye"></i>
                    </div>
                    <h3 style="font-size: 1.4rem; color: #ffffff; margin-bottom: 12px; font-weight: 800;">VISI BENGPUSKOMLEKAD</h3>
                    <p style="color: var(--text-sub); font-size: 1rem; line-height: 1.8;">
                        "Mewujudkan tata kelola pembinaan personalia prajurit dan PNS Bengpuskomlekad yang profesional, berintegritas, modern, serta adaptif berbasis teknologi informasi guna mendukung tugas pokok TNI AD."
                    </p>
                </div>

                <!-- Misi Card -->
                <div class="glass-box misi-card">
                    <div class="card-icon-header" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">
                        <i class="fa-solid fa-bullseye"></i>
                    </div>
                    <h3 style="font-size: 1.4rem; color: #ffffff; margin-bottom: 12px; font-weight: 800;">MISI PERSONALIA</h3>
                    <ul class="misi-list-items">
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Menyelenggarakan administrasi permohonan cuti dan izin nikah secara tertib, cepat, transparan, dan akuntabel.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Memelihara keakuratan data nominatif personel prajurit dan PNS Bengpuskomlekad secara teratur dan berkesinambungan.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Mengembangkan pengarsipan dokumen surat intern personalia secara digital yang aman, rapi, dan mudah diakses.</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-circle-check"></i>
                            <span>Meningkatkan kualitas pelayanan dan kesejahteraan moral personel melalui kemudahan akses permohonan administrasi.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Utama Section -->
    <section class="layanan-section" id="layanan">
        <div class="container">
            <div class="section-header-box">
                <span class="section-subtitle-tag">Layanan Layanan Administrasi</span>
                <h2 class="section-main-title">Modul & Fitur Utama Portal PERS</h2>
                <p class="section-desc-text">
                    Seluruh fitur administrasi personalia terintegrasi dalam satu sistem terpusat untuk kemudahan prajurit dan Staf Personalia.
                </p>
            </div>

            <div class="layanan-cards-grid">
                <!-- Layanan 1 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(16, 185, 129, 0.18); color: #34d399;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Pengajuan Cuti Online</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Proses permohonan cuti tahunan 12 hari kerja dengan 4 langkah mudah, dilengkapi kalender interaktif dan verifikasi syarat & ketentuan.
                    </p>
                </div>

                <!-- Layanan 2 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(245, 158, 11, 0.18); color: #fbbf24;">
                        <i class="fa-solid fa-calculator"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Kalkulasi Hari Kerja Cerdas</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Penghitungan hari kerja secara otomatis melewatakan hari Sabtu, Minggu, dan hari libur nasional Indonesia tanpa mengurangi jatah secara tidak tepat.
                    </p>
                </div>

                <!-- Layanan 3 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(59, 130, 246, 0.18); color: #60a5fa;">
                        <i class="fa-solid fa-heart"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Permohonan Izin Nikah</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Pengajuan izin menikah anggota lengkap dengan berkas persyaratan N1-N4, SKBM, SKCK, foto PDH, serta verifikasi Staf Personalia.
                    </p>
                </div>

                <!-- Layanan 4 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(167, 139, 250, 0.18); color: #a78bfa;">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Data Nominatif Personel</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Pengelolaan master data personel prajurit dan PNS Bengpuskomlekad mencakup NRP/NIP, pangkat, jabatan, dan satuan kerja.
                    </p>
                </div>

                <!-- Layanan 5 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(251, 113, 133, 0.18); color: #fb7185;">
                        <i class="fa-solid fa-file-pdf"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Arsip Surat Intern</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Penyimpanan dan pencarian berkas digital surat perintah, surat izin cuti/nikah, serta nota dinas secara terstruktur dan aman.
                    </p>
                </div>

                <!-- Layanan 6 -->
                <div class="glass-box layanan-card">
                    <div class="layanan-icon-box" style="background: rgba(34, 197, 94, 0.18); color: #4ade80;">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <h3 style="font-size: 1.2rem; color: #ffffff; margin-bottom: 10px; font-weight: 700;">Monitoring & Verifikasi Admin</h3>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6;">
                        Dashboard pengawasan komprehensif bagi Admin Staf Personalia untuk menyetujui, menolak dengan alasan, atau mengevaluasi pengajuan.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Alur Pengajuan Section -->
    <section class="alur-section" id="alur">
        <div class="container">
            <div class="section-header-box">
                <span class="section-subtitle-tag">Prosedur Penggunaan</span>
                <h2 class="section-main-title">Alur Pengajuan Cuti & Izin Nikah</h2>
                <p class="section-desc-text">
                    Tahapan sistematis bagi anggota Bengpuskomlekad untuk melakukan permohonan administrasi personalia.
                </p>
            </div>

            <div class="step-flow-grid">
                <div class="glass-box step-flow-card">
                    <div class="step-badge-number">1</div>
                    <h4 style="color: #ffffff; font-size: 1.05rem; margin-bottom: 8px; font-weight: 700;">Registrasi & Login</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Daftarkan akun anggota menggunakan NRP/NIP dan email aktif lalu masuk ke sistem.</p>
                </div>

                <div class="glass-box step-flow-card">
                    <div class="step-badge-number">2</div>
                    <h4 style="color: #ffffff; font-size: 1.05rem; margin-bottom: 8px; font-weight: 700;">Pilih Jenis Pengajuan</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Pilih menu Cuti atau Nikah, lalu baca dan setujui syarat & ketentuan permohonan.</p>
                </div>

                <div class="glass-box step-flow-card">
                    <div class="step-badge-number">3</div>
                    <h4 style="color: #ffffff; font-size: 1.05rem; margin-bottom: 8px; font-weight: 700;">Isi Data & Tanggal</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Pilih tanggal pelaksanaan via kalender, isi keterangan, dan unggah berkas pendukung.</p>
                </div>

                <div class="glass-box step-flow-card">
                    <div class="step-badge-number">4</div>
                    <h4 style="color: #ffffff; font-size: 1.05rem; margin-bottom: 8px; font-weight: 700;">Verifikasi Staf Pers</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted);">Staf Personalia memeriksa permohonan. Status terupdate secara otomatis di dashboard.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="footer-main" id="kontak">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Instansi Info -->
                <div>
                    <div class="brand-logo-group" style="margin-bottom: 18px;">
                        <div class="brand-emblem-wrapper">
                            <img src="{{ asset('images/logo.png') }}" alt="Bengpuskomlekad Logo">
                        </div>
                        <div class="brand-title-text">
                            <h2>BENGPUSKOMLEKAD TNI AD</h2>
                            <span>Sistem Informasi Personalia</span>
                        </div>
                    </div>

                    <p style="color: var(--text-muted); font-size: 0.875rem; line-height: 1.7; max-width: 380px;">
                        Bengkel Pusat Komunikasi dan Elektronika TNI Angkatan Darat (Bengpuskomlekad). Menyelenggarakan pembinaan teknis materiil komunikasi, elektronika, dan pembinaan personalia.
                    </p>
                </div>

                <!-- Col 2: Navigasi Cepat -->
                <div>
                    <h4 class="footer-col-title">Navigasi Utama</h4>
                    <ul class="footer-links-list">
                        <li><a href="#beranda"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--accent-gold-light);"></i> Beranda</a></li>
                        <li><a href="#visimisi"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--accent-gold-light);"></i> Visi & Misi</a></li>
                        <li><a href="#layanan"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--accent-gold-light);"></i> Layanan Utama</a></li>
                        <li><a href="#alur"><i class="fa-solid fa-chevron-right" style="font-size: 0.7rem; color: var(--accent-gold-light);"></i> Alur Pengajuan</a></li>
                    </ul>
                </div>

                <!-- Col 3: Portal Login -->
                <div>
                    <h4 class="footer-col-title">Akses Portal</h4>
                    <ul class="footer-links-list">
                        <li><a href="{{ route('login') }}"><i class="fa-solid fa-lock" style="font-size: 0.75rem; color: var(--accent-gold-light);"></i> Login Anggota & Admin</a></li>
                        <li><a href="{{ route('register') }}"><i class="fa-solid fa-user-plus" style="font-size: 0.75rem; color: #34d399;"></i> Registrasi Anggota Baru</a></li>
                    </ul>
                </div>

                <!-- Col 4: Markas & Kontak -->
                <div>
                    <h4 class="footer-col-title">Markas Bengpuskomlekad</h4>
                    <div style="display: flex; flex-direction: column; gap: 12px; font-size: 0.875rem; color: var(--text-muted);">
                        <div style="display: flex; gap: 10px; align-items: flex-start;">
                            <i class="fa-solid fa-location-dot" style="color: var(--accent-gold-light); margin-top: 4px;"></i>
                            <span>Jl. Jakarta No. 60, Bandung, Jawa Barat 40272</span>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <i class="fa-solid fa-phone" style="color: #34d399;"></i>
                            <span>(022) 7208123 (Staf Pers)</span>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <i class="fa-solid fa-envelope" style="color: #60a5fa;"></i>
                            <span>pers@bengpuskomlekad.mil.id</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom-bar">
                <div>
                    &copy; {{ date('Y') }} <strong style="color: #ffffff;">Bengpuskomlekad TNI AD</strong> — Sistem Informasi Personalia. Hak Cipta Dilindungi.
                </div>
                <div>
                    <span style="color: var(--accent-gold-light); font-weight: 800; letter-spacing: 0.05em;">TNI AD — KARTIKA EKA PAKSI</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Hamburger Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-nav-menu');
            const btn = document.getElementById('hamburger-btn');
            const line1 = document.getElementById('ham-line1');
            const line2 = document.getElementById('ham-line2');
            const line3 = document.getElementById('ham-line3');
            
            menu.classList.toggle('open');
            document.body.style.overflow = menu.classList.contains('open') ? 'hidden' : '';
            
            if (menu.classList.contains('open')) {
                line1.style.transform = 'translateY(7px) rotate(45deg)';
                line2.style.opacity = '0';
                line3.style.transform = 'translateY(-7px) rotate(-45deg)';
            } else {
                line1.style.transform = '';
                line2.style.opacity = '';
                line3.style.transform = '';
            }
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-nav-menu');
            const line1 = document.getElementById('ham-line1');
            const line2 = document.getElementById('ham-line2');
            const line3 = document.getElementById('ham-line3');
            
            menu.classList.remove('open');
            document.body.style.overflow = '';
            line1.style.transform = '';
            line2.style.opacity = '';
            line3.style.transform = '';
        }

        // Close mobile menu when resizing to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeMobileMenu();
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header.navbar-sticky');
            if (window.scrollY > 50) {
                header.style.boxShadow = '0 4px 30px rgba(0,0,0,0.6)';
            } else {
                header.style.boxShadow = '0 4px 20px rgba(0,0,0,0.4)';
            }
        });
    </script>

</body>
</html>
