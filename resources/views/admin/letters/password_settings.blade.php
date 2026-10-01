@extends('layouts.admin')

@section('page-title', 'Keamanan Arsip Rahasia')

@section('admin-content')
<div style="max-width: 600px; margin: 0 auto;">
    <div class="glass-card" style="padding: 30px;">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
            <div style="width: 50px; height: 50px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2); display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-lock" style="font-size: 1.5rem; color: var(--accent-gold);"></i>
            </div>
            <div>
                <h2 style="font-size: 1.25rem; font-weight: 700; color: #fff; margin: 0 0 4px 0;">Sandi Arsip Rahasia</h2>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0;">Password ini digunakan secara global untuk mengakses seluruh arsip surat dengan klasifikasi RAHASIA.</p>
            </div>
        </div>

        <form action="{{ route('admin.letters.settings.password.update') }}" method="POST">
            @csrf

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="current_password" style="display: block; margin-bottom: 8px; color: var(--text-main); font-weight: 600; font-size: 0.9rem;">
                    Sandi Saat Ini <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-key" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="current_password" id="current_password" class="form-control" style="width: 100%; padding: 10px 10px 10px 36px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; color: #fff;" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="new_password" style="display: block; margin-bottom: 8px; color: var(--text-main); font-weight: 600; font-size: 0.9rem;">
                    Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock-open" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="new_password" id="new_password" class="form-control" style="width: 100%; padding: 10px 10px 10px 36px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; color: #fff;" required minlength="8">
                </div>
                <small style="display: block; margin-top: 4px; color: var(--text-muted); font-size: 0.75rem;">Minimal 8 karakter.</small>
            </div>

            <div class="form-group" style="margin-bottom: 30px;">
                <label for="new_password_confirmation" style="display: block; margin-bottom: 8px; color: var(--text-main); font-weight: 600; font-size: 0.9rem;">
                    Konfirmasi Sandi Baru <span style="color: #ef4444;">*</span>
                </label>
                <div style="position: relative;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" style="width: 100%; padding: 10px 10px 10px 36px; background: var(--bg-input); border: 1px solid var(--border-color); border-radius: 8px; color: #fff;" required minlength="8">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                <i class="fa-solid fa-save"></i> Ganti Password
            </button>
        </form>
    </div>
</div>

<style>
    .form-control:focus {
        outline: none;
        border-color: var(--accent-gold) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
</style>
@endsection
