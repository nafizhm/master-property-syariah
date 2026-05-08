<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $logo = \App\Models\PengaturanMedia::where('jenis_data', 'logo website')->first();
        $konfigurasi = \App\Models\PengaturanProfil::first();
        $icon = \App\Models\PengaturanMedia::where('jenis_data', 'fav icon')->first();
        $faviconPath =
            $icon && $icon->nama_file ? asset('config_media/' . $icon->nama_file) : asset('default/favicon.ico');
        $logoPath =
            $logo && $logo->nama_file ? asset('config_media/' . $logo->nama_file) : asset('assets/img/iconLogin.svg');
        $companyName = $konfigurasi->nama_perusahaan ?? 'Template Aplikasi';
    @endphp

    <link rel="icon" href="{{ $faviconPath }}" type="image/x-icon" />
    <link rel="shortcut icon" href="{{ $faviconPath }}" type="image/x-icon" />
    <title>{{ $companyName }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Outfit:wght@300;400;500;600;700&display=swap"
        rel="stylesheet" />

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0d1117;
            --surface: #141a22;
            --card: #1a2230;
            --border: rgba(255, 255, 255, 0.07);
            --gold: #d4a853;
            --gold-dim: rgba(212, 168, 83, 0.15);
            --gold-glow: rgba(212, 168, 83, 0.08);
            --white: #f0eae0;
            --muted: rgba(240, 234, 224, 0.58);
            --hint: rgba(240, 234, 224, 0.25);
            --danger: #e05252;
            --success: #3dd68c;
            --radius: 12px;
        }

        body {
            min-height: 100vh;
            padding: 32px 16px;
            background:
                radial-gradient(circle at top, rgba(212, 168, 83, 0.08), transparent 34%),
                var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Outfit', sans-serif;
            position: relative;
            overflow-x: hidden;
            color: var(--white);
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(212, 168, 83, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(212, 168, 83, 0.04) 1px, transparent 1px);
            background-size: 56px 56px;
            pointer-events: none;
        }

        .bg-glow {
            position: fixed;
            width: 720px;
            height: 720px;
            border-radius: 50%;
            background: radial-gradient(circle, var(--gold-glow) 0%, transparent 65%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            filter: blur(6px);
        }

        .bg-city {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 200px;
            opacity: 0.04;
            pointer-events: none;
        }

        .top-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 34px;
            position: relative;
            z-index: 2;
            animation: fadeDown 0.5s ease both;
        }

        .logo-mark {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--gold-dim);
            border: 1px solid rgba(212, 168, 83, 0.32);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-mark img {
            width: 72%;
            height: 72%;
            object-fit: contain;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1;
        }

        .brand-name {
            font-family: 'Cormorant Garamond', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 0.03em;
        }

        .brand-tagline {
            font-size: 10px;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--gold);
            margin-top: 4px;
        }

        .card {
            position: relative;
            z-index: 2;
            width: min(430px, calc(100vw - 32px));
            background: rgba(26, 34, 48, 0.96);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 40px 40px 34px;
            box-shadow: 0 30px 70px rgba(0, 0, 0, 0.28);
            animation: fadeUp 0.55s cubic-bezier(.16, 1, .3, 1) 0.1s both;
            backdrop-filter: blur(8px);
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 90px;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            border-radius: 999px;
        }

        .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .card-header h1 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 32px;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 6px;
        }

        .card-header p {
            font-size: 13.5px;
            color: var(--muted);
        }

        .alert-box {
            display: none;
            align-items: flex-start;
            gap: 10px;
            padding: 12px 14px;
            border-radius: var(--radius);
            font-size: 13px;
            margin-bottom: 18px;
            line-height: 1.45;
        }

        .alert-box.show {
            display: flex;
        }

        .alert-box svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert-error {
            background: rgba(224, 82, 82, 0.12);
            border: 1px solid rgba(224, 82, 82, 0.28);
            color: #f3a6a6;
        }

        .alert-success {
            display: flex;
            background: rgba(61, 214, 140, 0.12);
            border: 1px solid rgba(61, 214, 140, 0.28);
            color: #9cebc1;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 11.5px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--hint);
            pointer-events: none;
            transition: color 0.2s ease;
            line-height: 0;
        }

        .input-icon svg {
            width: 16px;
            height: 16px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 44px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
            color: var(--white);
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        input::placeholder {
            color: var(--hint);
        }

        input:focus {
            border-color: rgba(212, 168, 83, 0.45);
            background: rgba(212, 168, 83, 0.04);
            box-shadow: 0 0 0 3px rgba(212, 168, 83, 0.08);
        }

        .input-wrap:focus-within .input-icon {
            color: var(--gold);
        }

        .show-pass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--hint);
            cursor: pointer;
            line-height: 0;
            padding: 4px;
            transition: color 0.2s ease;
        }

        .show-pass:hover {
            color: var(--gold);
        }

        .show-pass svg {
            width: 16px;
            height: 16px;
        }

        .is-invalid {
            border-color: rgba(224, 82, 82, 0.6) !important;
            box-shadow: 0 0 0 3px rgba(224, 82, 82, 0.08) !important;
        }

        .invalid-feedback {
            display: block;
            margin-top: 8px;
            color: #f3a6a6;
            font-size: 12.5px;
        }

        .form-note {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            font-size: 12.5px;
            color: var(--muted);
        }

        .form-note span:last-child {
            color: var(--gold);
        }

        .btn-submit {
            width: 100%;
            padding: 13.5px;
            background: var(--gold);
            color: #0d1117;
            border: none;
            border-radius: var(--radius);
            font-family: 'Outfit', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            letter-spacing: 0.04em;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: opacity 0.2s ease, transform 0.15s ease;
        }

        .btn-submit:hover:not(:disabled) {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: wait;
        }

        .button-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(13, 17, 23, 0.24);
            border-top-color: #0d1117;
            border-radius: 50%;
            display: none;
            animation: spin 0.7s linear infinite;
        }

        .button-spinner.show {
            display: inline-block;
        }

        .card-footer {
            margin-top: 26px;
            text-align: center;
            font-size: 12px;
            color: var(--hint);
            line-height: 1.5;
        }

        .status-bar {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 22px;
            animation: fadeDown 0.5s ease 0.3s both;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--success);
            animation: blink 2s ease-in-out infinite;
        }

        .status-text {
            font-size: 11.5px;
            color: rgba(240, 234, 224, 0.32);
            letter-spacing: 0.05em;
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 24px 14px;
            }

            .top-brand {
                margin-bottom: 26px;
            }

            .card {
                padding: 32px 22px 26px;
            }

            .card-header h1 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <div class="bg-grid"></div>
    <div class="bg-glow"></div>

    <svg class="bg-city" viewBox="0 0 1440 200" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg"
        fill="white">
        <rect x="0" y="100" width="60" height="100" />
        <rect x="10" y="60" width="40" height="40" />
        <rect x="20" y="40" width="20" height="20" />
        <rect x="70" y="80" width="80" height="120" />
        <rect x="90" y="50" width="40" height="30" />
        <rect x="160" y="110" width="50" height="90" />
        <rect x="220" y="70" width="100" height="130" />
        <rect x="250" y="40" width="40" height="30" />
        <rect x="330" y="90" width="60" height="110" />
        <rect x="340" y="60" width="40" height="30" />
        <rect x="400" y="50" width="120" height="150" />
        <rect x="430" y="20" width="60" height="30" />
        <rect x="450" y="5" width="20" height="15" />
        <rect x="530" y="80" width="70" height="120" />
        <rect x="610" y="60" width="90" height="140" />
        <rect x="630" y="30" width="50" height="30" />
        <rect x="710" y="90" width="60" height="110" />
        <rect x="780" y="40" width="110" height="160" />
        <rect x="810" y="10" width="50" height="30" />
        <rect x="825" y="0" width="20" height="10" />
        <rect x="900" y="70" width="80" height="130" />
        <rect x="990" y="85" width="60" height="115" />
        <rect x="1060" y="55" width="100" height="145" />
        <rect x="1090" y="25" width="40" height="30" />
        <rect x="1170" y="90" width="70" height="110" />
        <rect x="1250" y="65" width="90" height="135" />
        <rect x="1280" y="35" width="30" height="30" />
        <rect x="1350" y="80" width="90" height="120" />
    </svg>

    <div class="top-brand">
        <div class="logo-mark">
            <img src="{{ $logoPath }}" alt="Logo {{ $companyName }}">
        </div>
        <div class="brand-text">
            <span class="brand-name">{{ $companyName }}</span>
            <span class="brand-tagline">Sistem Properti & Penjualan</span>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h1>Masuk ke Akun</h1>
            <p>Selamat datang kembali, silakan masuk menggunakan akun Anda</p>
        </div>

        @if (session('success'))
            <div class="alert-box alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="alert-box alert-error{{ $errors->has('msg') ? ' show' : '' }}" id="alert-msg">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <circle cx="12" cy="16" r=".5" fill="currentColor" />
            </svg>
            <span id="alert-text">{{ $errors->first('msg') }}</span>
        </div>

        <form id="formLogin" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M20 21a8 8 0 0 0-16 0" />
                            <circle cx="12" cy="7" r="4" />
                        </svg>
                    </span>
                    <input type="text" id="username" name="username" placeholder="Masukkan username"
                        value="{{ old('username') }}" autocomplete="username" />
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="input-wrap">
                    <span class="input-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <rect x="3" y="11" width="18" height="10" rx="2" />
                            <path d="M7 11V8a5 5 0 0 1 10 0v3" />
                        </svg>
                    </span>
                    <input type="password" id="password" name="password" placeholder="Masukkan kata sandi"
                        autocomplete="current-password" />
                    <button type="button" class="show-pass" id="toggle-password" aria-label="Tampilkan kata sandi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-note">
                <span>Akses hanya untuk pengguna terdaftar</span>
                <span>Panel Admin</span>
            </div>

            <button type="submit" class="btn-submit" id="submitBtn">
                <span class="button-spinner" id="button-spinner"></span>
                <span class="button-text" id="button-text">Masuk</span>
            </button>
        </form>

        <div class="card-footer">
            &copy; {{ now()->year }} {{ $companyName }}<br>
            Sistem Pengelolaan Penjualan & Pembangunan Perumahan
        </div>
    </div>

    <div class="status-bar">
        <div class="status-dot"></div>
        <span class="status-text">Sistem berjalan normal</span>
    </div>

    <script>
        const loginForm = document.getElementById('formLogin');
        const usernameInput = document.getElementById('username');
        const passwordInput = document.getElementById('password');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('button-spinner');
        const buttonText = document.getElementById('button-text');
        const alertBox = document.getElementById('alert-msg');
        const alertText = document.getElementById('alert-text');
        const togglePasswordBtn = document.getElementById('toggle-password');
        const audio = new Audio('{{ asset('audio/notification.ogg') }}');

        togglePasswordBtn.addEventListener('click', function() {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            this.setAttribute('aria-label', showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
            this.innerHTML = showPassword ?
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>' :
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        });

        function clearValidationErrors() {
            document.querySelectorAll('.is-invalid').forEach((element) => {
                element.classList.remove('is-invalid');
            });

            document.querySelectorAll('.invalid-feedback').forEach((element) => {
                element.remove();
            });
        }

        function showAlert(message) {
            alertText.textContent = message;
            alertBox.classList.add('show');
        }

        function hideAlert() {
            alertText.textContent = '';
            alertBox.classList.remove('show');
        }

        function appendFieldError(field, message) {
            const input = document.getElementById(field);

            if (!input) {
                showAlert(message);
                return;
            }

            input.classList.add('is-invalid');

            const feedback = document.createElement('span');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;

            input.closest('.input-wrap').insertAdjacentElement('afterend', feedback);
        }

        async function refreshCsrfToken() {
            const response = await fetch('{{ route('refresh.csrf') }}', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal memperbarui token keamanan.');
            }

            const data = await response.json();
            document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
            return data.token;
        }

        loginForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearValidationErrors();
            hideAlert();

            submitBtn.disabled = true;
            spinner.classList.add('show');
            buttonText.textContent = 'Masuk...';

            try {
                const csrfToken = await refreshCsrfToken();
                const formData = new FormData(loginForm);

                const response = await fetch('{{ route('admin.loginPost') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.href = '{{ route('dashboard.index') }}';
                    return;
                }

                if (response.status === 422) {
                    const result = await response.json();
                    audio.play().catch(() => {});

                    if (result.errors) {
                        Object.entries(result.errors).forEach(([field, messages]) => {
                            appendFieldError(field, messages[0]);
                        });
                    }

                    if (!result.errors || Object.keys(result.errors).length === 0) {
                        showAlert(result.message || 'Username atau password tidak valid.');
                    }

                    return;
                }

                showAlert('Terjadi kesalahan pada server. Silakan coba lagi.');
            } catch (error) {
                showAlert(error.message || 'Terjadi kesalahan pada server. Silakan coba lagi.');
            } finally {
                submitBtn.disabled = false;
                spinner.classList.remove('show');
                buttonText.textContent = 'Masuk';
            }
        });
    </script>
</body>

</html>
