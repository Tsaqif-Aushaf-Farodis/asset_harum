<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme noprint" id="layout-navbar"
    style="z-index: 500;">
    <div class="container-xxl">

        {{-- <div class="navbar-brand menu-text fw-bold">
            <span>Sistem Pendaftaran Tryout </span>
        </div> --}}

        <!--  Brand demo (display only for navbar-full and hide on below xl) -->
        <div class="py-0 navbar-brand app-brand demo d-none d-xl-flex me-4 d-xl-none">
            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
            </a>
        </div>

        <!-- ! Not required for layout-without-menu -->
        <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
            <a class="px-0 nav-item nav-link me-xl-6" href="javascript:void(0)">
                <i class="bx bx-menu bx-md"></i>
            </a>
        </div>

        <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <ul class="flex-row navbar-nav align-items-center ms-auto">

                <!-- Style Switcher -->
                {{-- <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                        <i class="bx bx-md bx-sun"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                        <li>
                            <a class="dropdown-item active" href="javascript:void(0);" data-theme="light">
                                <span><i class="bx bx-sun bx-md me-3"></i>Light</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                <span><i class="bx bx-moon bx-md me-3"></i>Dark</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                <span><i class="bx bx-desktop bx-md me-3"></i>System</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}
                <!--/ Style Switcher -->

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="p-0 nav-link dropdown-toggle hide-arrow" href="javascript:void(0);"
                        data-bs-toggle="dropdown">
                        <div class="d-flex">
                            <div class="avatar avatar-online">
                                <img src="https://ui-avatars.com/api/?background=random&name={{ auth()->user()->name }}"
                                    alt="" class="h-auto w-px-40 rounded-circle">
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                <small class="text-muted">{{ '@' . auth()->user()->username }}</small>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <div class="dropdown-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="avatar avatar-online">
                                            <img src="https://ui-avatars.com/api/?background=random&name={{ auth()->user()->name }}"
                                                alt="" class="h-auto w-px-40 rounded-circle">
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ auth()->user()->name }}</h6>
                                        <small class="text-muted">{{ '@' . auth()->user()->username }}</small>
                                    </div>
                                </div>
                            </div>
                        </li>
                        {{-- tampilan role nya apa aja harusnay disini, pake badge --}}
                        <li>
                            @canany(['user profile', 'user change-password'])
                                <div class="my-1 dropdown-divider"></div>
                            @endcanany
                        </li>
                        <li>
                            @can('user profile')
                                <a class="dropdown-item" href="{{ route('profile') }}">
                                    <i class="menu-icon bx bx-user bx-md"></i><span>My Profile</span>
                                </a>
                            @endcan
                        </li>
                        <li>
                            @can('user change-password')
                                <a class="dropdown-item" href="{{ route('change-password') }}">
                                    <i class="menu-icon bx bx-user bx-md"></i><span>Change Password</span>
                                </a>
                            @endcan
                        </li>
                        <li>
                            @canany(['user profile', 'user change-password'])
                                <div class="my-1 dropdown-divider"></div>
                            @endcanany
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <x-input.confirm-button text="Anda akan keluar dari akun ini!" positive="Ya, keluar!"
                                    icon="info" class="dropdown-item">
                                    <i class="menu-icon bx bx-exit"></i><span>Logout</span>
                                </x-input.confirm-button>
                            </form>
                        </li>
                    </ul>
                </li>
                <!--/ User -->
            </ul>
        </div>

    </div>
</nav>
