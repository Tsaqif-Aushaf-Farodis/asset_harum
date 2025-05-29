<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light-style layout-menu-fixed layout-compact"
    dir="ltr" data-style="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title : config('app.name', 'Prabubima Tech') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}">

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
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/aos/aos.css') }}" />

    <style media="print">
        .noprint {
            display: none;
        }
    </style>

    @stack('style')

    <style>
        .main-container {
            position: relative;
            height: 100vh;
        }

        .main-bg {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* z-index: -1; */
        }

        h4.section-title {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 20px;
            text-transform: uppercase;
            position: relative;
        }

        h4.section-title:before {
            content: "";
            position: absolute;
            display: block;
            width: 160px;
            height: 2px;
            background: color-mix(in srgb, var(--bs-white), transparent 60%);
            left: 0;
            right: 0;
            bottom: 1px;
            margin: auto;
        }

        h4.section-title::after {
            content: "";
            position: absolute;
            display: block;
            width: 60px;
            height: 3px;
            background: var(--bs-primary);
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
        }
    </style>
</head>

<body>

    <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <!-- Toast default -->
            @if (isset($withError) ? $withError : false)
                <x-bs-toast />
            @endif

            <!-- Navbar -->
            {{-- <x-navbar-guest /> --}}

            <!-- Konten Utama -->
            <div class="layout-page">
                {{ $slot }}
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
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/aos/aos.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('assets/js/main.js') }}"></script>

    <script src="https://unpkg.com/htmx.org@2.0.4"
        integrity="sha384-HGfztofotfshcF7+8n44JQL2oJmowVChPTg48S+jvZoztPfvwD79OC/LTtG6dMp+" crossorigin="anonymous">
    </script>

    <!-- END: Theme JS-->
    @stack('script')

    @session('noback')
        <script type="text/javascript">
            window.history.pushState(null, null, window.location.href);
            window.onpopstate = function() {
                window.history.pushState(null, null, window.location.href);
            };
        </script>
    @endsession
</body>

</html>
