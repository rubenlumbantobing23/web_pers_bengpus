<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Informasi Personalia Bengpuskomlekad')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --bg-dark: #0a0f1d;
            --bg-card: #131b2e;
            --bg-card-hover: #1c2742;
            --bg-sidebar: #090d18;
            --border-color: rgba(255, 255, 255, 0.08);
            --primary: #059669; /* Emerald 600 */
            --primary-hover: #047857;
            --primary-light: rgba(5, 150, 105, 0.15);
            --secondary: #d97706; /* Gold / Amber */
            --accent-gold: #f59e0b;
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-sub: #cbd5e1;
            --shadow-glow: 0 0 25px rgba(5, 150, 105, 0.25);
            --glass-bg: rgba(19, 27, 46, 0.75);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-dark);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.02em;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        /* Glass Cards */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* Buttons */
        .btn-military {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px 24px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.3);
            transition: all 0.25s ease;
        }

        .btn-military:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.5);
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
            color: #ffffff;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-sub);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: rgba(9, 13, 24, 0.7);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            color: var(--text-main);
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.2);
        }

        /* Fix autocomplete background for dark mode */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus, 
        .form-control:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #090d18 inset !important;
            -webkit-text-fill-color: var(--text-main) !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        /* Status Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .badge-pending {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-approved {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-rejected {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-cancelled {
            background: rgba(148, 163, 184, 0.15);
            color: #cbd5e1;
            border: 1px solid rgba(148, 163, 184, 0.3);
        }

        /* Alert notifications */
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #34d399;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #f87171;
        }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        .custom-table th {
            background: rgba(9, 13, 24, 0.8);
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 18px;
            border-bottom: 1px solid var(--border-color);
        }

        .custom-table td {
            padding: 16px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-sub);
            font-size: 0.925rem;
        }

        .custom-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.02);
        }

        /* Header / Emblem */
        .emblem-box {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .emblem-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.3rem;
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.4);
        }
    </style>

    @stack('styles')
</head>
<body>

    @yield('content')

    <script>
        // Auto-capitalize the first letter of every word for text inputs
        document.addEventListener('input', function (e) {
            if (e.target.tagName.toLowerCase() === 'input' && e.target.type === 'text') {
                // Ignore specific fields if needed
                if (e.target.id === 'email' || e.target.name === 'email') return;
                
                let start = e.target.selectionStart;
                let end = e.target.selectionEnd;
                
                let words = e.target.value.split(' ');
                for (let i = 0; i < words.length; i++) {
                    if (words[i].length > 0) {
                        words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
                    }
                }
                e.target.value = words.join(' ');
                
                e.target.setSelectionRange(start, end);
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
