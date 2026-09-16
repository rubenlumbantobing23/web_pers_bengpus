<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | Bengpuskomlekad PERS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Outfit:wght@700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0a0f1d;
            --primary: #059669;
            --text-muted: #94a3b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body {
            background: var(--bg-dark);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            background-image: radial-gradient(circle at 30% 30%, rgba(5, 150, 105, 0.12), transparent 50%), radial-gradient(circle at 70% 70%, rgba(217, 119, 6, 0.08), transparent 50%);
        }

        h1 { font-family: 'Outfit', sans-serif; }

        .code {
            font-size: 8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #059669, #34d399);
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
            box-shadow: 0 4px 20px rgba(5, 150, 105, 0.4);
            margin-top: 24px;
            transition: all 0.2s ease;
        }

        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(5, 150, 105, 0.5); }
    </style>
</head>
<body>
    <div>
        <div class="code">404</div>
        <h1 style="font-size: 1.8rem; margin-bottom: 12px;">Halaman Tidak Ditemukan</h1>
        <p style="color: var(--text-muted); font-size: 1rem; max-width: 400px; line-height: 1.6;">
            Halaman yang Anda cari tidak tersedia atau sudah dipindahkan. Silakan kembali ke beranda sistem.
        </p>
        @auth
            @if(Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="btn">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Admin Dashboard
                </a>
            @else
                <a href="{{ route('user.dashboard') }}" class="btn">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            @endif
        @else
            <a href="{{ route('login') }}" class="btn">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Login
            </a>
        @endauth
    </div>
</body>
</html>
