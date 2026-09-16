@extends('layouts.app')

@section('title', 'Registrasi Anggota - Bengpuskomlekad')

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
    }

    .split-left {
        flex: 1 1 50%;
        padding: 80px 8%;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: var(--bg-dark);
        overflow: hidden;
    }

    .split-right {
        flex: 1 1 50%;
        padding: 60px 8%;
        background: rgba(10, 15, 28, 0.95);
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        border-left: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: -20px 0 50px rgba(0, 0, 0, 0.5);
        overflow-y: auto;
        max-height: 100vh;
    }

    /* Animated Background in Left Side */
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

    /* Inputs for register */
    .form-control {
        width: 100%; padding: 14px 16px;
        background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px; font-size: 1rem; color: #fff; transition: all 0.3s ease;
        outline: none; box-sizing: border-box;
    }
    .form-control:focus {
        background: rgba(0, 0, 0, 0.5); border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(5,150,105,0.15);
    }

    /* Responsive */
    @media (max-width: 900px) {
        .split-left { padding: 60px 5%; min-height: 50vh; flex: 1 1 100%; }
        .split-right { padding: 60px 5%; flex: 1 1 100%; max-height: unset; overflow-y: visible; }
    }
</style>

<div class="split-container">
    
    <!-- LEFT SIDE: Information -->
    <div class="split-left">
        <!-- Logo Watermark Background -->
        <div class="bg-logo-watermark"></div>
        
        <div class="bg-glow-1"></div>
        <div class="bg-glow-2"></div>
        <div class="bg-grid"></div>

        <div style="position: relative; z-index: 10;" class="animate-fade-up">
            <h1 style="font-family: 'Outfit', sans-serif; font-size: clamp(2rem, 3.5vw, 3rem); font-weight: 800; color: #ffffff; margin-bottom: 12px; letter-spacing: 0.01em; line-height: 1.1;">
                Registrasi Akun <br>
                <span style="background: linear-gradient(135deg, #34d399, #10b981); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Anggota</span>
            </h1>
            <p style="color: var(--text-muted); font-size: 1.05rem; line-height: 1.7; margin-bottom: 32px; max-width: 450px;">
                Daftarkan akun Anda untuk mengakses portal <strong style="color: #fff;">BENGPUSKOMLEKAD TNI AD</strong>. Registrasi hanya berlaku untuk anggota yang sudah terdata dalam nominatif kesatuan.
            </p>
        </div>

        <!-- Features List -->
        <div style="position: relative; z-index: 10; max-width: 450px; margin-bottom: 20px;" class="animate-fade-up delay-1">
            <div class="feature-item">
                <div class="feature-icon"><i class="fa-solid fa-id-card-clip"></i></div>
                <div class="feature-text">Validasi Otomatis via NRP / NIP</div>
            </div>
            <div class="feature-item delay-2" style="animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0;">
                <div class="feature-icon" style="background: rgba(217, 119, 6, 0.2); color: #fbbf24; border-left-color: #fbbf24;"><i class="fa-solid fa-lock"></i></div>
                <div class="feature-text">Keamanan Akses Data Pribadi</div>
            </div>
            <div class="feature-item delay-3" style="animation: fadeUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0;">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.2); color: #60a5fa; border-left-color: #60a5fa;"><i class="fa-solid fa-file-signature"></i></div>
                <div class="feature-text">Koneksi Penuh ke Sistem Persuratan</div>
            </div>
        </div>
    </div>

    <!-- RIGHT SIDE: Register Form -->
    <div class="split-right">
        <div style="max-width: 580px; width: 100%; margin: 0 auto; padding-top: 40px; padding-bottom: 40px;" class="animate-fade-up delay-2">
            
            <div style="margin-bottom: 32px;">
                <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; color: #ffffff; margin-bottom: 8px;">Buat Akun Anda</h2>
                <p style="color: var(--text-muted); font-size: 0.95rem;">Lengkapi data di bawah ini untuk mengaktifkan akun sistem.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-error animate-fade-in" style="padding: 16px; border-radius: 12px; margin-bottom: 24px; font-size: 0.9rem; background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.2);">
                    <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.2rem; color: #ef4444; margin-bottom: 10px; display: block;"></i>
                    <div style="color: #fca5a5;">
                        <strong style="color: #fff;">Gagal Melanjutkan Registrasi:</strong>
                        <ul style="margin-top: 8px; padding-left: 20px; color: #fecaca; line-height: 1.5;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <!-- Langkah 1: Pengecekan NRP/NIP -->
                <div class="form-group" style="margin-bottom: 24px;">
                    <label class="form-label" for="nrp_nip" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">NRP / NIP Terdaftar *</label>
                    <div style="display: flex; gap: 12px;">
                        <input type="text" id="nrp_nip" name="nrp_nip" class="form-control" placeholder="Masukkan NRP / NIP Anda..." value="{{ old('nrp_nip') }}" required style="font-weight: 700; letter-spacing: 1px;">
                        <button type="button" class="btn-secondary" id="btnCheckNrp" onclick="checkNrpNominatif()" style="white-space: nowrap; padding: 0 20px; border-radius: 12px; border-color: rgba(96, 165, 250, 0.4); color: #60a5fa; font-weight: 700; background: rgba(59, 130, 246, 0.1);">
                            <i class="fa-solid fa-magnifying-glass"></i> CEK
                        </button>
                    </div>
                    <small style="color: rgba(255,255,255,0.4); display: block; margin-top: 8px; font-size: 0.8rem;">
                        * Pastikan NRP/NIP sudah terdata di staf personalia.
                    </small>
                </div>

                <!-- Alert Message Container AJAX -->
                <div id="nrpAlertContainer" style="display: none; margin-bottom: 24px; border-radius: 12px; padding: 14px; font-size: 0.9rem;"></div>

                <!-- Data Personel Read-Only Display (Tampil Setelah NRP Cocok) -->
                <div id="personelDetailBox" style="display: none; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 16px; padding: 24px; margin-bottom: 32px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; border-bottom: 1px dashed rgba(255, 255, 255, 0.1); padding-bottom: 12px;">
                        <span style="font-size: 0.85rem; font-weight: 800; color: #34d399; text-transform: uppercase; letter-spacing: 0.05em;">
                            <i class="fa-solid fa-circle-check"></i> Personel Terverifikasi
                        </span>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399; font-size: 0.7rem; border: 1px solid rgba(16, 185, 129, 0.3);">READ ONLY</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; font-size: 0.9rem;">
                        <div style="grid-column: span 2;">
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 4px;">Nama Lengkap:</span>
                            <span style="color: #fff; font-weight: 800; font-size: 1.1rem; letter-spacing: 0.5px;" id="info-nama">-</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 4px;">NRP / NIP:</span>
                            <span style="color: var(--accent-gold); font-weight: 700;" id="info-nrp">-</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 4px;">Pangkat / Golongan:</span>
                            <span style="color: #fff; font-weight: 600;" id="info-pangkat">-</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 4px;">Jabatan:</span>
                            <span style="color: #fff; font-weight: 600;" id="info-jabatan">-</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted); font-size: 0.8rem; display: block; margin-bottom: 4px;">Satuan / Bagian:</span>
                            <span style="color: #fff; font-weight: 600;" id="info-satuan">-</span>
                        </div>
                    </div>
                </div>

                <!-- Input Kredensial Akun (Email & Password) -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label" for="email" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Alamat Email (Untuk Login) *</label>
                        <input type="email" id="email" name="email" class="form-control" placeholder="nama@bengpuskomlekad.mil.id" value="{{ old('email') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Password *</label>
                        <div style="position: relative;">
                            <input type="password" id="password" name="password" class="form-control" placeholder="Min. 6 karakter" required style="padding-right: 40px;">
                            <i class="fa-solid fa-eye" id="togglePasswordReg" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = document.getElementById('password'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('fa-eye-slash');"></i>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Konfirmasi *</label>
                        <div style="position: relative;">
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi password" required style="padding-right: 40px;">
                            <i class="fa-solid fa-eye" id="togglePasswordConf" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: var(--text-muted);" onclick="const input = document.getElementById('password_confirmation'); input.type = input.type === 'password' ? 'text' : 'password'; this.classList.toggle('fa-eye-slash');"></i>
                        </div>
                    </div>
                </div>

                <!-- Input Personel Tambahan (No HP & Bagian) -->
                <div id="additionalFieldsBox" style="display: none; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px; padding-top: 24px; border-top: 1px dashed rgba(255, 255, 255, 0.1);">
                    <div class="form-group">
                        <label class="form-label" for="no_hp" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Nomor Handphone *</label>
                        <input type="text" id="no_hp" name="no_hp" class="form-control" placeholder="0812xxxxxx" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="organization_unit_id" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-sub); font-weight: 700; margin-bottom: 10px; display: block;">Atasan Langsung *</label>
                        <select id="organization_unit_id" name="organization_unit_id" class="form-control" required style="appearance: none; padding-right: 30px;">
                            <option value="">-- Pilih --</option>
                            @foreach($officials as $official)
                                <option value="{{ $official->organization_unit_id }}">{{ $official->roleLabel }} - {{ $official->personel ? $official->personel->nama : 'Belum Ada Pejabat' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-military" id="btnSubmitRegister" style="width: 100%; padding: 18px; margin-top: 32px; border-radius: 14px; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; box-shadow: 0 15px 35px rgba(5, 150, 105, 0.4); border: 1px solid rgba(255,255,255,0.2);">
                    <i class="fa-solid fa-user-check"></i> BUAT AKUN SEKARANG
                </button>
            </form>

            <div style="margin-top: 40px; padding-top: 24px; border-top: 1px dashed rgba(255,255,255,0.1); text-align: center;">
                <p style="font-size: 0.95rem; color: var(--text-muted);">
                    Sudah memiliki akun? <br>
                    <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 800; display: inline-block; margin-top: 12px; transition: color 0.2s ease; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.9rem;" onmouseover="this.style.color='#34d399'" onmouseout="this.style.color='var(--primary)'">&larr; KEMBALI KE LOGIN</a>
                </p>
            </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkNrpNominatif() {
        const nrpNip = document.getElementById('nrp_nip').value.trim();
        const alertBox = document.getElementById('nrpAlertContainer');
        const detailBox = document.getElementById('personelDetailBox');
        const btnCheck = document.getElementById('btnCheckNrp');

        if (!nrpNip) {
            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
            alertBox.style.border = '1px solid rgba(239, 68, 68, 0.2)';
            alertBox.style.color = '#fca5a5';
            alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color: #ef4444; margin-right: 8px;"></i> Silakan masukkan NRP atau NIP terlebih dahulu.';
            detailBox.style.display = 'none';
            return;
        }

        btnCheck.disabled = true;
        btnCheck.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> MEMERIKSA';

        fetch('{{ route("register.check_nrp") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nrp_nip: nrpNip })
        })
        .then(res => res.json())
        .then(data => {
            btnCheck.disabled = false;
            btnCheck.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> CEK';

            if (data.found) {
                alertBox.style.display = 'none';
                detailBox.style.display = 'block';
                document.getElementById('additionalFieldsBox').style.display = 'grid';

                document.getElementById('info-nama').innerText = data.personel.nama;
                document.getElementById('info-nrp').innerText = data.personel.nrp_nip;
                document.getElementById('info-pangkat').innerText = data.personel.pangkat_golongan;
                document.getElementById('info-jabatan').innerText = data.personel.jabatan;
                document.getElementById('info-satuan').innerText = data.personel.satuan_bagian;

                // Populate old data if exists
                if (data.personel.no_hp) {
                    document.getElementById('no_hp').value = data.personel.no_hp;
                }
                if (data.personel.organization_unit_id) {
                    document.getElementById('organization_unit_id').value = data.personel.organization_unit_id;
                }

            } else {
                detailBox.style.display = 'none';
                document.getElementById('additionalFieldsBox').style.display = 'none';
                alertBox.style.display = 'block';
                alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
                alertBox.style.border = '1px solid rgba(239, 68, 68, 0.2)';
                alertBox.style.color = '#fca5a5';
                alertBox.innerHTML = `<i class="fa-solid fa-triangle-exclamation" style="color: #ef4444; margin-right: 8px;"></i> ${data.message}`;
            }
        })
        .catch(err => {
            btnCheck.disabled = false;
            btnCheck.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> CEK';
            alertBox.style.display = 'block';
            alertBox.style.background = 'rgba(239, 68, 68, 0.1)';
            alertBox.style.border = '1px solid rgba(239, 68, 68, 0.2)';
            alertBox.style.color = '#fca5a5';
            alertBox.innerHTML = '<i class="fa-solid fa-triangle-exclamation" style="color: #ef4444; margin-right: 8px;"></i> Terjadi kesalahan saat memeriksa data. Silakan coba lagi.';
        });
    }

    // Auto check if input loses focus or pressed Enter
    document.getElementById('nrp_nip').addEventListener('blur', function() {
        if (this.value.trim().length >= 4) {
            checkNrpNominatif();
        }
    });

    document.getElementById('nrp_nip').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            checkNrpNominatif();
        }
    });
</script>
@endpush
@endsection
