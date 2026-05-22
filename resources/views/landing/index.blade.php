<x-layout.guest.app title="Beranda" activeMenu="landing" :withError="false">
    @push('style')
        <style>
            :root {
                --da-primary: #2c1f54;
                --da-primary-dark: #1c1438;
                --da-primary-soft: #efedf7;
                --da-accent: #d4a017;
                --da-accent-light: #f0c14b;
            }

            html,
            body {
                background: #f7f6fb;
                overflow-x: hidden;
            }

            /* Let layout grow with content (Vuexy default constrains height) */
            .layout-wrapper.layout-content-navbar,
            .layout-wrapper.layout-content-navbar .layout-container,
            .layout-wrapper.layout-content-navbar .layout-page {
                min-height: 100vh;
                height: auto;
                overflow: visible;
            }

            .main-container {
                position: relative;
                width: 100%;
                overflow: visible;
            }

            .main-bg {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 760px;
                object-fit: cover;
                opacity: .08;
                z-index: 0;
                pointer-events: none;
            }

            /* Glass navbar override */
            .layout-navbar {
                backdrop-filter: blur(14px);
                -webkit-backdrop-filter: blur(14px);
                background: rgba(255, 255, 255, .85) !important;
                border: 1px solid rgba(44, 31, 84, .08) !important;
                box-shadow: 0 8px 24px rgba(44, 31, 84, .07);
            }

            /* ===== HERO ===== */
            .hero {
                position: relative;
                z-index: 1;
                padding: 90px 0 80px;
            }

            .hero-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 16px;
                border-radius: 999px;
                background: rgba(44, 31, 84, .08);
                color: var(--da-primary);
                font-weight: 600;
                font-size: .8rem;
                letter-spacing: .4px;
                text-transform: uppercase;
                border: 1px solid rgba(44, 31, 84, .15);
            }

            .hero-title {
                font-size: clamp(2rem, 4vw, 3.4rem);
                font-weight: 800;
                line-height: 1.15;
                color: #1a1238;
                margin: 22px 0 18px;
            }

            .hero-title .accent {
                background: linear-gradient(120deg, var(--da-primary), var(--da-accent));
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
            }

            .hero-sub {
                font-size: 1.05rem;
                color: #5a527a;
                max-width: 560px;
                line-height: 1.7;
                margin-bottom: 28px;
            }

            .hero-cta .btn {
                padding: 12px 24px;
                font-weight: 600;
                border-radius: 50px;
            }

            .btn-da-primary {
                background: var(--da-primary);
                border-color: var(--da-primary);
                color: #fff;
                box-shadow: 0 12px 24px -10px rgba(44, 31, 84, .55);
            }

            .btn-da-primary:hover {
                background: var(--da-primary-dark);
                border-color: var(--da-primary-dark);
                color: #fff;
            }

            .btn-da-outline {
                border: 1.5px solid var(--da-primary);
                color: var(--da-primary);
                background: transparent;
            }

            .btn-da-outline:hover {
                background: var(--da-primary);
                color: #fff;
            }

            .hero-stats {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 18px;
                margin-top: 44px;
                max-width: 520px;
            }

            .hero-stat .num {
                font-size: 1.8rem;
                font-weight: 800;
                color: var(--da-primary);
                line-height: 1;
            }

            .hero-stat .lbl {
                font-size: .82rem;
                color: #7c7596;
                margin-top: 6px;
            }

            .hero-visual {
                position: relative;
                aspect-ratio: 1 / 1;
                max-width: 480px;
                margin: 0 auto;
            }

            .hero-logo-wrap {
                position: absolute;
                inset: 12% 12%;
                display: grid;
                place-items: center;
                background: radial-gradient(circle at 30% 30%,
                        rgba(44, 31, 84, .12),
                        rgba(44, 31, 84, .04) 60%,
                        transparent);
                border-radius: 50%;
            }

            .hero-logo-wrap img {
                width: 78%;
                height: auto;
                filter: drop-shadow(0 25px 40px rgba(44, 31, 84, .35));
                animation: float 6s ease-in-out infinite;
            }

            @keyframes float {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-12px);
                }
            }

            .hero-card {
                position: absolute;
                background: #fff;
                border-radius: 18px;
                box-shadow: 0 25px 50px -20px rgba(44, 31, 84, .3);
                padding: 14px 18px;
                display: flex;
                align-items: center;
                gap: 12px;
                border: 1px solid rgba(44, 31, 84, .06);
                z-index: 2;
            }

            .hero-card .ic {
                width: 42px;
                height: 42px;
                border-radius: 12px;
                display: grid;
                place-items: center;
                color: #fff;
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .hero-card .lbl {
                font-size: .68rem;
                color: #8c84a8;
                text-transform: uppercase;
                font-weight: 600;
                letter-spacing: .5px;
            }

            .hero-card .val {
                font-weight: 700;
                color: #1a1238;
                font-size: .95rem;
            }

            .hero-card.c1 {
                top: 6%;
                left: -2%;
            }

            .hero-card.c1 .ic {
                background: linear-gradient(135deg, #2c1f54, #4a3782);
            }

            .hero-card.c2 {
                top: 44%;
                right: -8%;
            }

            .hero-card.c2 .ic {
                background: linear-gradient(135deg, #d4a017, #f0c14b);
            }

            .hero-card.c3 {
                bottom: 6%;
                left: 4%;
            }

            .hero-card.c3 .ic {
                background: linear-gradient(135deg, #3b88c4, #6fb1e3);
            }

            /* ===== SECTION ===== */
            section.block {
                position: relative;
                z-index: 1;
                padding: 80px 0;
                background: transparent;
            }

            section.block.alt {
                background: #fff;
            }

            .section-eyebrow {
                color: var(--da-primary);
                font-weight: 700;
                text-transform: uppercase;
                font-size: .8rem;
                letter-spacing: 2px;
                margin-bottom: 10px;
            }

            .section-heading {
                font-size: clamp(1.6rem, 2.6vw, 2.2rem);
                font-weight: 800;
                color: #1a1238;
                margin-bottom: 14px;
            }

            .section-lead {
                color: #6b6388;
                max-width: 640px;
                margin: 0 auto;
                line-height: 1.7;
            }

            /* ===== FEATURE GRID ===== */
            .feature-card {
                background: #fff;
                border: 1px solid #ebe7f3;
                border-radius: 18px;
                padding: 26px 22px;
                height: 100%;
                transition: all .25s ease;
                position: relative;
                overflow: hidden;
            }

            .feature-card:hover {
                transform: translateY(-6px);
                border-color: rgba(44, 31, 84, .35);
                box-shadow: 0 20px 40px -20px rgba(44, 31, 84, .25);
            }

            .feature-card .ic {
                width: 52px;
                height: 52px;
                border-radius: 14px;
                display: grid;
                place-items: center;
                font-size: 1.5rem;
                background: rgba(44, 31, 84, .1);
                color: var(--da-primary);
                margin-bottom: 16px;
            }

            .feature-card h5 {
                font-weight: 700;
                color: #1a1238;
                margin-bottom: 8px;
            }

            .feature-card p {
                color: #75709a;
                font-size: .93rem;
                line-height: 1.6;
                margin: 0;
            }

            /* ===== ABOUT ===== */
            .about-wrapper {
                background: linear-gradient(135deg, var(--da-primary), var(--da-primary-dark));
                border-radius: 30px;
                padding: 60px 50px;
                color: #fff;
                position: relative;
                overflow: hidden;
            }

            .about-wrapper::before {
                content: "";
                position: absolute;
                width: 380px;
                height: 380px;
                background: rgba(212, 160, 23, .18);
                border-radius: 50%;
                right: -120px;
                top: -120px;
            }

            .about-wrapper::after {
                content: "";
                position: absolute;
                width: 280px;
                height: 280px;
                background: rgba(255, 255, 255, .06);
                border-radius: 50%;
                left: -80px;
                bottom: -80px;
            }

            .about-wrapper .content {
                position: relative;
                z-index: 1;
            }

            .about-wrapper h3 {
                font-weight: 800;
                font-size: 2rem;
                margin-bottom: 16px;
            }

            .about-wrapper p {
                color: rgba(255, 255, 255, .85);
                line-height: 1.8;
                font-size: 1rem;
            }

            .about-list li {
                display: flex;
                gap: 12px;
                margin-bottom: 12px;
                color: rgba(255, 255, 255, .92);
            }

            .about-list li i {
                color: var(--da-accent-light);
                font-size: 1.2rem;
                flex-shrink: 0;
            }

            .about-logo {
                max-width: 280px;
                filter: drop-shadow(0 20px 30px rgba(0, 0, 0, .35));
            }

            /* ===== CTA ===== */
            .cta-band {
                background: #fff;
                border: 1px solid #ebe7f3;
                border-radius: 24px;
                padding: 50px;
                text-align: center;
                box-shadow: 0 30px 60px -30px rgba(44, 31, 84, .18);
            }

            .cta-band h3 {
                font-weight: 800;
                font-size: 1.8rem;
                color: #1a1238;
                margin-bottom: 12px;
            }

            .cta-band p {
                color: #6b6388;
                margin-bottom: 24px;
            }

            /* ===== FOOTER ===== */
            .site-footer {
                background: #1a1238;
                color: rgba(255, 255, 255, .75);
                padding: 50px 0 24px;
                margin-top: 40px;
                position: relative;
                z-index: 1;
            }

            .site-footer h6 {
                color: #fff;
                font-weight: 700;
                margin-bottom: 16px;
                font-size: .9rem;
                text-transform: uppercase;
                letter-spacing: .5px;
            }

            .site-footer a {
                color: rgba(255, 255, 255, .7);
                text-decoration: none;
            }

            .site-footer a:hover {
                color: var(--da-accent-light);
            }

            .footer-bottom {
                border-top: 1px solid rgba(255, 255, 255, .08);
                padding-top: 18px;
                margin-top: 30px;
                font-size: .85rem;
                color: rgba(255, 255, 255, .55);
            }

            .footer-brand img {
                width: 56px;
                height: 56px;
                object-fit: contain;
                flex-shrink: 0;
            }

            @media (max-width: 768px) {
                .hero {
                    padding: 60px 0 40px;
                }

                .hero-visual {
                    margin-top: 40px;
                    max-width: 320px;
                }

                .about-wrapper {
                    padding: 40px 24px;
                }

                .cta-band {
                    padding: 36px 24px;
                }
            }
        </style>
    @endpush

    <div class="main-container">
        <img src="{{ asset('assets/img/front-pages/hero-bg.png') }}" class="main-bg" alt="">

        <x-layout.guest.navbar />

        {{-- HERO --}}
        <section class="hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="hero-badge">
                            <i class="bx bxs-shield-alt-2"></i>
                            Sistem Manajemen Terpadu
                        </span>
                        <h1 class="hero-title">
                            Kelola Aset Pesantren dengan <span class="accent">Cerdas &amp; Terpadu</span>
                        </h1>
                        <p class="hero-sub">
                            <strong>SIMADU Modul Aset</strong> adalah Sistem Manajemen Terpadu Pondok Pesantren
                            Darul Arqam Muhammadiyah Daerah Garut untuk pencatatan, pelacakan, dan pengawasan
                            seluruh aset pesantren — mulai dari tanah, bangunan, kendaraan, hingga barang
                            inventaris santri dan asatidz.
                        </p>
                        <div class="hero-cta d-flex flex-wrap gap-3">
                            <a href="{{ route('login') }}" class="btn btn-da-primary">
                                <i class="bx bx-log-in-circle me-1"></i> Masuk Sistem
                            </a>
                            <a href="#fitur" class="btn btn-da-outline">
                                Pelajari Fitur <i class="bx bx-right-arrow-alt ms-1"></i>
                            </a>
                        </div>

                        <!-- <div class="hero-stats">
                            <div class="hero-stat">
                                <div class="num">10+</div>
                                <div class="lbl">Modul Aset</div>
                            </div>
                            <div class="hero-stat">
                                <div class="num">24/7</div>
                                <div class="lbl">Akses Realtime</div>
                            </div>
                            <div class="hero-stat">
                                <div class="num">100%</div>
                                <div class="lbl">Terdokumentasi</div>
                            </div>
                        </div> -->
                    </div>

                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="hero-visual">
                            <div class="hero-logo-wrap">
                                <img src="{{ asset('images/logo_da.png') }}" alt="Logo Ponpes Darul Arqam Garut">
                            </div>
                            <div class="hero-card c1">
                                <div class="ic"><i class="bx bxs-building-house"></i></div>
                                <div>
                                    <div class="lbl">Bangunan</div>
                                    <div class="val">Asrama &amp; Madrasah</div>
                                </div>
                            </div>
                            <div class="hero-card c2">
                                <div class="ic"><i class="bx bxs-car"></i></div>
                                <div>
                                    <div class="lbl">Kendaraan</div>
                                    <div class="val">Operasional</div>
                                </div>
                            </div>
                            <div class="hero-card c3">
                                <div class="ic"><i class="bx bxs-box"></i></div>
                                <div>
                                    <div class="lbl">Inventaris</div>
                                    <div class="val">Barang &amp; Mutasi</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- FITUR --}}
        <section class="block alt" id="fitur">
            <div class="container">
                <div class="mb-5 text-center">
                    <div class="section-eyebrow">Fitur Utama</div>
                    <h2 class="section-heading">Modul Lengkap untuk Pengelolaan Aset Pesantren</h2>
                    <p class="section-lead">
                        Dirancang khusus untuk kebutuhan Pondok Pesantren Darul Arqam Muhammadiyah Daerah Garut
                        agar pengelolaan aset menjadi tertib, akuntabel, dan mudah dipertanggungjawabkan.
                    </p>
                </div>

                <div class="row g-4">
                    @php
                        $features = [
                            [
                                'icon' => 'bxs-map',
                                'title' => 'Manajemen Tanah',
                                'desc' => 'Pencatatan sertifikat, luas, lokasi, dan status kepemilikan tanah wakaf maupun milik pesantren.',
                            ],
                            [
                                'icon' => 'bxs-building-house',
                                'title' => 'Manajemen Bangunan',
                                'desc' => 'Data asrama santri, ruang kelas, masjid, kantor, hingga sarana penunjang lainnya dalam satu sistem.',
                            ],
                            [
                                'icon' => 'bxs-car',
                                'title' => 'Manajemen Kendaraan',
                                'desc' => 'Inventaris kendaraan operasional, STNK, jadwal servis, dan penggunaan harian pesantren.',
                            ],
                            [
                                'icon' => 'bxs-box',
                                'title' => 'Master Barang & Inventaris',
                                'desc' => 'Katalog seluruh barang pesantren dengan kategori, satuan, lokasi, dan status kondisi yang jelas.',
                            ],
                            [
                                'icon' => 'bxs-cart-add',
                                'title' => 'Pengadaan',
                                'desc' => 'Proses pengadaan tanah, bangunan, kendaraan, dan barang yang tercatat rapi dengan jejak audit.',
                            ],
                            [
                                'icon' => 'bxs-transfer-alt',
                                'title' => 'Mutasi Aset',
                                'desc' => 'Catat perpindahan aset antar lokasi atau antar penanggung jawab dengan riwayat lengkap.',
                            ],
                            [
                                'icon' => 'bxs-check-shield',
                                'title' => 'Stock Opname',
                                'desc' => 'Verifikasi fisik aset secara berkala untuk memastikan data dan kondisi selalu akurat.',
                            ],
                            [
                                'icon' => 'bxs-bookmark-alt',
                                'title' => 'Peminjaman & Permohonan',
                                'desc' => 'Alur permohonan dan peminjaman aset oleh asatidz/santri dengan persetujuan berjenjang.',
                            ],
                            [
                                'icon' => 'bxs-report',
                                'title' => 'Laporan & Cetak',
                                'desc' => 'Laporan aset, mutasi, opname hingga rekap pengadaan siap cetak untuk pelaporan yayasan.',
                            ],
                        ];
                    @endphp

                    @foreach ($features as $f)
                        <div class="col-md-6 col-lg-4">
                            <div class="feature-card">
                                <div class="ic"><i class="bx {{ $f['icon'] }}"></i></div>
                                <h5>{{ $f['title'] }}</h5>
                                <p>{{ $f['desc'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- TENTANG --}}
        <section class="block" id="tentang">
            <div class="container">
                <div class="about-wrapper">
                    <div class="content">
                        <div class="row align-items-center g-5">
                            <div class="col-lg-7">
                                <div class="section-eyebrow" style="color: var(--da-accent-light);">Tentang
                                    Pesantren</div>
                                <h3 style="color: white;">Pondok Pesantren Darul Arqam Muhammadiyah Daerah Garut</h3>
                                <p>
                                    Pondok Pesantren Darul Arqam Muhammadiyah Daerah Garut merupakan lembaga
                                    pendidikan Islam yang berkomitmen mencetak generasi qur'ani, berakhlak
                                    mulia, dan berwawasan luas. Pengelolaan aset yang amanah dan transparan
                                    adalah bagian dari ikhtiar menjaga kepercayaan umat dan keberlanjutan
                                    dakwah pendidikan.
                                </p>
                                <!-- <ul class="about-list list-unstyled mt-4 mb-0">
                                    <li><i class="bx bxs-check-circle"></i> Transparansi pengelolaan aset wakaf
                                        dan inventaris pesantren.</li>
                                    <li><i class="bx bxs-check-circle"></i> Tata kelola yang akuntabel kepada
                                        yayasan, pengurus, dan jamaah.</li>
                                    <li><i class="bx bxs-check-circle"></i> Mendukung operasional asrama,
                                        madrasah, dan dakwah secara terpadu.</li>
                                </ul> -->
                            </div>
                            <div class="col-lg-5 text-center d-none d-lg-block">
                                <div style="display: flex; justify-content: center; align-items: center; width: 300px; height: 300px; background-color: #f5f5f5; border-radius: 50%; margin: 0 auto;">
                                    <img src="{{ asset('images/logo_da.png') }}"
                                        alt="Logo Ponpes Darul Arqam Garut" class="about-logo" style="width: 70%; height: auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- CTA --}}
        <section class="block alt" id="hubungi-admin">
            <div class="container">
                <div class="cta-band">
                    <h3>Siap Mengelola Aset Pesantren Lebih Tertib?</h3>
                    <p>Masuk ke SIMADU Modul Aset dan mulai catat, pantau, serta kelola aset pesantren
                        dengan satu sistem yang terpadu.</p>
                    <a href="{{ route('login') }}" class="btn btn-da-primary">
                        <i class="bx bx-log-in-circle me-1"></i> Masuk ke Sistem
                    </a>
                </div>
            </div>
        </section>

        {{-- FOOTER --}}
        <footer class="site-footer">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <div class="footer-brand d-flex gap-3 align-items-center mb-3">
                            <img src="{{ asset('images/logo_da.png') }}" alt="Logo Darul Arqam">
                            <div>
                                <div class="text-white fw-bold" style="font-size: 1.1rem;">SIMADU Modul Aset</div>
                                <small style="color: rgba(255,255,255,.6);">Ponpes Darul Arqam Garut</small>
                            </div>
                        </div>
                        <p style="color: rgba(255,255,255,.65); line-height: 1.7;">
                            Sistem Manajemen Terpadu untuk pencatatan dan pengelolaan aset Pondok Pesantren
                            Darul Arqam Muhammadiyah Daerah Garut secara akuntabel, transparan, dan modern.
                        </p>
                    </div>
                    <div class="col-6 col-lg-3">
                        <h6>Tautan</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2">
                            <li><a href="{{ url('/') }}">Beranda</a></li>
                            <li><a href="{{ route('login') }}">Login</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-lg-4">
                        <h6>Kontak Pesantren</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2"
                            style="color: rgba(255,255,255,.7); font-size: .92rem;">
                            <li><i class="bx bx-map me-2"></i> Garut, Jawa Barat, Indonesia</li>
                            <li><i class="bx bx-envelope me-2"></i> admin@darularqamgarut.sch.id</li>
                            <li><i class="bx bx-time me-2"></i> Senin – Jumat, 08.00 – 16.00 WIB</li>
                        </ul>
                    </div>
                </div>
                <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
                    <div>
                        © <script>
                            document.write(new Date().getFullYear());
                        </script> SIMADU Modul Aset — Ponpes Darul Arqam Garut.
                    </div>
                    <div>
                        Dikembangkan oleh
                        <a href="https://prabubimatech.com" target="_blank">Team PrabubimaTech</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</x-layout.guest.app>
