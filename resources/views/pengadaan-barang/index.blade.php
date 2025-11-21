<x-layout.app title="Pengadaan Barang" activeMenu="pengadaan-barang" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang'],
        ]" />

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Aset</h6>
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
                                <h6 class="text-muted mb-1">Total Nilai Aset</h6>
                                <h3 class="mb-0">Rp {{ number_format($statistics['total_nilai'], 0, ',', '.') }}</h3>
                            </div>
                            <div class="text-info">
                                <i class="bx bx-money fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    @can('pengadaan-barang create')
                        <div class="col-auto">
                            <a href="{{ route('pengadaan-barang.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Aset
                            </a>
                        </div>
                    @endcan
                    
                    <div class="col">
                        <form method="GET" action="{{ route('pengadaan-barang.index') }}" class="row g-2">
                            <div class="col-md-2">
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
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
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
                            <div class="col-md-4">
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
                                        <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-outline-danger">
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
    </div>
</x-layout.app>