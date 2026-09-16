@extends('layouts.user')

@section('page-title', 'Form Pengajuan Cuti')

@section('user-content')
<div style="max-width: 900px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px;">

    <!-- Title Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <h2 style="font-size: 1.5rem; color: #fff;">Pengajuan Cuti Anggota</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Ikuti langkah-langkah di bawah untuk mengajukan permohonan cuti</p>
        </div>
        <a href="{{ route('user.leave.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Stepper Header Indicator -->
    <div class="glass-card" style="padding: 16px 24px;">
        <div style="display: flex; align-items: center; justify-content: space-between; position: relative;">
            <div id="step-indicator-1" class="step-badge active" onclick="goToStep(1)" style="cursor: pointer;">
                <span class="step-num">1</span>
                <span class="step-title">Pilih Jenis Cuti</span>
            </div>
            <div style="flex-grow: 1; height: 2px; background: var(--border-color); margin: 0 8px;"></div>
            <div id="step-indicator-2" class="step-badge" onclick="goToStep(2)" style="cursor: pointer;">
                <span class="step-num">2</span>
                <span class="step-title">Syarat & Ketentuan</span>
            </div>
            <div style="flex-grow: 1; height: 2px; background: var(--border-color); margin: 0 8px;"></div>
            <div id="step-indicator-3" class="step-badge" onclick="goToStep(3)" style="cursor: pointer;">
                <span class="step-num">3</span>
                <span class="step-title">Pilih Tanggal</span>
            </div>
            <div style="flex-grow: 1; height: 2px; background: var(--border-color); margin: 0 8px;"></div>
            <div id="step-indicator-4" class="step-badge" onclick="goToStep(4)" style="cursor: pointer;">
                <span class="step-num">4</span>
                <span class="step-title">Surat Permohonan</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                <strong>Gagal Mengirim Pengajuan:</strong>
                <ul style="margin-top: 4px; padding-left: 18px;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Main Form Container -->
    <form action="{{ route('user.leave.store') }}" method="POST" enctype="multipart/form-data" id="leaveForm">
        @csrf
        
        <!-- ==================== STEP 1: PILIH JENIS CUTI ==================== -->
        <div id="step-1" class="form-step active">
            <div class="glass-card">
                <h3 style="font-size: 1.2rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-list-check" style="color: var(--primary);"></i> Langkah 1: Pilih Jenis Cuti
                </h3>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 24px;">
                    @foreach($leaveTypes as $type)
                        @php
                            if ($type->code === 'CT_HAMIL_LAHIR') {
                                $genderLower = strtolower($userGender ?? '');
                                $isFemale = str_contains($genderLower, 'wanita') || str_contains($genderLower, 'perempuan') || $genderLower === 'p' || $genderLower === 'w';
                                if (!$isFemale) {
                                    continue;
                                }
                            }
                        @endphp
                        <label class="leave-type-card" id="type-card-{{ $type->id }}" onclick="selectLeaveType({{ json_encode($type) }})">
                            <input type="radio" name="leave_type_id" value="{{ $type->id }}" style="display: none;" required>
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                <div style="font-size: 1.1rem; font-weight: 700; color: #fff;">{{ $type->name }}</div>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">
                                    @if($type->code === 'CT_KAWIN')
                                        Pria 3 Hari / Wanita 6 Hari
                                    @else
                                        Default {{ $type->default_days }} Hari
                                    @endif
                                </span>
                            </div>
                            <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.4;">{{ $type->description }}</p>
                        </label>
                    @endforeach
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 16px;">
                    <p style="font-size: 0.85rem; color: #f59e0b; font-weight: 500; margin: 0;">
                        * Pelaksanaan cuti menyesuaikan kebijakan atasan.
                    </p>
                    <button type="button" class="btn-military" id="btn-to-step-2" onclick="goToStep(2)" disabled>
                        Lanjutkan ke Syarat & Ketentuan &rarr;
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 2: SYARAT & KETENTUAN ==================== -->
        <div id="step-2" class="form-step" style="display: none;">
            <div class="glass-card">
                <h3 style="font-size: 1.2rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-file-contract" style="color: var(--accent-gold);"></i> Langkah 2: Syarat & Ketentuan Cuti
                </h3>

                <div style="background: rgba(9, 13, 24, 0.8); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 20px;">
                    <h4 style="font-size: 1.05rem; color: #34d399; margin-bottom: 8px;" id="terms-title">Cuti Tahunan</h4>
                    
                    <div style="margin-bottom: 16px;">
                        <strong style="color: #fff; font-size: 0.9rem;">Ketentuan Pelaksanaan:</strong>
                        <div id="terms-content" style="color: var(--text-sub); font-size: 0.9rem; margin-top: 6px; white-space: pre-line; line-height: 1.6;">
                            [Pilih jenis cuti terlebih dahulu]
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <strong style="color: #fff; font-size: 0.9rem;">Dokumen Persyaratan Yang Wajib Disiapkan:</strong>
                        <div id="terms-docs" style="color: var(--accent-gold); font-size: 0.875rem; margin-top: 6px; font-weight: 500;">
                            -
                        </div>
                    </div>

                    <div style="padding-top: 12px; border-top: 1px dashed rgba(255, 255, 255, 0.1);">
                        <p style="font-size: 0.85rem; color: #f59e0b; font-weight: 500; margin: 0;">
                            * Pelaksanaan cuti menyesuaikan kebijakan atasan.
                        </p>
                    </div>
                </div>

                <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 12px; padding: 16px; margin-bottom: 24px;">
                    <label style="display: flex; align-items: flex-start; gap: 12px; cursor: pointer;">
                        <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" onchange="toggleTermsAcceptance()" style="width: 20px; height: 20px; accent-color: var(--primary); margin-top: 2px;">
                        <span style="font-size: 0.925rem; color: #fef08a; font-weight: 600;">
                            Saya telah membaca, memahami, dan menyetujui seluruh syarat & ketentuan permohonan cuti di atas.
                        </span>
                    </label>
                </div>

                <div style="display: flex; justify-content: space-between;">
                    <button type="button" class="btn-secondary" onclick="goToStep(1)">
                        &larr; Kembali
                    </button>
                    <button type="button" class="btn-military" id="btn-to-step-3" onclick="goToStep(3)" disabled>
                        Lanjutkan Pilih Tanggal &rarr;
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 3: KALENDER PEMILIHAN TANGGAL ==================== -->
        <div id="step-3" class="form-step" style="display: none;">
            <div class="glass-card">
                <h3 style="font-size: 1.2rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-regular fa-calendar-days" style="color: #60a5fa;"></i> Langkah 3: Pilih Tanggal Cuti pada Kalender
                </h3>

                <!-- Information Bar -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div style="padding: 12px 16px; background: rgba(5, 150, 105, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 10px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Sisa Jatah Cuti Anda:</span>
                        <div style="font-size: 1.2rem; font-weight: 800; color: #34d399;">{{ $entitlement['available_after_pending'] }} Hari Kerja</div>
                    </div>
                    <div style="padding: 12px 16px; background: rgba(59, 130, 246, 0.15); border: 1px solid rgba(59, 130, 246, 0.3); border-radius: 10px;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Hari Kerja Terhitung:</span>
                        <div style="font-size: 1.2rem; font-weight: 800; color: #93c5fd;" id="calculated-days-badge">0 Hari Kerja</div>
                    </div>
                </div>

                <!-- Calendar Legend -->
                <div style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; padding: 10px 14px; background: rgba(255, 255, 255, 0.03); border-radius: 8px; font-size: 0.8rem;">
                    <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: #059669; border-radius: 3px;"></span> Tanggal Dipilih</div>
                    <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: #ef4444; border-radius: 3px;"></span> Libur Nasional</div>
                    <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: #334155; border-radius: 3px;"></span> Akhir Pekan (Sabtu/Minggu)</div>
                    <div style="display: flex; align-items: center; gap: 6px;"><span style="width: 12px; height: 12px; background: #d97706; border-radius: 3px;"></span> Cuti Anda Sebelumnya</div>
                </div>

                <!-- Interactive Date Picker Container -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">
                    <div>
                        <label class="form-label" for="start_date">Tanggal Mulai Cuti</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" min="{{ date('Y-m-d') }}" onchange="onDateSelectionChange()" required>
                    </div>
                    <div>
                        <label class="form-label" for="end_date">Tanggal Selesai Cuti</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" min="{{ date('Y-m-d') }}" onchange="onDateSelectionChange()" required>
                    </div>
                </div>

                <!-- Visual Hotel-Style Calendar -->
                <div style="background: rgba(9, 13, 24, 0.9); border: 1px solid var(--border-color); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <button type="button" class="btn-secondary" style="padding: 6px 12px;" onclick="changeMonth(-1)"><i class="fa-solid fa-chevron-left"></i></button>
                        <h4 style="font-size: 1.1rem; color: #fff;" id="calendar-month-year">Agustus 2026</h4>
                        <button type="button" class="btn-secondary" style="padding: 6px 12px;" onclick="changeMonth(1)"><i class="fa-solid fa-chevron-right"></i></button>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; text-align: center; font-size: 0.8rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px;">
                        <div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div style="color: #f87171;">Sab</div><div style="color: #f87171;">Min</div>
                    </div>

                    <div id="calendar-grid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px;">
                        <!-- Rendered dynamically by JavaScript -->
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between;">
                    <button type="button" class="btn-secondary" onclick="goToStep(2)">
                        &larr; Kembali
                    </button>
                    <button type="button" class="btn-military" id="btn-to-step-4" onclick="goToStep(4)" disabled>
                        Lanjutkan ke Data Tambahan &rarr;
                    </button>
                </div>
            </div>
        </div>

        <!-- ==================== STEP 4: DATA & SURAT PERMOHONAN ==================== -->
        <div id="step-4" class="form-step" style="display: none;">
            <div class="glass-card">
                <h3 style="font-size: 1.2rem; color: #fff; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-file-signature" style="color: #a78bfa;"></i> Langkah 4: Data Tambahan & Surat Permohonan
                </h3>

                <!-- Summary Review Box -->
                <div style="background: rgba(5, 150, 105, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 16px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Ringkasan Pengajuan:</span>
                        <div style="font-size: 1rem; font-weight: 700; color: #fff;" id="summary-leave-name">Cuti Tahunan</div>
                        <div style="font-size: 0.85rem; color: var(--text-sub);" id="summary-dates">-</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge badge-approved" style="font-size: 0.9rem;" id="summary-working-days">0 Hari Kerja</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="reason">Alasan / Keperluan Cuti</label>
                    <textarea id="reason" name="reason" class="form-control" rows="2" placeholder="Jelaskan alasan cuti secara ringkas..." required>{{ old('reason') }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label" for="tujuan">Tujuan / Alamat Cuti</label>
                    <textarea id="tujuan" name="tujuan" class="form-control" rows="2" placeholder="Contoh: Jl. Merdeka No. 10, Bandung" required>{{ old('tujuan') }}</textarea>
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label" for="pengikut">Pengikut</label>
                        <input type="text" id="pengikut" name="pengikut" class="form-control" placeholder="Contoh: Keluarga / - " value="{{ old('pengikut') }}" required>
                    </div>
                    <div>
                        <label class="form-label" for="kendaraan">Kendaraan</label>
                        <input type="text" id="kendaraan" name="kendaraan" class="form-control" placeholder="Contoh: Lihat kenyataan / Pribadi" value="{{ old('kendaraan') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="kodim_koramil">Kodim / Koramil Terdekat (Untuk Tembusan Surat)</label>
                    <input type="text" id="kodim_koramil" name="kodim_koramil" class="form-control" placeholder="Contoh: Koramil 0925/Cimahi Utara" value="{{ old('kodim_koramil') }}" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="emergency_contact">Nomor Telepon / Kontak Darurat Yang Dapat Dihubungi</label>
                    <input type="text" id="emergency_contact" name="emergency_contact" class="form-control" placeholder="Contoh: 08123456789 (Istri / Orang Tua)" value="{{ old('emergency_contact') }}" required>
                </div>

                <!-- Template Surat Permohonan Cuti (4 TTD) Box -->
                <div style="background: rgba(30, 41, 59, 0.7); border: 1px dashed rgba(96, 165, 250, 0.5); border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                        <div style="flex: 1; min-width: 260px;">
                            <h4 style="font-size: 1.05rem; color: #60a5fa; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                                <i class="fa-solid fa-file-word" style="font-size: 1.2rem; color: #3b82f6;"></i> Template Surat Permohonan Cuti
                            </h4>
                            <p style="font-size: 0.875rem; color: var(--text-sub); line-height: 1.5; margin-bottom: 10px;">
                                Unduh blangko Surat Permohonan Cuti. Surat ini akan disesuaikan otomatis dengan Kategori Pemohon Anda.
                            </p>
                            <p style="font-size: 0.825rem; color: var(--accent-gold); font-weight: 500; margin-bottom: 14px;">
                                * Cetak, lengkapi tanda tangan dari para pejabat terkait, lalu unggah kembali hasilnya di bawah.
                            </p>
                            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                <a href="javascript:void(0)" onclick="downloadLeavePermohonanTemplate()" class="btn-secondary" style="background: rgba(37, 99, 235, 0.25); border-color: rgba(59, 130, 246, 0.5); color: #93c5fd; font-size: 0.875rem; font-weight: 600; padding: 10px 18px;">
                                    <i class="fa-solid fa-download"></i> Unduh Surat Permohonan (.doc)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="permohonan_document">Upload Surat Permohonan (Sudah Ditandatangani) *</label>
                    <input type="file" id="permohonan_document" name="permohonan_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                    <small style="color: var(--text-muted); display: block; margin-top: 6px;">Unggah Surat Permohonan Cuti yang telah ditandatangani oleh atasan. Format: PDF, JPG, PNG (Maksimal 5MB).</small>
                </div>

                <div class="form-group">
                    <label class="form-label" for="supporting_documents">Upload Persuratan Pendukung (Jika Ada / Disyaratkan)</label>
                    <input type="file" id="supporting_documents" name="supporting_documents[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png">
                    <small style="color: var(--text-muted); display: block; margin-top: 6px;">Unggah lampiran pendukung seperti Surat Keterangan Dokter, Undangan Nikah, dll. Bisa pilih lebih dari satu file. Format: PDF, JPG, PNG (Maksimal 5MB per file).</small>
                </div>

                <div style="display: flex; justify-content: space-between; margin-top: 28px;">
                    <button type="button" class="btn-secondary" onclick="goToStep(3)">
                        &larr; Kembali
                    </button>
                    <button type="submit" class="btn-military" style="padding: 14px 32px;">
                        <i class="fa-solid fa-paper-plane"></i> KIRIM PENGAJUAN CUTI
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

<!-- ==================== INTERACTIVE QUOTA WARNING MODAL (outside all containers) ==================== -->
<div id="quotaWarningModal" class="custom-modal-overlay" style="display: none;">
    <div class="custom-modal-content">
        <button type="button" class="modal-close-btn" onclick="closeQuotaModal()">&times;</button>

        <div style="text-align: center; margin-bottom: 20px;">
            <div class="modal-icon-wrapper">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <h3 class="modal-title">Peringatan: Jatah Cuti Melebihi Batas</h3>
            <p class="modal-subtitle">Pengajuan hari kerja Anda melebihi kuota / jatah yang tersedia.</p>
        </div>

        <div class="modal-info-card">
            <div class="modal-info-row">
                <span class="info-label"><i class="fa-solid fa-list-check" style="color: #60a5fa;"></i> Jenis Cuti:</span>
                <span class="info-value" id="modal-leave-name" style="color: #fff;">-</span>
            </div>
            <div class="modal-info-row">
                <span class="info-label"><span class="status-dot dot-red"></span> Hari Kerja Diajukan:</span>
                <span class="info-value text-red" id="modal-requested-days">0 Hari</span>
            </div>
            <div class="modal-info-row">
                <span class="info-label"><span class="status-dot dot-green"></span> Sisa Jatah / Maksimal:</span>
                <span class="info-value text-green" id="modal-max-days">0 Hari</span>
            </div>
            <div class="modal-info-row" style="border-top: 1px dashed rgba(255,255,255,0.1); padding-top: 10px; margin-top: 4px;">
                <span class="info-label" style="color: #f59e0b; font-weight: 600;"><i class="fa-solid fa-circle-exclamation"></i> Kelebihan Hari:</span>
                <span class="info-value text-amber" id="modal-exceeded-days" style="font-size: 1.05rem; font-weight: 800;">+0 Hari</span>
            </div>
        </div>

        <div style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.3); border-radius: 10px; padding: 12px 16px; margin-bottom: 24px; font-size: 0.85rem; color: #fef08a; display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-circle-info" style="font-size: 1.1rem; color: #f59e0b; flex-shrink: 0;"></i>
            <div>Anda tidak dapat melanjutkan ke langkah berikutnya sebelum durasi cuti disesuaikan.</div>
        </div>

        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <button type="button" class="btn-military" style="flex: 1; min-width: 200px; justify-content: center;" onclick="autoAdjustDates()">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Sesuaikan Tanggal Otomatis
            </button>
            <button type="button" class="btn-secondary" style="flex: 1; min-width: 130px; justify-content: center;" onclick="closeQuotaModal()">
                <i class="fa-solid fa-pen-to-square"></i> Ubah Manual
            </button>
        </div>
    </div>
</div>

<style>
    .step-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
    }

    .step-badge.active {
        color: var(--primary);
    }

    .step-num {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .step-badge.active .step-num {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 0 12px rgba(5, 150, 105, 0.5);
    }

    .leave-type-card {
        background: rgba(9, 13, 24, 0.7);
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 18px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .leave-type-card:hover {
        border-color: rgba(16, 185, 129, 0.4);
        background: rgba(19, 27, 46, 0.9);
    }

    .leave-type-card.selected {
        border-color: var(--primary);
        background: linear-gradient(135deg, rgba(5, 150, 105, 0.15) 0%, rgba(19, 27, 46, 0.9) 100%);
        box-shadow: 0 0 15px rgba(5, 150, 105, 0.2);
    }

    .cal-cell {
        padding: 10px 4px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
        min-height: 40px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.02);
        color: var(--text-main);
    }

    .cal-cell.weekend {
        background: rgba(51, 65, 85, 0.4);
        color: #94a3b8;
    }

    .cal-cell.holiday {
        background: rgba(239, 68, 68, 0.25);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.4);
    }

    .cal-cell.selected {
        background: var(--primary) !important;
        color: #fff !important;
        font-weight: 800;
        box-shadow: 0 0 10px rgba(5, 150, 105, 0.6);
    }

    .cal-cell.in-range {
        background: rgba(5, 150, 105, 0.25) !important;
        color: #34d399 !important;
    }

    .cal-cell.existing-leave {
        background: rgba(217, 119, 6, 0.3) !important;
        color: #fbbf24 !important;
    }

    /* Modal Overlay & Card Styling */
    .custom-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(4, 7, 15, 0.85);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        opacity: 0;
        animation: fadeInOverlay 0.25s forwards cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeInOverlay {
        to { opacity: 1; }
    }

    .custom-modal-content {
        background: linear-gradient(145deg, #131b2e 0%, #0d1424 100%);
        border: 1px solid rgba(239, 68, 68, 0.4);
        box-shadow: 0 0 35px rgba(239, 68, 68, 0.25), 0 20px 50px rgba(0, 0, 0, 0.6);
        border-radius: 20px;
        padding: 28px;
        max-width: 520px;
        width: 100%;
        position: relative;
        transform: scale(0.9);
        animation: scaleUpModal 0.3s forwards cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes scaleUpModal {
        to { transform: scale(1); }
    }

    .modal-close-btn {
        position: absolute;
        top: 16px;
        right: 18px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        width: 34px;
        height: 34px;
        border-radius: 50%;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.4);
    }

    .modal-icon-wrapper {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.15);
        border: 2px solid rgba(239, 68, 68, 0.4);
        color: #ef4444;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 14px;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        animation: pulseWarning 2s infinite ease-in-out;
    }

    @keyframes pulseWarning {
        0%, 100% { box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); transform: scale(1); }
        50% { box-shadow: 0 0 35px rgba(239, 68, 68, 0.6); transform: scale(1.05); }
    }

    .modal-title {
        font-size: 1.25rem;
        color: #ffffff;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .modal-subtitle {
        font-size: 0.875rem;
        color: var(--text-muted);
    }

    .modal-info-card {
        background: rgba(9, 13, 24, 0.8);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 16px 20px;
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .modal-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
    }

    .info-label {
        color: var(--text-sub);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .info-value {
        font-weight: 700;
    }

    .text-red { color: #f87171; }
    .text-green { color: #34d399; }
    .text-amber { color: #fbbf24; }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }
    .dot-red { background: #ef4444; box-shadow: 0 0 6px #ef4444; }
    .dot-green { background: #10b981; box-shadow: 0 0 6px #10b981; }
</style>

@push('scripts')
<script>
    const holidaysData = @json($holidays);
    const existingLeavesData = @json($existingLeaves);
    const userRemainingDays = {{ $entitlement['available_after_pending'] }};
    const userGender = @json($userGender);

    let selectedLeaveTypeObj = null;
    let currentCalMonth = new Date().getMonth();
    let currentCalYear = new Date().getFullYear();
    let computedWorkingDays = 0;

    function selectLeaveType(typeObj) {
        selectedLeaveTypeObj = typeObj;
        
        document.querySelectorAll('.leave-type-card').forEach(c => c.classList.remove('selected'));
        const selectedCard = document.getElementById('type-card-' + typeObj.id);
        if (selectedCard) {
            selectedCard.classList.add('selected');
            selectedCard.querySelector('input[type="radio"]').checked = true;
        }

        document.getElementById('terms-title').innerText = typeObj.name;
        
        let termsText = typeObj.terms_conditions || 'Tidak ada ketentuan khusus.';
        if (typeObj.code === 'CT_KAWIN') {
            const gLower = (userGender || '').toLowerCase();
            const isFemale = gLower.includes('wanita') || gLower.includes('perempuan') || gLower === 'p' || gLower === 'w';
            const userMax = isFemale ? 6 : 3;
            const gLabel = isFemale ? 'Wanita' : 'Pria';
            termsText += `\n\n📌 Status Jenis Kelamin Profil Anda: ${userGender || 'Belum diisi'} → Maksimal jatah Cuti Kawin Anda: ${userMax} Hari Kerja (${gLabel}). Pelaksanaan cuti menyesuaikan kebijakan atasan.`;
        }
        document.getElementById('terms-content').innerText = termsText;

        document.getElementById('terms-docs').innerText = typeObj.required_documents_info || 'Tidak ada dokumen khusus.';
        document.getElementById('summary-leave-name').innerText = typeObj.name;

        document.getElementById('btn-to-step-2').disabled = false;
    }

    function toggleTermsAcceptance() {
        const accepted = document.getElementById('terms_accepted').checked;
        document.getElementById('btn-to-step-3').disabled = !accepted;
    }

    function getMaxAllowedDays() {
        if (!selectedLeaveTypeObj) return userRemainingDays;
        if (selectedLeaveTypeObj.code === 'CT_TAHUNAN') {
            return userRemainingDays;
        }
        if (selectedLeaveTypeObj.code === 'CT_KAWIN') {
            const gLower = (userGender || '').toLowerCase();
            if (gLower.includes('wanita') || gLower.includes('perempuan') || gLower === 'p' || gLower === 'w') {
                return 6;
            }
            return 3;
        }
        return selectedLeaveTypeObj.default_days > 0 ? selectedLeaveTypeObj.default_days : userRemainingDays;
    }

    function showQuotaWarningModal(requestedDays, maxDays, leaveTypeName) {
        const exceededDays = requestedDays - maxDays;
        
        document.getElementById('modal-leave-name').innerText = leaveTypeName || 'Cuti';
        document.getElementById('modal-requested-days').innerText = requestedDays + ' Hari Kerja';
        document.getElementById('modal-max-days').innerText = maxDays + ' Hari Kerja';
        document.getElementById('modal-exceeded-days').innerText = '+' + exceededDays + ' Hari Kerja';

        const modal = document.getElementById('quotaWarningModal');
        modal.style.display = 'flex';

        modal.onclick = function(e) {
            if (e.target === modal) {
                closeQuotaModal();
            }
        };
    }

    function closeQuotaModal() {
        const modal = document.getElementById('quotaWarningModal');
        modal.style.display = 'none';
    }

    function isWorkingDay(dateObj) {
        const dayOfWeek = dateObj.getDay(); // 0 = Sun, 6 = Sat
        if (dayOfWeek === 0 || dayOfWeek === 6) return false;
        const yyyy = dateObj.getFullYear();
        const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
        const dd = String(dateObj.getDate()).padStart(2, '0');
        const dateStr = `${yyyy}-${mm}-${dd}`;
        if (holidaysData.some(h => h.date === dateStr)) return false;
        return true;
    }

    function autoAdjustDates() {
        const startVal = document.getElementById('start_date').value;
        if (!startVal) {
            closeQuotaModal();
            return;
        }
        
        const maxDays = getMaxAllowedDays();
        if (maxDays <= 0) {
            closeQuotaModal();
            return;
        }

        let currentDate = new Date(startVal + 'T00:00:00');
        let workingDaysCount = 0;
        let lastWorkingDate = new Date(currentDate);

        for (let i = 0; i < 365; i++) {
            if (isWorkingDay(currentDate)) {
                workingDaysCount++;
                lastWorkingDate = new Date(currentDate);
                if (workingDaysCount >= maxDays) break;
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }

        const yyyy = lastWorkingDate.getFullYear();
        const mm = String(lastWorkingDate.getMonth() + 1).padStart(2, '0');
        const dd = String(lastWorkingDate.getDate()).padStart(2, '0');
        const newEndVal = `${yyyy}-${mm}-${dd}`;

        document.getElementById('end_date').value = newEndVal;
        closeQuotaModal();
        onDateSelectionChange();
    }

    function goToStep(stepNum) {
        // Auto-enable next buttons if data is already filled (for back-and-forth navigation)
        if (selectedLeaveTypeObj) {
            document.getElementById('btn-to-step-2').disabled = false;
        }
        if (document.getElementById('terms_accepted').checked) {
            document.getElementById('btn-to-step-3').disabled = false;
        }
        if (document.getElementById('start_date').value && document.getElementById('end_date').value && computedWorkingDays > 0) {
            const maxAllowed = getMaxAllowedDays();
            if (maxAllowed === 0 || computedWorkingDays <= maxAllowed) {
                document.getElementById('btn-to-step-4').disabled = false;
            }
        }

        // Validasi Langkah 1: Harus memilih jenis cuti
        if (stepNum >= 2 && !selectedLeaveTypeObj) {
            alert('Silakan pilih jenis cuti terlebih dahulu pada Langkah 1.');
            return;
        }

        // Validasi Langkah 2: Harus menyetujui syarat & ketentuan
        if (stepNum >= 3 && !document.getElementById('terms_accepted').checked) {
            alert('Anda harus menyetujui syarat & ketentuan permohonan cuti pada Langkah 2.');
            return;
        }

        // Validasi Langkah 3: Harus memilih tanggal mulai dan selesai cuti yang valid
        if (stepNum >= 4) {
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;
            
            if (!startDate || !endDate) {
                alert('Silakan pilih tanggal mulai dan tanggal selesai cuti terlebih dahulu pada kalender.');
                return;
            }

            if (computedWorkingDays <= 0) {
                alert('Jumlah hari kerja untuk rentang tanggal yang Anda pilih adalah 0 hari kerja (seluruhnya hari libur/akhir pekan). Silakan pilih tanggal lain.');
                return;
            }

            const maxAllowed = getMaxAllowedDays();
            if (maxAllowed > 0 && computedWorkingDays > maxAllowed) {
                showQuotaWarningModal(computedWorkingDays, maxAllowed, selectedLeaveTypeObj ? selectedLeaveTypeObj.name : 'Cuti');
                return;
            }
        }

        document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
        document.getElementById('step-' + stepNum).style.display = 'block';

        document.querySelectorAll('.step-badge').forEach((b, idx) => {
            if (idx + 1 === stepNum) {
                b.classList.add('active');
            } else if (idx + 1 < stepNum) {
                b.classList.add('active');
            } else {
                b.classList.remove('active');
            }
        });

        if (stepNum === 3) {
            renderCalendar();
        }
    }

    function onDateSelectionChange() {
        const startVal = document.getElementById('start_date').value;
        const endVal = document.getElementById('end_date').value;

        if (!startVal || !endVal) {
            document.getElementById('btn-to-step-4').disabled = true;
            return;
        }

        if (endVal < startVal) {
            document.getElementById('end_date').value = startVal;
        }

        const s = document.getElementById('start_date').value;
        const e = document.getElementById('end_date').value || s;

        // Perform AJAX calculation
        fetch('{{ route("user.leave.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ start_date: s, end_date: e })
        })
        .then(res => res.json())
        .then(data => {
            computedWorkingDays = data.working_days;
            document.getElementById('calculated-days-badge').innerText = computedWorkingDays + ' Hari Kerja';
            document.getElementById('summary-working-days').innerText = computedWorkingDays + ' Hari Kerja';
            document.getElementById('summary-dates').innerText = s + ' s/d ' + e;

            const maxAllowed = getMaxAllowedDays();
            if (maxAllowed > 0 && computedWorkingDays > maxAllowed) {
                document.getElementById('btn-to-step-4').disabled = true;
                showQuotaWarningModal(computedWorkingDays, maxAllowed, selectedLeaveTypeObj ? selectedLeaveTypeObj.name : 'Cuti');
                renderCalendar();
                return;
            }

            document.getElementById('btn-to-step-4').disabled = (computedWorkingDays <= 0);
            renderCalendar();
        });
    }

    function changeMonth(delta) {
        currentCalMonth += delta;
        if (currentCalMonth > 11) {
            currentCalMonth = 0;
            currentCalYear++;
        } else if (currentCalMonth < 0) {
            currentCalMonth = 11;
            currentCalYear--;
        }
        renderCalendar();
    }

    function renderCalendar() {
        const grid = document.getElementById('calendar-grid');
        grid.innerHTML = '';

        const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        document.getElementById('calendar-month-year').innerText = monthNames[currentCalMonth] + " " + currentCalYear;

        const firstDay = new Date(currentCalYear, currentCalMonth, 1).getDay();
        // Convert Sunday (0) to 7 for Mon=1, Sun=7
        const startDayIndex = firstDay === 0 ? 6 : firstDay - 1;
        const daysInMonth = new Date(currentCalYear, currentCalMonth + 1, 0).getDate();

        // Empty cells for alignment
        for (let i = 0; i < startDayIndex; i++) {
            const emptyDiv = document.createElement('div');
            grid.appendChild(emptyDiv);
        }

        const startDateVal = document.getElementById('start_date').value;
        const endDateVal = document.getElementById('end_date').value || startDateVal;

        for (let d = 1; d <= daysInMonth; d++) {
            const cell = document.createElement('div');
            cell.className = 'cal-cell';
            
            const monthStr = String(currentCalMonth + 1).padStart(2, '0');
            const dayStr = String(d).padStart(2, '0');
            const dateStr = `${currentCalYear}-${monthStr}-${dayStr}`;

            cell.innerText = d;

            const dateObj = new Date(currentCalYear, currentCalMonth, d);
            const dayOfWeek = dateObj.getDay(); // 0 = Sun, 6 = Sat

            if (dayOfWeek === 0 || dayOfWeek === 6) {
                cell.classList.add('weekend');
            }

            const isHoliday = holidaysData.find(h => h.date === dateStr);
            if (isHoliday) {
                cell.classList.add('holiday');
                cell.title = isHoliday.name;
            }

            if (startDateVal && dateStr === startDateVal) {
                cell.classList.add('selected');
            } else if (endDateVal && dateStr === endDateVal) {
                cell.classList.add('selected');
            } else if (startDateVal && endDateVal && dateStr > startDateVal && dateStr < endDateVal) {
                cell.classList.add('in-range');
            }

            const today = new Date();
            const todayStr = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

            if (dateStr < todayStr) {
                cell.style.opacity = '0.3';
                cell.style.cursor = 'not-allowed';
                // No onclick event for past dates
            } else {
                cell.onclick = function() {
                    const currentStart = document.getElementById('start_date').value;
                    const currentEnd = document.getElementById('end_date').value;

                    if (!currentStart || (currentStart && currentEnd && currentStart !== currentEnd)) {
                        document.getElementById('start_date').value = dateStr;
                        document.getElementById('end_date').value = dateStr;
                    } else {
                        if (dateStr >= currentStart) {
                            document.getElementById('end_date').value = dateStr;
                        } else {
                            document.getElementById('start_date').value = dateStr;
                            document.getElementById('end_date').value = dateStr;
                        }
                    }
                    onDateSelectionChange();
                };
            }

            grid.appendChild(cell);
        }
    }

    function downloadLeavePermohonanTemplate() {
        const leaveTypeId = selectedLeaveTypeObj ? selectedLeaveTypeObj.id : '';
        const startDate = document.getElementById('start_date').value || '';
        const endDate = document.getElementById('end_date').value || '';
        const reason = encodeURIComponent(document.getElementById('reason').value || '');
        const emergencyContact = encodeURIComponent(document.getElementById('emergency_contact').value || '');

        let url = `{{ route('user.leave.template_permohonan') }}?leave_type_id=${leaveTypeId}&start_date=${startDate}&end_date=${endDate}&reason=${reason}&emergency_contact=${emergencyContact}`;
        window.location.href = url;
    }

</script>
@endpush
@endsection
