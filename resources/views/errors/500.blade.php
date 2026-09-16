<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Bengpuskomlekad PERS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #0a0f1d;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background-image: radial-gradient(circle at 50% 30%, rgba(239, 68, 68, 0.1), transparent 60%);
        }

        h1 { font-family: 'Outfit', sans-serif; }

        .code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444, #f87171);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            margin-top: 24px;
        }
    </style>
</head>
<body>
    <div>
        <div class="code">500</div>
        <h1 style="font-size: 1.8rem; margin-bottom: 12px;">Terjadi Kesalahan Server</h1>
        <p style="color: #94a3b8; font-size: 1rem; max-width: 420px; line-height: 1.6; margin: 0 auto;">
            Sistem mengalami gangguan teknis. Tim pengelola telah diberitahu. Silakan coba beberapa saat lagi.
        </p>
        <a href="{{ url('/') }}" class="btn">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
