@extends('layouts.app')

@section('title', 'Sistem Informasi Personalia - Bengpuskomlekad')

@section('content')
<style>
    /* Full Screen Layout Reset */
    body, html {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }
    
    .split-container {
        display: flex;
        min-height: 100vh;
        width: 100%;
        flex-wrap: wrap;
        background: var(--bg-dark);
        position: relative;
        overflow: hidden;
    }

    .split-left {
        flex: 1 1 55%;
        padding: 80px 10%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: transparent;
        z-index: 10;
    }

    .split-right {
        flex: 1 1 45%;
        padding: 60px 8%;
        background: transparent;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        z-index: 10;
    }

    /* Animated Background in Container */
    .bg-logo-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        height: 80%;
        background-image: url('{{ asset('images/logo.png') }}');
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.04;
        z-index: 1;
        pointer-events: none;
    }

    .bg-glow-1 {
        position: absolute; top: -10%; left: -10%; width: 50vw; height: 50vw;
        background: radial-gradient(circle, rgba(5, 150, 105, 0.15) 0%, transparent 60%);
        border-radius: 50%; filter: blur(70px);
        animation: float 10s infinite alternate ease-in-out;
        z-index: 0;
    }
    .bg-glow-2 {
        position: absolute; bottom: -10%; right: -10%; width: 40vw; height: 40vw;
        background: radial-gradient(circle, rgba(217, 119, 6, 0.12) 0%, transparent 60%);
        border-radius: 50%; filter: blur(70px);
        animation: float 12s infinite alternate-reverse ease-in-out;
        z-index: 0;
    }
    .bg-grid {
        position: absolute; inset: 0;
        background-image: linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        background-size: 40px 40px; opacity: 0.8;
        z-index: 0;
    }

    /* Animations */
    @keyframes float {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 30px); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .animate-fade-up { animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; }
    .animate-fade-in { animation: fadeIn 1s ease-out forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    .delay-4 { animation-delay: 0.8s; }

    /* Interactive Elements */
    .stat-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: ''; position: absolute; top: 0; left: -100%; width: 50%; height: 100%;
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.05), transparent);
        transform: skewX(-20deg); transition: 0.5s;
    }
    .stat-card:hover::before { left: 150%; }
    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.04);
        border-color: rgba(255, 255, 255, 0.15);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
    }
    
    .feature-item {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
        padding: 16px;
        background: rgba(0, 0, 0, 0.2);
        border-left: 3px solid var(--primary);
        border-radius: 0 12px 12px 0;
        transition: all 0.3s ease;
    }
    .feature-item:hover {
        background: rgba(5, 150, 105, 0.1);
        transform: translateX(10px);
    }
    .feature-icon {
        width: 40px; height: 40px; border-radius: 10px; background: rgba(5, 150, 105, 0.2);
        display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.1rem;
    }
    .feature-text {
        font-size: 1rem; font-weight: 600; color: #fff;
    }

    .input-wrapper { position: relative; }
    .input-wrapper input {
        width: 100%; padding: 16px 16px 16px 52px;
        background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px; font-size: 1.05rem; color: #fff; transition: all 0.3s ease;
        outline: none; box-sizing: border-box;
    }
    .input-wrapper input:focus {
        background: rgba(0, 0, 0, 0.5); border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(5,150,105,0.15);
    }
    .input-wrapper i.fa-envelope, .input-wrapper i.fa-lock {
        position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); transition: color 0.3s ease; font-size: 1.1rem;
    }
    .input-wrapper input:focus + i { color: var(--primary); }

    /* Responsive */
    @media (max-width: 900px) {
        .split-left { padding: 60px 5%; min-height: 60vh; }
        .split-right { padding: 60px 5%; }
    }
</style>

<div class="split-container">
    
    <!-- Background Elements (Unified) -->
    <div class="bg-logo-watermark"></div>
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>
    <div class="bg-grid"></div>

    <!-- LEFT SIDE: Information & Stats -->
    <div class="split-left">

        <div style="position: relative; z-index: 10;" class="animate-fade-up">
            <h1 style="font-family: 'Outfit', sans-serif; font-size: clamp(2.5rem, 4vw, 3.5rem); font-weight: 800; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.01em; line-height: 1.1;">
                Sistem Informasi <br>
                <span style="background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Personalia</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.7; margin-bottom: 32px; max-width: 500px;">
                Portal digital terintegrasi untuk pengelolaan data anggota, pengajuan cuti, dan persuratan di lingkungan <strong style="color: #fff;">BENGPUSKOMLEKAD TNI AD</strong>.
            </p>
        </div>

        <!-- Features List to fill space -->
        <div style="position: relative; z-index: 10; max-width: 450px; margin-bottom: 40px;" class="animate-fade-up delay-1">
            <div class="feature-item">
                <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
                <div class="feature-text">Pengelolaan Data Nominatif Personel</div>
            </div>
            <div class="feature-item delay-2" style="animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0;">
                <div class="feature-icon" style="background: rgba(217, 119, 6, 0.2); color: #fbbf24; border-left-color: #fbbf24;"><i class="fa-solid fa-calendar-check"></i></div>
                <div class="feature-text">Otomatisasi Pengajuan Cuti & Nikah</div>
            </div>
            <div class="feature-item delay-3" style="animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0;">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border-left-color: #60a5fa;"><i class="fa-solid fa-file-signature"></i></div>
                <div class="feature-text">Pencetakan Surat Dinas Terintegrasi</div>
            </div>
        </div>

        <!-- Statistics Widgets -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 24px; position: relative; z-index: 10; max-width: 600px;" class="animate-fade-up delay-4">
            <div class="stat-card">
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(5, 150, 105, 0.15); display: flex; align-items: center; justify-content: center; color: #34d399; font-size: 1.2rem; border: 1px solid rgba(5, 150, 105, 0.3);">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-sub); font-weight: 700;">Total Personel Terdata</span>
                </div>
                <div style="font-size: 2.8rem; font-weight: 800; color: #ffffff; font-family: 'Outfit', sans-serif; line-height: 1;">{{ $totalPersonel }} <span style="font-size: 1rem; font-weight: 500; color: var(--text-muted); text-transform: none;">Anggota</span></div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Login Form -->
    <div class="split-right">
        <div style="max-width: 440px; width: 100%; margin: 0 auto; padding: 40px 32px; background: rgba(10, 15, 28, 0.7); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-radius: 24px; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);" class="animate-fade-up delay-2">
            <div style="margin-bottom: 32px; text-align: center;">
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Masuk ke Akun Anda</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Silakan isi email dan kata sandi untuk melanjutkan.</p>
            </div>

        @if(session('success'))
            <div class="alert alert-success animate-fade-in" style="padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-size: 0.95rem; background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.2);">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error animate-fade-in" style="padding: 14px 20px; border-radius: 14px; margin-bottom: 24px; font-size: 0.95rem; background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2);">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="email" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-sub); font-weight: 700; margin-bottom: 8px; display: block;">Alamat Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="nama@bengpuskomlekad.mil.id" value="{{ old('email') }}" required autofocus>
                    <i class="fa-solid fa-envelope"></i>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" for="password" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-sub); font-weight: 700; margin-bottom: 8px; display: block;">Kata Sandi</label>
                <div class="input-wrapper" style="position: relative;">
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <i class="fa-solid fa-lock" style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%);"></i>
                    <i class="fa-solid fa-eye" id="togglePassword" style="position: absolute; right: 20px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);"></i>
                </div>
            </div>

            <script>
                document.getElementById('togglePassword').addEventListener('click', function (e) {
                    const passwordInput = document.getElementById('password');
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                });
            </script>

            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px;">
                <label style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-sub); cursor: pointer; user-select: none;">
                    <input type="checkbox" name="remember" style="accent-color: var(--primary); width: 16px; height: 16px; cursor: pointer;">
                    Ingat Saya
                </label>
                <a href="{{ route('password.request') }}" style="color: var(--accent-gold); font-size: 0.85rem; text-decoration: none; transition: color 0.2s ease;" onmouseover="this.style.color='#fde047'" onmouseout="this.style.color='var(--accent-gold)'">Lupa Kata Sandi?</a>
            </div>

            <button type="submit" class="btn-military" style="width: 100%; padding: 16px; border-radius: 12px; font-size: 1.05rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 10px 25px rgba(5, 150, 105, 0.4); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                <i class="fa-solid fa-right-to-bracket"></i> MASUK
            </button>
        </form>

        <div style="margin-top: 32px; padding-top: 24px; border-top: 1px dashed rgba(255,255,255,0.1); text-align: center;">
            <p style="font-size: 0.9rem; color: var(--text-muted);">
                Belum memiliki akun Anggota? <br>
                <a href="{{ route('register') }}" style="color: var(--accent-gold); font-weight: 800; display: inline-block; margin-top: 8px; transition: color 0.2s ease; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.85rem;" onmouseover="this.style.color='#fde047'" onmouseout="this.style.color='var(--accent-gold)'">Registrasi Akun Baru &rarr;</a>
            </p>
        </div>
    </div>

</div>
@endsection
