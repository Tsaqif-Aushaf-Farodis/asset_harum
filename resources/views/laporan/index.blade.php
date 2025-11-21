<x-layout.app title="Laporan" activeMenu="laporan.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan'],
        ]" />

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-package display-4 text-primary"></i>
                        <h5 class="card-title mt-3">Laporan Inventaris</h5>
                        <p class="card-text text-muted">Laporan daftar inventaris per lokasi dan kategori</p>
                        <a href="{{ route('laporan.inventaris') }}" class="btn btn-primary">
                            <i class="bx bx-file me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-transfer display-4 text-success"></i>
                        <h5 class="card-title mt-3">Laporan Mutasi</h5>
                        <p class="card-text text-muted">Laporan perpindahan dan mutasi aset</p>
                        <a href="{{ route('laporan.mutasi') }}" class="btn btn-success">
                            <i class="bx bx-file me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-check-circle display-4 text-info"></i>
                        <h5 class="card-title mt-3">Laporan Opname</h5>
                        <p class="card-text text-muted">Laporan hasil stock opname aset</p>
                        <a href="{{ route('laporan.opname') }}" class="btn btn-info">
                            <i class="bx bx-file me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-share display-4 text-warning"></i>
                        <h5 class="card-title mt-3">Laporan Peminjaman</h5>
                        <p class="card-text text-muted">Laporan peminjaman dan pengembalian aset</p>
                        <a href="{{ route('laporan.peminjaman') }}" class="btn btn-warning">
                            <i class="bx bx-file me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <i class="bx bx-money display-4 text-danger"></i>
                        <h5 class="card-title mt-3">Laporan Nilai Aset</h5>
                        <p class="card-text text-muted">Laporan nilai dan total aset per kategori</p>
                        <a href="{{ route('laporan.nilai-aset') }}" class="btn btn-danger">
                            <i class="bx bx-file me-1"></i>Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
