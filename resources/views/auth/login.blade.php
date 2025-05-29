<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
    dir="ltr" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login - {{ !empty($title) ? $title : config('app.name', 'Prabubima Tech') }}</title>

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
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('css')

</head>

<body>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Login -->
                <div class="px-0 card px-sm-6">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ url('/') }}" class="gap-2 app-brand-link">
                                <span class="app-brand-logo demo">
                                    <img src="{{ asset('assets/img/logo/pb.png') }}" alt="" height="50px">
                                </span>
                                <span class="app-brand-text demo text-heading fw-bold">Login
                                    {{ config('app.name') }}</span>
                            </a>
                        </div>

                        <!-- /Logo -->
                        <h4 class="mb-1">Selamat datang!</h4>
                        <p class="mb-6">Silahkan Login untuk melihat informasi lebih lanjut.</p>

                        <x-error-list />

                        <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="post">
                            @csrf
                            <div class="mb-6">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    value="{{ old('username') }}" placeholder="Masukkan username" autofocus />
                            </div>
                            <div class="mb-6 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        value="{{ old('password') }}"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="cursor-pointer input-group-text"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-8">
                                {{-- <div class="mt-8 d-flex justify-content-between"> --}}
                                {{-- <div class="mb-0 form-check ms-2">
                                        <input class="form-check-input" type="checkbox" id="remember-me" />
                                        <label class="form-check-label" for="remember-me"> Remember Me </label>
                                    </div> --}}
                                {{-- <a href="auth-forgot-password-basic.html">
                                        <span>Forgot Password?</span>
                                    </a> --}}
                                {{-- </div> --}}
                            </div>
                            <div class="mb-6">
                                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
                            </div>
                        </form>

                        {{-- <p class="text-center">
                            <span>User Baru?</span>
                            <a href="{{ url('/') }}">
                                <span>Daftar</span>
                            </a>
                        </p> --}}
                    </div>
                </div>
                <!-- /Register -->
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

    @stack('css')
</body>

</html>
