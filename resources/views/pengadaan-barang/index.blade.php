@php
    $judul = match (true) {
        $perawatan => 'Peralatan yang Perlu Perawatan',
        $jenis === 'peralatan' => 'Daftar Aset (Peralatan) & Nilai Buku',
        $jenis === 'perlengkapan' => 'Pengadaan Perlengkapan',
        default => 'Pengadaan Barang',
    };
    $mode = $statistics['mode'];
@endphp
<x-layout.app :title="$judul" activeMenu="pengadaan-barang" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb :title="$judul" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => $judul],
        ]" />

        <!-- Statistik Cards -->
        <div class="row mb-4">
            @if ($mode === 'peralatan')
                <div class="col-xl-3 col-md-6">
                    <div class="card border-start border-primary border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Aset (Peralatan)</h6>
                                    <h3 class="mb-0">{{ number_format($statistics['total_aset']) }}</h3>
                                </div>
                                <div class="text-primary">
                                    <i class="bx bx-box fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-start border-success border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Aset Aktif</h6>
                                    <h3 class="mb-0">{{ number_format($statistics['total_aktif']) }}</h3>
                                </div>
                                <div class="text-success">
                                    <i class="bx bx-check-circle fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-start border-warning border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Sedang Dipinjam</h6>
                                    <h3 class="mb-0">{{ number_format($statistics['total_dipinjam']) }}</h3>
                                </div>
                                <div class="text-warning">
                                    <i class="bx bx-transfer fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card border-start border-info border-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Nilai Buku Saat Ini</h6>
                                    <h3 class="mb-0">{{ \App\Helpers\Format::rupiah($statistics['total_nilai_buku']) }}</h3>
                                    <small class="text-muted">Perolehan {{ \App\Helpers\Format::rupiah($statistics['total_nilai']) }}</small>
                                </div>
                                <div class="text-info">
                                    <i class="bx bx-money fs-1"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-primary border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Batch Stok Masuk</h6>
                            <h3 class="mb-0">{{ number_format($statistics['total_batch']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-success border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Nilai Persediaan (Sisa Stok)</h6>
                            <h3 class="mb-0">{{ \App\Helpers\Format::rupiah($statistics['nilai_persediaan']) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6">
                    <div class="card border-start border-warning border-4">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Nilai Terpakai</h6>
                            <h3 class="mb-0">{{ \App\Helpers\Format::rupiah($statistics['nilai_terpakai']) }}</h3>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ !$jenis ? 'active' : '' }}" href="{{ route('pengadaan-barang.index') }}">Semua</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $jenis === 'peralatan' && !$perawatan ? 'active' : '' }}"
                    href="{{ route('pengadaan-barang.index', ['jenis' => 'peralatan']) }}">Peralatan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $jenis === 'perlengkapan' ? 'active' : '' }}"
                    href="{{ route('pengadaan-barang.index', ['jenis' => 'perlengkapan']) }}">Perlengkapan</a>
            </li>
            @if ($perawatan)
                <li class="nav-item">
                    <a class="nav-link active" href="#">Perlu Perawatan</a>
                </li>
            @endif
        </ul>

        <div class="card">
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    @can('pengadaan-barang create')
                        <div class="col-auto">
                            <a href="{{ route('pengadaan-barang.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Pengadaan
                            </a>
                        </div>
                    @endcan

                    @can('pengadaan-barang import')
                        <div class="col-auto">
                            <a href="{{ route('pengadaan-barang.import') }}" class="btn btn-outline-primary">
                                <i class="bx bx-import me-1"></i>Import
                            </a>
                        </div>
                    @endcan

                    @if ($jenis !== 'perlengkapan')
                        @can('pengadaan-barang view')
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                    data-bs-target="#qrSelectModal">
                                    <i class="bx bx-qr-scan me-1"></i>Download QR Code
                                </button>
                            </div>
                        @endcan
                    @endif

                    <div class="col">
                        <form method="GET" action="{{ route($indexRoute) }}" class="row g-2">
                            @if ($jenis && $indexRoute === 'pengadaan-barang.index')
                                <input type="hidden" name="jenis" value="{{ $jenis }}">
                            @endif
                            <div class="col-md-3">
                                <select name="lokasi_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($lokasiList as $lokasi)
                                        <option value="{{ $lokasi->id }}" {{ request('lokasi_id') == $lokasi->id ? 'selected' : '' }}>
                                            {{ $lokasi->lokasi->nama_lokasi }} - {{ $lokasi->nama_sub_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="kategori_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoriList as $kategori)
                                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori_barang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Kondisi</option>
                                    @foreach($statusList as $status)
                                        <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                            {{ $status->nama_status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Tahun</option>
                                    @foreach($tahunList as $tahun)
                                        <option value="{{ $tahun }}" {{ request('tahun') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="input-group input-group-sm">
                                    <input
                                        type="text"
                                        name="search"
                                        class="form-control"
                                        placeholder="Cari kode inventaris, nama barang..."
                                        value="{{ request('search') }}"
                                    >
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="bx bx-search"></i>
                                    </button>
                                    @if(request()->hasAny(['search', 'lokasi_id', 'kategori_id', 'status_id', 'tahun']))
                                        <a href="{{ route($indexRoute, $jenis && $indexRoute === 'pengadaan-barang.index' ? ['jenis' => $jenis] : []) }}" class="btn btn-outline-danger">
                                            <i class="bx bx-x"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="pengadaan-barang-table">
                    @include('pengadaan-barang.includes.index-table', compact('pengadaanBarang'))
                </div>
            </div>
        </div>

        @if ($jenis !== 'perlengkapan')
            @can('pengadaan-barang view')
                @include('pengadaan-barang.includes.qr-select-modal', compact('allPengadaanBarangForQr'))
            @endcan
        @endif
    </div>
</x-layout.app>
