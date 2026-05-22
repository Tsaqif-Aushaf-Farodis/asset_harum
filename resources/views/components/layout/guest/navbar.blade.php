<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar"
    style="height: 5.5rem; border-radius: 18px; margin-top: 14px; z-index: 500;">

    <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
        {{-- Brand --}}
        <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none gap-2">
            <img src="{{ asset('images/logo_da.png') }}" alt="Logo Darul Arqam"
                style="width:52px;height:52px;object-fit:contain;">
            <div class="lh-1">
                <div class="fw-bold" style="font-size: 1.05rem; color:#2c1f54;">SIMADU Modul Aset</div>
                <small class="fw-medium d-none d-sm-block" style="font-size: .72rem; color:#2c1f54; opacity:.7;">
                    Ponpes Darul Arqam Garut
                </small>
            </div>
        </a>

        <ul class="flex-row navbar-nav align-items-center ms-auto">
            <li>
                <a href="{{ route('login') }}" class="btn"
                    style="background:#2c1f54;color:#fff;border-radius:50px;padding:9px 22px;font-weight:600;box-shadow:0 8px 18px -8px rgba(44,31,84,.55);">
                    <span class="tf-icons bx bx-log-in-circle me-md-1"></span>
                    <span>Login</span>
                </a>
            </li>
        </ul>
    </div>
</nav>
