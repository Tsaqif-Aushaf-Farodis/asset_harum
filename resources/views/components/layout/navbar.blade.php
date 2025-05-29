<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme noprint" style="z-index: 501;">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link">
            <span class="app-brand-logo demo">
            </span>
            <div class="app-brand-text demo menu-text ms-2" style="font-size: 20px;">
                <span class="fw-bold" style="line-height: 110%;">{{ config('app.name') }}</span>
                <br>
                <small class="fw-semibold d-none d-sm-block">{{ config('app.subname') }}</small>
            </div>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">
        {{-- Dashboard Selalu Tampil --}}
        <li class="menu-item {{ request()->route()->getName() == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
            </a>
        </li>

        @php
            $navConfig = config('navigation', []);
            $currentGroup = '';
        @endphp

        @foreach ($navConfig as $group => $menus)
            @if ($group != $currentGroup)
                <li class="menu-header small text-uppercase">
                    <span class="menu-header-text">{{ $group }}</span>
                </li>
            @endif

            @foreach ($menus as $menu)
                @if (isset($menu['submenus']))
                    @php
                        $submenuPermission = collect($menu['submenus'])
                            ->pluck('permissions')
                            ->flatten()
                            ->toArray();
                    @endphp
                    @if ($submenuPermission || is_null($submenuPermission))
                        <li
                            class="menu-item {{ in_array(
                                request()->route()->getName(),
                                collect($menu['submenus'])->pluck('route')->toArray(),
                            )
                                ? 'active open'
                                : '' }}">
                            <a href="javascript:void(0);" class="menu-link menu-toggle">
                                {!! $menu['icon'] !!}
                                <div class="text-truncate" data-i18n="{{ $menu['title'] }}">{{ $menu['title'] }}</div>
                            </a>
                            <ul class="menu-sub">
                                @foreach ($menu['submenus'] as $submenu)
                                    {{-- @can($submenu['permissions'][0]) --}}
                                    {{-- @endcan --}}
                                    @if (auth()->user()->canAny($submenu['permissions']) || is_null($submenu['permissions']))
                                        <li
                                            class="menu-item {{ request()->route()->getName() == $submenu['route'] ? 'active' : '' }}">
                                            <a href="{{ !$submenu['route'] ? '#' : route($submenu['route']) }}"
                                                class="menu-link">
                                                <div class="text-truncate" data-i18n="{{ $submenu['title'] }}">
                                                    {{ $submenu['title'] }}
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </li>
                    @endif
                @else
                    @if (auth()->user()->canAny($menu['permissions']) || is_null($menu['permissions']))
                        <li class="menu-item {{ request()->route()->getName() == $menu['route'] ? 'active' : '' }}">
                            <a href="{{ !$menu['route'] ? '#' : route($menu['route']) }}" class="menu-link">
                                {!! $menu['icon'] !!}
                                <div class="text-truncate" data-i18n="{{ $menu['title'] }}">{{ $menu['title'] }}</div>
                            </a>
                        </li>
                    @endif
                @endif
            @endforeach
        @endforeach

        {{-- Logout --}}
        <li class="menu-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <x-input.confirm-button text="Anda akan keluar dari akun ini!" positive="Ya, keluar!" icon="info"
                    class="text-white menu-link bg-dark">
                    <i class="menu-icon tf-icons bx bx-exit"></i>
                    <div class="text-truncate" data-i18n="Logout">Logout</div>
                </x-input.confirm-button>
            </form>
        </li>
    </ul>

</aside>
