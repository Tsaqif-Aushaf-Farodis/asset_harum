<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
    style="height: 5.5rem;border-color: rgba(255,255,255,.68); background: rgba(255,255,255,.38); z-index: 500;">
    {{-- <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
        <a class="px-0 nav-item nav-link me-xl-6" href="javascript:void(0)">
            <i class="bx bx-menu bx-md"></i>
        </a>
    </div> --}}

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <img src="{{ asset('assets/img/logo/pb.png') }}" alt="" height="50px">
                <span class="mb-0 border-0 shadow-none form-control ps-1 ps-sm-2 fw-medium">
                    <span style="font-size: 1.3rem;" class="fw-bold">{{ config('app.name') }}</span>
                    <br>
                    <small class="text-primary d-none d-sm-block">{{ config('app.subname') }}</small>
                </span>
            </div>
        </div>
        <!-- /Search -->

        <ul class="flex-row navbar-nav align-items-center ms-auto">
            <!-- Place this tag where you want the button to render. -->
            <li class="nav-item me-4 d-none d-md-flex d-lg-flex">
                <a class="nav-link fw-medium collapsed" href="{{ url('/') }}">Home</a>
            </li>
            {{-- <li class="nav-item me-4 d-none d-md-flex d-lg-flex"
                <a class="nav-link fw-medium collapsed" role="button" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="hubungi-admin" href="{{ url('/') }}#hubungi-admin">Bantuan</a>
            </li>
            <li class="nav-item me-4 d-none d-md-flex d-lg-flex"
                <a class="nav-link fw-medium collapsed" role="button" data-bs-toggle="collapse" aria-expanded="false"
                    aria-controls="alur-pendaftaran" href="{{ url('/') }}#alur-pendaftaran">Alur Pendaftaran</a>
            </li> --}}
            <li>
                <a href="{{ route('login') }}" class="btn btn-primary" style="border-radius: 50px;">
                    <span class="tf-icons bx bx-log-in-circle scaleX-n1-rtl me-md-1"></span>
                    <span class="d-nonex d-md-block">Login</span></a>
            </li>
        </ul>
    </div>
</nav>
