<x-layout.app title="Permohonan" activeMenu="permohonan" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Permohonan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Permohonan'],
        ]" />

        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Permohonan</h6>
                                <h3 class="mb-0">{{ number_format($statistics['total']) }}</h3>
                            </div>
                            <div class="text-primary">
                                <i class="bx bx-file fs-1"></i>
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
                                <h6 class="text-muted mb-1">Pending</h6>
                                <h3 class="mb-0">{{ number_format($statistics['pending']) }}</h3>
                            </div>
                            <div class="text-warning">
                                <i class="bx bx-time fs-1"></i>
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
                                <h6 class="text-muted mb-1">Disetujui</h6>
                                <h3 class="mb-0">{{ number_format($statistics['approved']) }}</h3>
                            </div>
                            <div class="text-success">
                                <i class="bx bx-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-danger border-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Ditolak</h6>
                                <h3 class="mb-0">{{ number_format($statistics['rejected']) }}</h3>
                            </div>
                            <div class="text-danger">
                                <i class="bx bx-x-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Info Alert -->
        <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
            <i class="bx bx-info-circle me-2"></i>
            <strong>Informasi Alur Permohonan:</strong>
            <ul class="mb-0 mt-2">
                <li>Permohonan dibuat dengan status <span class="badge bg-warning">Pending</span></li>
                <li>Setelah permohonan <span class="badge bg-success">Disetujui</span>, barang akan tersedia untuk inventarisasi/pengadaan</li>
                <li>Jika permohonan <span class="badge bg-danger">Ditolak</span>, barang tidak dapat digunakan untuk inventarisasi</li>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    @can('permohonan create')
                        <div class="col-12 col-md-auto">
                            <a href="{{ route('permohonan.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Permohonan
                            </a>
                        </div>
                    @endcan
                    
                    <div class="col-12 col-md">
                        <form method="GET" action="{{ route('permohonan.index') }}" class="row g-2">
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="tahun_anggaran" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Tahun</option>
                                    @foreach($tahunAnggaranList as $tahun)
                                        <option value="{{ $tahun }}" {{ request('tahun_anggaran') == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control"
                                        placeholder="Cari bidang, unit kegiatan..."
                                        value="{{ request('search') }}"
                                    >
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="bx bx-search"></i>
                                    </button>
                                    @if(request()->hasAny(['search', 'status', 'tahun_anggaran']))
                                        <a href="{{ route('permohonan.index') }}" class="btn btn-outline-danger">
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
                <div id="permohonan-table">
                    @include('permohonan.includes.index-table', compact('permohonan'))
                </div>
            </div>
        </div>
    </div>
</x-layout.app>