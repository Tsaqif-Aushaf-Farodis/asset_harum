<x-layout.app title="Dashboard" activeMenu="dashboard" :withError="true">
    <div class="container-fluid my-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Dashboard SIMASET</h3>
                <p class="text-muted mb-0">Sistem Informasi Manajemen Aset - LPIT Harapan Ummat</p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">{{ now()->isoFormat('dddd, D MMMM Y') }}</small>
                <small class="text-muted">Selamat datang, <strong>{{ Auth::user()->name }}</strong></small>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-primary border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Total Aset</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($totalAset) }}</h2>
                                <small class="text-success"><i class="bx bx-trending-up"></i> Unit</small>
                            </div>
                            <div class="text-primary opacity-75">
                                <i class="bx bx-package fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-success border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Nilai Aset</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($totalNilaiAset / 1000000, 1) }}M</h2>
                                <small class="text-success">Rp {{ number_format($totalNilaiAset, 0, ',', '.') }}</small>
                            </div>
                            <div class="text-success opacity-75">
                                <i class="bx bx-money fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-info border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Master Barang</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($totalBarang) }}</h2>
                                <small class="text-info"><i class="bx bx-check-circle"></i> Jenis</small>
                            </div>
                            <div class="text-info opacity-75">
                                <i class="bx bx-cube fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-start border-warning border-4 h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 small">Total Lokasi</h6>
                                <h2 class="mb-0 fw-bold">{{ number_format($totalLokasi) }}</h2>
                                <small class="text-warning"><i class="bx bx-map"></i> Lokasi</small>
                            </div>
                            <div class="text-warning opacity-75">
                                <i class="bx bx-buildings fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Statistik Permohonan -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bx bx-file-blank me-2"></i>Statistik Permohonan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <h3 class="mb-1">{{ $permohonanStats['total'] }}</h3>
                                    <small class="text-muted">Total</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-warning">{{ $permohonanStats['pending'] }}</h3>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-success">{{ $permohonanStats['approved'] }}</h3>
                                    <small class="text-muted">Disetujui</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-danger">{{ $permohonanStats['rejected'] }}</h3>
                                    <small class="text-muted">Ditolak</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Inventaris -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bx bx-box me-2"></i>Statistik Inventaris</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-primary">{{ $inventarisStats['total'] }}</h3>
                                    <small class="text-muted">Total Inventaris</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-success">{{ $inventarisStats['aktif'] }}</h3>
                                    <small class="text-muted">Aktif</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-warning">{{ $inventarisStats['dipinjam'] }}</h3>
                                    <small class="text-muted">Dipinjam</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                    <h3 class="mb-1 text-info">{{ $inventarisStats['tahun_ini'] }}</h3>
                                    <small class="text-muted">Tahun Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Status Barang -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bx bx-task me-2"></i>Jumlah Master Barang</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Pending</small>
                                <small class="fw-bold">{{ $barangStatusStats['pending'] }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-warning" role="progressbar" 
                                    style="width: {{ $totalBarang > 0 ? ($barangStatusStats['pending'] / $totalBarang * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>Approved</small>
                                <small class="fw-bold">{{ $barangStatusStats['approved'] }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ $totalBarang > 0 ? ($barangStatusStats['approved'] / $totalBarang * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between mb-1">
                                <small>Rejected</small>
                                <small class="fw-bold">{{ $barangStatusStats['rejected'] }}</small>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-danger" role="progressbar" 
                                    style="width: {{ $totalBarang > 0 ? ($barangStatusStats['rejected'] / $totalBarang * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Peminjaman & Mutasi -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bx bx-transfer me-2"></i>Peminjaman & Mutasi</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3 pb-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Peminjaman Aktif</span>
                                <span class="badge bg-warning">{{ $peminjamanStats['aktif'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Dikembalikan</span>
                                <span class="badge bg-success">{{ $peminjamanStats['dikembalikan'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Terlambat</span>
                                <span class="badge bg-danger">{{ $peminjamanStats['terlambat'] }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Total Mutasi</span>
                                <span class="badge bg-info">{{ $mutasiStats['total'] }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Mutasi Bulan Ini</span>
                                <span class="badge bg-primary">{{ $mutasiStats['bulan_ini'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opname -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bx bx-search-alt me-2"></i>Stock Opname</h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <h2 class="mb-0">{{ $opnameStats['total_sesi'] }}</h2>
                            <small class="text-muted">Total Sesi Opname</small>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small">Selesai</span>
                            <span class="badge bg-success">{{ $opnameStats['selesai'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small">Sedang Proses</span>
                            <span class="badge bg-warning">{{ $opnameStats['proses'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <!-- Top Kategori -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bx bx-bar-chart me-2"></i>Top 5 Kategori Aset</h6>
                    </div>
                    <div class="card-body">
                        @forelse($topKategori as $kategori)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">{{ $kategori->nama_kategori_barang }}</span>
                                <span class="small fw-bold">{{ $kategori->total }} unit</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-primary" role="progressbar" 
                                    style="width: {{ $totalAset > 0 ? ($kategori->total / $totalAset * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Top Lokasi -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bx bx-map-pin me-2"></i>Top 5 Lokasi Aset</h6>
                    </div>
                    <div class="card-body">
                        @forelse($topLokasi as $lokasi)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small">{{ $lokasi->nama_lokasi }}</span>
                                <span class="small fw-bold">{{ $lokasi->total }} unit</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                    style="width: {{ $totalAset > 0 ? ($lokasi->total / $totalAset * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center mb-0">Belum ada data</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Permohonan Terbaru -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-time-five me-2"></i>Permohonan Terbaru</h6>
                        <a href="{{ route('permohonan.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Bidang</th>
                                        <th>Unit</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permohonanTerbaru as $p)
                                    <tr>
                                        <td><small>{{ $p->bidang }}</small></td>
                                        <td><small>{{ $p->unit_kegiatan }}</small></td>
                                        <td>
                                            @if($p->status == 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($p->status == 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @else
                                                <span class="badge bg-danger">Rejected</span>
                                            @endif
                                        </td>
                                        <td><small>{{ $p->created_at->format('d/m/Y') }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aset Terbaru -->
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="bx bx-package me-2"></i>Aset Terbaru</h6>
                        <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Lokasi</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($asetTerbaru as $aset)
                                    <tr>
                                        <td><small class="font-monospace">{{ $aset->kode_inventaris }}</small></td>
                                        <td><small>{{ $aset->barang->nama_barang ?? '-' }}</small></td>
                                        <td><small>{{ $aset->subLokasi->lokasi->nama_lokasi ?? '-' }}</small></td>
                                        <td><small>{{ $aset->created_at->format('d/m/Y') }}</small></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada data</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
