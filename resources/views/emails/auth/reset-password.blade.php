<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Pers Bengpuskomlekad</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif; color:#1f2937;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f4f6f8; padding:30px 15px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:600px; background:#ffffff; border-radius:12px; overflow:hidden;">

                <!-- HEADER -->
                <tr>
                    <td style="background:#0f172a; padding:30px 35px; text-align:center;">

                        <div style="font-size:22px; font-weight:bold; color:#ffffff;">
                            PERS BENGPUSKOMLEKAD
                        </div>

                        <div style="font-size:13px; color:#94a3b8; margin-top:6px;">
                            SISTEM INFORMASI PERSONALIA
                        </div>

                    </td>
                </tr>

                <!-- CONTENT -->
                <tr>
                    <td style="padding:35px;">

                        <h2 style="margin:0 0 20px 0; color:#111827; font-size:24px;">
                            Reset Password
                        </h2>

                        <p style="font-size:15px; line-height:1.7; margin:0 0 15px 0;">
                            Halo, <strong>{{ $user->name }}</strong>.
                        </p>

                        <p style="font-size:15px; line-height:1.7; margin:0 0 15px 0;">
                            Kami menerima permintaan untuk mengatur ulang kata sandi
                            akun Anda pada
                            <strong>Sistem Informasi Personalia Bengpuskomlekad</strong>.
                        </p>

                        <p style="font-size:15px; line-height:1.7; margin:0 0 25px 0;">
                            Silakan klik tombol di bawah ini untuk membuat kata sandi baru.
                        </p>

                        <!-- BUTTON -->
                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center">

                                    <a href="{{ $resetUrl }}"
                                       style="
                                       display:inline-block;
                                       background:#10b981;
                                       color:#ffffff;
                                       text-decoration:none;
                                       font-weight:bold;
                                       padding:14px 28px;
                                       border-radius:8px;
                                       font-size:14px;
                                       ">
                                        RESET PASSWORD
                                    </a>

                                </td>
                            </tr>
                        </table>

                        <p style="font-size:14px; line-height:1.6; color:#6b7280; margin:25px 0 10px 0;">
                            Link reset password ini berlaku selama
                            <strong>{{ $expireMinutes }} menit</strong>.
                        </p>

                        <p style="font-size:14px; line-height:1.6; color:#6b7280; margin:0 0 20px 0;">
                            Jika Anda tidak merasa melakukan permintaan reset password,
                            Anda dapat mengabaikan email ini.
                            Tidak diperlukan tindakan lebih lanjut.
                        </p>

                        <hr style="border:none; border-top:1px solid #e5e7eb; margin:25px 0;">

                        <p style="font-size:12px; line-height:1.6; color:#9ca3af; margin:0;">
                            Jika tombol di atas tidak dapat digunakan, salin dan buka
                            link berikut pada browser Anda:
                        </p>

                        <p style="font-size:12px; line-height:1.6; word-break:break-all; margin:8px 0 0 0;">
                            <a href="{{ $resetUrl }}" style="color:#2563eb;">
                                {{ $resetUrl }}
                            </a>
                        </p>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f8fafc; padding:25px 35px; text-align:center;">

                        <p style="font-size:13px; color:#6b7280; margin:0 0 5px 0;">
                            Email ini dikirim secara otomatis oleh
                        </p>

                        <p style="font-size:13px; font-weight:bold; color:#374151; margin:0;">
                            Sistem Informasi Personalia Bengpuskomlekad
                        </p>

                        <p style="font-size:11px; color:#9ca3af; margin:12px 0 0 0;">
                            Mohon tidak membalas email ini.
                        </p>

                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>