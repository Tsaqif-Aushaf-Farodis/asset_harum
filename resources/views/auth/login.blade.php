<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
    dir="ltr" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login — SIMASET | LPIT Harapan Ummat</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" class="template-customizer-core-css">
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/theme-default.css') }}"
        class="template-customizer-theme-css">
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}">

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}">

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --da-primary: #2c1f54;
            --da-primary-dark: #1c1438;
            --da-accent: #d4a017;
        }

        body {
            background: #f5f4fa;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            border-radius: 50%;
            z-index: 0;
            filter: blur(90px);
            opacity: .32;
        }

        body::before {
            width: 480px;
            height: 480px;
            background: #2c1f54;
            top: -160px;
            left: -160px;
        }

        body::after {
            width: 420px;
            height: 420px;
            background: #d4a017;
            bottom: -160px;
            right: -160px;
        }

        .auth-shell {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 16px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, .7);
            border-radius: 24px;
            box-shadow: 0 40px 80px -30px rgba(44, 31, 84, .35),
                0 12px 40px -15px rgba(0, 0, 0, .12);
            padding: 44px 40px;
        }

        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            margin-bottom: 26px;
        }

        .brand-logo-img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            filter: drop-shadow(0 10px 20px rgba(44, 31, 84, .35));
        }

        .brand-text .name {
            font-weight: 800;
            color: #1a1238;
            font-size: 1.05rem;
            line-height: 1.1;
        }

        .brand-text .sub {
            font-size: .76rem;
            color: var(--da-primary);
            font-weight: 600;
            margin-top: 3px;
        }

        .auth-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a1238;
            margin-bottom: 6px;
            text-align: center;
        }

        .auth-lead {
            color: #6b6388;
            font-size: .93rem;
            text-align: center;
            margin-bottom: 28px;
        }

        .form-floating-label {
            font-weight: 600;
            color: #3b3163;
            font-size: .85rem;
            margin-bottom: 6px;
        }

        .form-control,
        .input-group-text {
            border-radius: 12px !important;
            background: #fff;
            border-color: #e3dfee;
            padding: 12px 14px;
            font-size: .95rem;
        }

        .form-control:focus {
            border-color: var(--da-primary);
            box-shadow: 0 0 0 4px rgba(44, 31, 84, .12);
        }

        .input-group-merge .form-control {
            border-right: 0;
        }

        .input-group-merge .input-group-text {
            border-left: 0;
            background: #fff;
            color: #6b6388;
        }

        .input-group:focus-within .input-group-text {
            border-color: var(--da-primary);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--da-primary), var(--da-primary-dark));
            border: 0;
            color: #fff;
            padding: 13px 18px;
            font-weight: 700;
            border-radius: 12px;
            letter-spacing: .3px;
            transition: all .2s ease;
            box-shadow: 0 12px 24px -10px rgba(44, 31, 84, .55);
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px -12px rgba(44, 31, 84, .65);
            color: #fff;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .back-home {
            text-align: center;
            margin-top: 22px;
            font-size: .9rem;
        }

        .back-home a {
            color: var(--da-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .back-home a:hover {
            color: var(--da-primary-dark);
            text-decoration: underline;
        }

        .auth-footer {
            text-align: center;
            margin-top: 28px;
            color: #8a83a8;
            font-size: .82rem;
        }

        .auth-footer a {
            color: var(--da-primary);
            text-decoration: none;
            font-weight: 600;
        }

        .divider-soft {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #a098c0;
            font-size: .78rem;
            margin: 18px 0 20px;
        }

        .divider-soft::before,
        .divider-soft::after {
            content: "";
            flex: 1;
            height: 1px;
            background: #e3dfee;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
        }
    </style>

    @stack('css')
</head>

<body>
    <div class="auth-shell">
        <div class="auth-card">
            {{-- Brand --}}
            <a href="{{ url('/') }}" class="text-decoration-none">
                <div class="brand-row">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Darul Arqam" class="brand-logo-img">
                    <div class="brand-text">
                        <div class="name">SIMASET</div>
                        <div class="sub">LPIT Harapan Ummat</div>
                    </div>
                </div>
            </a>

            <h4 class="auth-title">Selamat Datang 👋</h4>
            <p class="auth-lead">
                Silakan masuk untuk mengelola aset LPIT Harapan Ummat.
            </p>

            <x-error-list />

            <form id="formAuthentication" action="{{ route('login') }}" method="post">
                @csrf

                <div class="mb-3">
                    <label for="username" class="form-floating-label">Username</label>
                    <div class="input-group input-group-merge">
                        <input type="text" class="form-control" id="username" name="username"
                            value="{{ old('username') }}" placeholder="Masukkan username Anda" autofocus />
                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                    </div>
                </div>

                <div class="mb-4 form-password-toggle">
                    <label for="password" class="form-floating-label">Password</label>
                    <div class="input-group input-group-merge">
                        <input type="password" id="password" class="form-control" name="password"
                            value="{{ old('password') }}" placeholder="••••••••" aria-describedby="password" />
                        <span class="cursor-pointer input-group-text"><i class="bx bx-hide"></i></span>
                    </div>
                </div>

                <button class="w-100 btn btn-login d-flex align-items-center justify-content-center gap-2"
                    type="submit">
                    <i class="bx bx-log-in-circle"></i>
                    Masuk Sistem
                </button>
            </form>

            <div class="back-home">
                <a href="{{ url('/') }}">
                    <i class="bx bx-arrow-back align-middle"></i> Kembali ke Beranda
                </a>
            </div>

            <div class="divider-soft">Sistem Manajemen Terpadu</div>

            <div class="auth-footer">
                © <script>
                    document.write(new Date().getFullYear());
                </script>
                LPIT Harapan Ummat · Dikembangkan oleh
                <a href="https://prabubimatech.com" target="_blank">PrabubimaTech</a>
            </div>
        </div>
    </div>

    <!-- Core JS -->
    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- Vendors JS -->
    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    @stack('script')
</body>

</html>
