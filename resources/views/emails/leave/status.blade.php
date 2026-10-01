<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Pers Bengpuskomlekad</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color:#1f2937; -webkit-font-smoothing: antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8; padding:40px 20px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">

                {{-- HEADER --}}
                <tr>
                    <td style="background:#090d18; padding:35px 30px; text-align:center; border-bottom: 3px solid #10b981;">
                        @if(file_exists(public_path('images/logo.png')))
                            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Logo Bengpuskomlekad" style="height:60px; margin-bottom:15px;">
                        @else
                            <img src="{{ asset('images/logo.png') }}" alt="Logo Bengpuskomlekad" style="height:60px; margin-bottom:15px;">
                        @endif
                        <div style="font-size:20px; font-weight:800; color:#ffffff; letter-spacing: 1px;">
                            BENGPUSKOMLEKAD
                        </div>
                        <div style="font-size:12px; color:#10b981; margin-top:5px; font-weight:600; letter-spacing: 2px;">
                            SISTEM INFORMASI PERSONALIA
                        </div>
                    </td>
                </tr>

                {{-- CONTENT --}}
                <tr>
                    <td style="padding:40px 35px;">

                        <h2 style="margin:0 0 20px 0; color:#111827; font-size:22px; font-weight:700;">
                            {{ $title }}
                        </h2>

                        <p style="font-size:15px; line-height:1.6; margin:0 0 15px 0; color:#374151;">
                            Yth. <strong>{{ $user->name }}</strong>,
                        </p>

                        <p style="font-size:15px; line-height:1.6; margin:0 0 30px 0; color:#4b5563;">
                            {{ $statusMessage }}
                        </p>

                        {{-- DETAIL PENGAJUAN --}}
                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:20px; margin-bottom:30px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding-bottom:15px; width:50%;">
                                        <div style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; margin-bottom:4px;">Nomor Pengajuan</div>
                                        <div style="font-size:15px; color:#0f172a; font-weight:700;">{{ $leaveRequest->request_number ?: '-' }}</div>
                                    </td>
                                    <td style="padding-bottom:15px; width:50%;">
                                        <div style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; margin-bottom:4px;">Jenis Cuti</div>
                                        <div style="font-size:15px; color:#0f172a; font-weight:700;">{{ $leaveRequest->leaveType?->name ?? 'Cuti Biasa' }}</div>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-bottom:0;" colspan="2">
                                        <div style="font-size:12px; color:#64748b; font-weight:600; text-transform:uppercase; margin-bottom:4px;">Keperluan</div>
                                        <div style="font-size:14px; color:#334155; line-height:1.5;">{{ $leaveRequest->reason ?? '-' }}</div>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        {{-- STATUS CARD --}}
                        @if ($status === 'approved' || $status === 'selesai' || $status === 'disetujui')
                            <div style="background:#ecfdf5; border-left:4px solid #10b981; padding:16px 20px; margin-bottom:30px; border-radius:4px;">
                                <div style="font-size:13px; color:#065f46; font-weight:700; margin-bottom:4px; text-transform:uppercase;">Status: Disetujui</div>
                                <div style="font-size:14px; color:#047857; line-height:1.5;">
                                    Jumlah hari yang disetujui: <strong>{{ $leaveRequest->approved_days ?? $leaveRequest->working_days_count }} hari</strong>
                                </div>
                            </div>
                        @elseif ($status === 'rejected' || $status === 'ditolak')
                            <div style="background:#fef2f2; border-left:4px solid #ef4444; padding:16px 20px; margin-bottom:30px; border-radius:4px;">
                                <div style="font-size:13px; color:#991b1b; font-weight:700; margin-bottom:4px; text-transform:uppercase;">Status: Ditolak</div>
                                <div style="font-size:14px; color:#b91c1c; line-height:1.5;">
                                    <strong>Alasan penolakan:</strong><br>
                                    {{ $leaveRequest->rejection_reason ?: 'Tidak ada alasan spesifik yang dicantumkan.' }}
                                </div>
                            </div>
                        @elseif ($status === 'cancelled' || $status === 'dibatalkan')
                            <div style="background:#fff7ed; border-left:4px solid #f97316; padding:16px 20px; margin-bottom:30px; border-radius:4px;">
                                <div style="font-size:13px; color:#9a3412; font-weight:700; margin-bottom:4px; text-transform:uppercase;">Status: Dibatalkan</div>
                                <div style="font-size:14px; color:#c2410c; line-height:1.5;">
                                    Pengajuan cuti ini telah dibatalkan.
                                </div>
                            </div>
                        @endif

                        {{-- BUTTON --}}
                        @if(isset($detailUrl))
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">
                                    <a href="{{ $detailUrl }}"
                                       style="display:inline-block; background:#10b981; color:#ffffff; text-decoration:none; font-weight:600; padding:14px 30px; border-radius:6px; font-size:15px; letter-spacing: 0.5px;">
                                        Lihat Detail Pengajuan
                                    </a>
                                </td>
                            </tr>
                        </table>

                        <hr style="border:none; border-top:1px solid #e2e8f0; margin:35px 0 25px 0;">

                        <p style="font-size:12px; line-height:1.5; color:#94a3b8; margin:0 0 8px 0; text-align:center;">
                            Atau salin tautan berikut ke browser Anda:
                        </p>
                        <p style="font-size:12px; line-height:1.5; word-break:break-all; margin:0; text-align:center;">
                            <a href="{{ $detailUrl }}" style="color:#10b981; text-decoration:none;">{{ $detailUrl }}</a>
                        </p>
                        @endif
                    </td>
                </tr>

                {{-- FOOTER --}}
                <tr>
                    <td style="background:#f8fafc; padding:30px 35px; text-align:center; border-top:1px solid #e2e8f0;">
                        <p style="font-size:12px; color:#64748b; margin:0 0 6px 0;">
                            Email ini dikirim secara otomatis oleh sistem.
                        </p>
                        <p style="font-size:13px; font-weight:700; color:#334155; margin:0 0 15px 0;">
                            Sistem Informasi Personalia BENGPUSKOMLEKAD
                        </p>
                        <p style="font-size:11px; color:#94a3b8; margin:0;">
                            Mohon tidak membalas email ini (Do Not Reply).
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>