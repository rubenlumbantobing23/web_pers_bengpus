@extends('layouts.app')

@section('title', 'Lupa Kata Sandi - Sistem Informasi Personalia')

@section('content')
<style>
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
    }

    .split-left {
        flex: 1 1 55%;
        padding: 80px 10%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--bg-dark);
        overflow: hidden;
    }

    .split-right {
        flex: 1 1 45%;
        padding: 60px 8%;
        background: rgba(10, 15, 28, 0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        border-left: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: -20px 0 50px rgba(0, 0, 0, 0.5);
    }

    .bg-logo-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 120%;
        height: 120%;
        background-image: url('{{ asset('images/logo.png') }}');
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        opacity: 0.05;
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
    .input-wrapper i {
        position: absolute; left: 20px; top: 50%; transform: translateY(-50%);
        color: var(--text-muted); transition: color 0.3s ease; font-size: 1.1rem;
    }
    .input-wrapper input:focus + i { color: var(--primary); }

    @media (max-width: 900px) {
        .split-left { padding: 60px 5%; min-height: 40vh; }
        .split-right { padding: 60px 5%; }
    }
</style>

<div class="split-container">
    <div class="split-left">
        <div class="bg-logo-watermark"></div>
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
        <div class="bg-grid"></div>

        <div style="position: relative; z-index: 10;" class="animate-fade-up">
            <h1 style="font-family: 'Outfit', sans-serif; font-size: clamp(2rem, 3vw, 3rem); font-weight: 800; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.01em; line-height: 1.1;">
                Lupa <br>
                <span style="background: linear-gradient(135deg, #f59e0b, #fbbf24); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Kata Sandi?</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.7; margin-bottom: 32px; max-width: 500px;">
                Jangan khawatir. Masukkan alamat email yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
            <a href="{{ route('login') }}" style="display: inline-flex; align-items: center; gap: 8px; color: var(--text-muted); font-weight: 600; text-decoration: none; padding: 12px 24px; border-radius: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;" onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='var(--text-muted)'">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <div class="split-right">
        <div style="max-width: 420px; width: 100%; margin: 0 auto;" class="animate-fade-up">
            <div style="margin-bottom: 40px;">
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Reset Kata Sandi</h2>
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

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 32px;">
                    <label class="form-label" for="email" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Alamat Email</label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" placeholder="nama@bengpuskomlekad.mil.id" value="{{ old('email') }}" required autofocus>
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>

                <button type="submit" class="btn-military" style="width: 100%; padding: 18px; border-radius: 14px; font-size: 1.05rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 15px 35px rgba(5, 150, 105, 0.4); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Tautan Reset
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 50px; color: rgba(255,255,255,0.2); font-size: 0.8rem; font-weight: 600; letter-spacing: 0.1em;">
                &copy; {{ date('Y') }} BENGPUSKOMLEKAD
            </div>
        </div>
    </div>
</div>
@endsection
