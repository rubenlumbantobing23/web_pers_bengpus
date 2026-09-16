@extends('layouts.admin')

@section('page-title', 'Pengaturan Template Persuratan')

@section('admin-content')
<div style="display: flex; flex-direction: column; gap: 24px;">

    <!-- Top Action Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengaturan Template Persuratan</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Kelola template file Microsoft Word (.docx) untuk format surat menyurat otomatis</p>
        </div>
    </div>

    @if(session('success'))
        <div class="glass-card" style="padding: 16px 20px; border-left: 4px solid var(--accent-success); background: rgba(16, 185, 129, 0.05);">
            <div style="display: flex; gap: 12px; align-items: center;">
                <i class="fa-solid fa-circle-check" style="color: var(--accent-success); font-size: 1.2rem;"></i>
                <span style="color: #fff; font-weight: 500;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-card" style="padding: 16px 20px; border-left: 4px solid var(--accent-danger); background: rgba(239, 68, 68, 0.05);">
            <div style="display: flex; gap: 12px; align-items: center;">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--accent-danger); font-size: 1.2rem;"></i>
                <span style="color: #fff; font-weight: 500;">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="glass-card" style="padding: 16px 20px; border-left: 4px solid var(--accent-danger); background: rgba(239, 68, 68, 0.05);">
            <div style="display: flex; gap: 12px; align-items: flex-start;">
                <i class="fa-solid fa-circle-exclamation" style="color: var(--accent-danger); font-size: 1.2rem; margin-top: 2px;"></i>
                <div>
                    <ul style="color: #fff; font-weight: 500; margin: 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(600px, 1fr)); gap: 24px;">
        @foreach($templates as $key => $template)
        <div class="glass-card" style="display: flex; flex-direction: column; overflow: hidden; height: 100%;">
            <div style="padding: 24px; border-bottom: 1px solid var(--border-color); display: flex; align-items: flex-start; justify-content: space-between;">
                <div>
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(37, 99, 235, 0.1); color: var(--accent-primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="fa-solid fa-file-word"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.2rem; color: #fff; margin: 0;">{{ $template['name'] }}</h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">
                                @if($template['exists'])
                                    <span style="color: var(--accent-success);"><i class="fa-solid fa-check"></i> File Aktif</span> &bull; 
                                    Update terakhir: {{ date('d M Y, H:i', $template['last_modified']) }}
                                @else
                                    <span style="color: var(--accent-danger);"><i class="fa-solid fa-xmark"></i> File Belum Ada</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0; padding-top: 8px;">{{ $template['description'] }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.templates.download', $key) }}" class="btn-secondary" style="white-space: nowrap; {{ !$template['exists'] ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                        <i class="fa-solid fa-download"></i> Download Saat Ini
                    </a>
                </div>
            </div>

            <div style="padding: 24px; display: flex; flex-direction: column; gap: 20px; flex-grow: 1;">
                <div>
                    <h4 style="font-size: 0.95rem; color: #fff; margin-bottom: 12px;"><i class="fa-solid fa-code"></i> Placeholder Tersedia</h4>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Gunakan kode di bawah ini di dalam file .docx Anda. Sistem akan menggantinya dengan data asli saat didownload.</p>
                    
                    <div style="background: var(--bg-dark); border-radius: 8px; border: 1px solid var(--border-color); padding: 12px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px;">
                        @foreach($template['placeholders'] as $code => $desc)
                            <div style="font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
                                <code style="background: rgba(255,255,255,0.1); color: var(--accent-gold); padding: 2px 6px; border-radius: 4px; font-family: monospace;">{{ $code }}</code>
                                <span style="color: var(--text-muted);">= {{ $desc }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="background: rgba(255,255,255,0.02); border: 1px dashed var(--border-color); border-radius: 12px; padding: 20px; margin-top: auto;">
                    <form action="{{ route('admin.templates.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="template_type" value="{{ $key }}">
                        
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <label style="font-size: 0.9rem; color: #fff; font-weight: 500;">Ganti / Upload Template Baru (.docx)</label>
                            <input type="file" name="template_file" accept=".docx" class="form-control" required style="padding: 10px; background: var(--bg-dark);">
                            <button type="submit" class="btn-military" style="width: 100%; justify-content: center;">
                                <i class="fa-solid fa-upload"></i> Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
