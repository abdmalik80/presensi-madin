<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Nawwir Quluubana') }} — Login</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background-color: #f0fdf4;
            color: #111827;
        }

        /* ── Page Shell ── */
        .page-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ── LEFT: form panel ── */
        .panel-left {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1.5rem;
            background: #ffffff;
            position: relative;
            overflow: hidden;
        }

        /* decorative top-right circle */
        .panel-left::before {
            content: '';
            position: absolute;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(34,197,94,0.1) 0%, transparent 70%);
            top: -80px;
            right: -80px;
            border-radius: 50%;
            pointer-events: none;
        }

        /* decorative bottom-left circle */
        .panel-left::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(20,83,45,0.07) 0%, transparent 70%);
            bottom: -60px;
            left: -60px;
            border-radius: 50%;
            pointer-events: none;
        }

        /* card that wraps the slot */
        .login-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 1.25rem;
            padding: 2.5rem 2rem;
            box-shadow:
                0 1px 3px rgba(0,0,0,0.05),
                0 10px 40px rgba(0,0,0,0.07);
            animation: cardIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
        }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(24px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* ── RIGHT: image panel ── */
        .panel-right {
            display: none;
            width: 50%;
            flex-shrink: 0;
            padding: 1.5rem;
            background: #f0fdf4;
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 1024px) {
            .panel-left { width: 50%; }
            .panel-right { display: flex; flex-direction: column; }
        }

        .image-frame {
            flex: 1;
            border-radius: 1.25rem;
            overflow: hidden;
            position: relative;
            background-image: url('{{ asset("/images/begron-login.png") }}');
            background-size: cover;
            background-position: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        }

        /* Dark overlay on image */
        .image-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to bottom,
                rgba(0,0,0,0.08) 0%,
                rgba(20,83,45,0.55) 100%
            );
            z-index: 1;
        }

        /* Caption block over image */
        .image-caption {
            position: absolute;
            bottom: 2.5rem;
            left: 2rem;
            right: 2rem;
            z-index: 2;
            color: #ffffff;
        }

        .image-caption .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 999px;
            padding: 0.3rem 0.9rem;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 0.9rem;
        }

        .image-caption h2 {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .image-caption p {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.8);
            line-height: 1.6;
        }
    </style>
</head>
<body class="overscroll-y-none">
    <div class="page-shell">

        {{-- LEFT PANEL --}}
        <div class="panel-left">
            <div class="login-card">
                {{ $slot }}
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="panel-right">
            <div class="image-frame">
                <div class="image-caption">
                    <div class="badge">
                        <i class="fas fa-mosque" style="font-size:0.7rem;"></i>
                        Madrasah Diniyah
                    </div>
                    <h2>PPTQ NAWWIR QULUUBANA</h2>
                    <p>Sistem informasi presensi santri yang modern, mudah, dan terpercaya.</p>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
