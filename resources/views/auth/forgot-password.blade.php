<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- ── Brand row ── --}}
    <div style="margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid #f3f4f6;">
        <div class="flex flex-col items-center text-center sm:hidden">
            <img src="{{ asset('images/logo-nq.png') }}"
                 alt="Logo"
                 style="width:72px; height:72px; object-fit:contain; filter:drop-shadow(0 2px 8px rgba(0,0,0,0.4)); margin-bottom:0.85rem;">
            <p style="font-size:1.15rem; font-weight:800; color:#111827; margin:0; line-height:1.2;">Nawwir Quluubana</p>
            <p style="font-size:0.73rem; color:#6b7280; margin:0; margin-top:4px;">Sistem Presensi Madrasah Diniyah</p>
        </div>
        <div class="hidden sm:flex items-center" style="gap:0.75rem;">
            <img src="{{ asset('images/logo-nq.png') }}"
                 alt="Logo"
                 style="width:44px; height:44px; object-fit:contain; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.45)); flex-shrink:0;">
            <div>
                <p style="font-size:0.95rem; font-weight:700; color:#111827; margin:0; line-height:1.2;">Nawwir Quluubana</p>
                <p style="font-size:0.72rem; color:#6b7280; margin:0; margin-top:1px;">Sistem Presensi Madrasah Diniyah</p>
            </div>
        </div>
    </div>

    {{-- ── Greeting ── --}}
    <div style="margin-bottom:2rem;">
        <div class="sm:hidden" style="text-align:left;">
            <h2 style="font-size:1.35rem; font-weight:800; color:#111827; margin:0 0 0.35rem; letter-spacing:-0.02em; line-height:1.25;">
                Lupa Password 🔐
            </h2>
            <p style="font-size:0.82rem; color:#6b7280; margin:0; line-height:1.6;">
                Masukkan email Anda, dan kami akan mengirimkan tautan untuk membuat password baru.
            </p>
        </div>
        <div class="hidden sm:block">
            <h2 style="font-size:1.6rem; font-weight:800; color:#111827; margin:0 0 0.4rem; letter-spacing:-0.03em; line-height:1.2;">
                Lupa Password 🔐
            </h2>
            <p style="font-size:0.875rem; color:#6b7280; margin:0; line-height:1.6;">
                Masukkan email Anda, dan kami akan mengirimkan tautan untuk membuat password baru.
            </p>
        </div>
    </div>

    {{-- ── Form ── --}}
    <form method="POST" action="{{ route('password.email') }}" style="display:flex; flex-direction:column; gap:1.1rem;">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.4rem; letter-spacing:0.01em;">
                EMAIL
            </label>
            <div style="position:relative;">
                <span style="position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;">
                    <i class="fas fa-envelope"></i>
                </span>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required autofocus autocomplete="username"
                       placeholder="Masukkan email Anda"
                       class="login-input">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" style="color:#ef4444; font-size:0.75rem;" />
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login" style="margin-top: 0.5rem;">
            <i class="fas fa-paper-plane"></i>
            Kirim Link Reset Password
        </button>
        
        <div style="text-align:center; margin-top:0.5rem;">
            <a href="{{ route('login') }}" style="font-size:0.85rem; color:#6b7280; text-decoration:none; font-weight:600; transition:color 0.2s;" onmouseover="this.style.color='#22C55E'" onmouseout="this.style.color='#6b7280'">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
    </form>

    {{-- ── Divider & info ── --}}
    <div style="margin-top:1.75rem; padding-top:1.5rem; border-top:1px solid #f3f4f6; text-align:center;">
        <p style="font-size:0.75rem; color:#9ca3af;">
            &copy; {{ date('Y') }} Nawwir Quluubana — All rights reserved.
        </p>
    </div>

    {{-- ── Styles ── --}}
    <style>
        .login-input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            background: #f9fafb;
            font-size: 0.9rem;
            color: #111827;
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s, background 0.25s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .login-input::placeholder { color: #c4c8d1; }

        .login-input:focus {
            border-color: #22C55E;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12);
        }

        .btn-login {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.15s, box-shadow 0.15s, background 0.25s;
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.25);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(22, 163, 74, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(22, 163, 74, 0.25);
        }
    </style>
</x-guest-layout>
