@extends('layouts.admin')

@section('page-title', 'Profil Admin')

@section('admin-content')
<div class="glass-card" style="max-width: 600px; margin: 0 auto; padding: 30px;">
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #d97706, #b45309); display: flex; align-items: center; justify-content: center; color: #fff; font-size: 2rem; font-weight: 700; box-shadow: 0 10px 20px rgba(217, 119, 6, 0.3);">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
        <div>
            <h2 style="font-size: 1.5rem; color: #fff; font-weight: 700; margin-bottom: 4px;">{{ $user->name }}</h2>
            <div style="display: inline-block; padding: 4px 10px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 6px; font-size: 0.75rem; font-weight: 600;">ADMIN PERSONALIA</div>
        </div>
    </div>

    <form action="{{ route('admin.profile.update') }}" method="POST">
        @csrf
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px;">Nama Lengkap</label>
            <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 12px; color: #fff;">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px;">Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 12px; color: #fff;">
        </div>

        <div style="margin-top: 30px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <h3 style="font-size: 1.1rem; color: #fff; font-weight: 600;"><i class="fa-solid fa-lock" style="color: var(--text-muted); margin-right: 8px;"></i>Ubah Kata Sandi</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 4px;">Biarkan kosong jika tidak ingin mengubah kata sandi.</p>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px;">Kata Sandi Baru</label>
            <input type="password" name="password" class="form-input" placeholder="••••••••" style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 12px; color: #fff;" autocapitalize="none">
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
            <label class="form-label" style="color: var(--text-muted); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 8px;">Konfirmasi Kata Sandi</label>
            <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" style="width: 100%; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 12px; color: #fff;" autocapitalize="none">
        </div>

        <div style="display: flex; justify-content: flex-end;">
            <button type="submit" class="btn-primary" style="padding: 12px 24px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-save"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection
