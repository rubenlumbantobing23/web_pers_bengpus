@extends('layouts.admin')

@section('page-title', 'Verifikasi Akses Arsip Rahasia')

@section('admin-content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 60vh;">
    <div class="glass-card" style="width: 100%; max-width: 450px; padding: 30px; text-align: center;">
        <i class="fa-solid fa-lock" style="font-size: 3rem; color: #ef4444; margin-bottom: 20px;"></i>
        <h3 style="font-size: 1.25rem; color: #fff; margin-bottom: 10px;">Arsip Bersifat Rahasia</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 24px;">
            Anda akan mengakses arsip <strong>{{ $letter->letter_number }}</strong>. Masukkan sandi akses khusus arsip rahasia untuk melanjutkan.
        </p>

        <form action="{{ route('admin.letters.secret.verify', $letter->id) }}" method="POST">
            @csrf
            <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                <label class="form-label" for="password">Sandi Akses Rahasia</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan sandi..." required>
            </div>

            <div style="display: flex; gap: 12px; justify-content: center;">
                <a href="{{ route('admin.letters.index') }}" class="btn-secondary" style="flex: 1; text-align: center;">Batal</a>
                <button type="submit" class="btn-danger" style="flex: 1;"><i class="fa-solid fa-unlock"></i> Buka Akses</button>
            </div>
        </form>
    </div>
</div>
@endsection
