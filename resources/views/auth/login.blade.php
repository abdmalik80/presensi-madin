<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- ── Brand row ── --}}
    {{-- Mobile: centered logo + teks bawah | Desktop: logo + teks samping --}}
    <div style="margin-bottom:2rem; padding-bottom:1.5rem; border-bottom:1px solid #f3f4f6;">
        {{-- Mobile --}}
        <div class="flex flex-col items-center text-center sm:hidden">
            <img src="{{ asset('images/logo-nq.png') }}"
                 alt="Logo"
                 style="width:72px; height:72px; object-fit:contain; filter:drop-shadow(0 2px 8px rgba(0,0,0,0.4)); margin-bottom:0.85rem;">
            <p style="font-size:1.15rem; font-weight:800; color:#111827; margin:0; line-height:1.2;">Nawwir Quluubana</p>
            <p style="font-size:0.73rem; color:#6b7280; margin:0; margin-top:4px;">Sistem Presensi Madrasah Diniyah</p>
        </div>
        {{-- Desktop --}}
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
        {{-- Mobile --}}
        <div class="sm:hidden" style="text-align:left;">
            <h2 style="font-size:1.35rem; font-weight:800; color:#111827; margin:0 0 0.35rem; letter-spacing:-0.02em; line-height:1.25;">
                Selamat Datang
                <span style="display:inline-block; animation:wave 2.2s infinite; transform-origin:70% 70%;">👋</span>
            </h2>
            <p style="font-size:0.82rem; color:#6b7280; margin:0; line-height:1.6;">
                Masukkan kredensial Anda untuk melanjutkan.
            </p>
        </div>
        {{-- Desktop --}}
        <div class="hidden sm:block">
            <h2 style="font-size:1.6rem; font-weight:800; color:#111827; margin:0 0 0.4rem; letter-spacing:-0.03em; line-height:1.2;">
                Selamat Datang
                <span style="display:inline-block; animation:wave 2.2s infinite; transform-origin:70% 70%;">👋</span>
            </h2>
            <p style="font-size:0.875rem; color:#6b7280; margin:0; line-height:1.6;">
                Masukkan kredensial Anda untuk melanjutkan.
            </p>
        </div>
    </div>

    {{-- ── Form ── --}}
    <form method="POST" action="{{ route('login') }}" style="display:flex; flex-direction:column; gap:1.1rem;">
        @csrf

        {{-- Username --}}
        <div>
            <label for="username" style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.4rem; letter-spacing:0.01em;">
                USERNAME
            </label>
            <div style="position:relative;">
                <span style="position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;">
                    <i class="fas fa-user"></i>
                </span>
                <input id="username"
                       type="text"
                       name="username"
                       value="{{ old('username') }}"
                       required autofocus autocomplete="username"
                       placeholder="Masukkan username"
                       class="login-input">
            </div>
            <x-input-error :messages="$errors->get('username')" class="mt-1" style="color:#ef4444; font-size:0.75rem;" />
        </div>

        {{-- Password --}}
        <div>
            <label for="password" style="display:block; font-size:0.8rem; font-weight:600; color:#374151; margin-bottom:0.4rem; letter-spacing:0.01em;">
                PASSWORD
            </label>
            <div style="position:relative;">
                <span style="position:absolute; left:0.9rem; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:0.85rem; pointer-events:none;">
                    <i class="fas fa-lock"></i>
                </span>
                <input id="password"
                       type="password"
                       name="password"
                       required autocomplete="current-password"
                       placeholder="Masukkan password"
                       class="login-input"
                       style="padding-left:2.5rem; padding-right:2.5rem;">
                <button type="button" onclick="togglePassword()" style="position:absolute; right:0.9rem; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#9ca3af; padding:0; transition:color 0.2s;" onmouseover="this.style.color='#22C55E'" onmouseout="this.style.color='#9ca3af'">
                    <i id="eye-icon" class="fas fa-eye"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" style="color:#ef4444; font-size:0.75rem;" />
        </div>

        {{-- Remember & Forgot --}}
        <div style="display:flex; align-items:center; justify-content:space-between;">
            <label style="display:flex; align-items:center; gap:7px; cursor:pointer; font-size:0.82rem; color:#4b5563; user-select:none;">
                <input id="remember_me" name="remember" type="checkbox"
                       style="width:16px; height:16px; border-radius:4px; accent-color:#22C55E; cursor:pointer;">
                <span>Ingat saya</span>
            </label>

            <a href="{{ route('password.request') }}" title="Reset password Anda" style="font-size:0.82rem; color:#9ca3af; cursor:pointer; display:flex; align-items:center; gap:4px; text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#22C55E'" onmouseout="this.style.color='#9ca3af'">
                <i class="fas fa-circle-info" style="font-size:0.75rem;"></i>
                Lupa password?
            </a>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login">
            <i class="fas fa-arrow-right-to-bracket"></i>
            Masuk
        </button>
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
            margin-top: 0.3rem;
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #22C55E 0%, #14532D 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: 0.01em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            box-shadow: 0 4px 14px rgba(34, 197, 94, 0.35);
            transition: transform 0.2s, box-shadow 0.2s, filter 0.2s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
            filter: brightness(1.05);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 10px rgba(34, 197, 94, 0.3);
        }

        @keyframes wave {
            0%   { transform: rotate(0deg); }
            10%  { transform: rotate(14deg); }
            20%  { transform: rotate(-8deg); }
            30%  { transform: rotate(14deg); }
            40%  { transform: rotate(-4deg); }
            50%  { transform: rotate(10deg); }
            60%  { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }
    </style>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</x-guest-layout>
