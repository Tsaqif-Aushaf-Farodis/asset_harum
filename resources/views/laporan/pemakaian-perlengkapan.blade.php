<x-layout.app title="Laporan Pemakaian Perlengkapan" activeMenu="laporan.pemakaian-perlengkapan" :withError="false">
    <div class="container-fluid my-5">
        <x-breadcrumb title="Laporan Pemakaian Perlengkapan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Pemakaian Perlengkapan'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Total Jumlah Dipakai</h6>
                    <h3 class="text-primary">{{ number_format($totalJumlah) }}</h3>
                </div></div>
            </div>
            <div class="col-md-6">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Total Nilai Terpakai</h6>
                    <h3 class="text-warning">Rp {{ number_format($totalNilai, 0, ',', '.') }}</h3>
                </div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Pemakaian Perlengkapan</h5>
                <a href="{{ route('laporan.pemakaian-perlengkapan.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-2">
                        <label class="form-label">Dari</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Barang</label>
                        <select name="barang_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($barangList as $id => $nama)
                                <option value="{{ $id }}" @selected(request('barang_id') == $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Lokasi</label>
                        <select name="lokasi_id" class="form-select">
                            <option value="">Semua</option>
                            @foreach($lokasi as $lok)
                                <option value="{{ $lok->id }}" @selected(request('lokasi_id') == $lok->id)>{{ $lok->nama_sub_lokasi }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Pemakai</label>
                        <input type="text" name="pemakai" class="form-control" value="{{ request('pemakai') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter"></i></button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Tanggal</th><th>Barang</th><th class="text-end">Jumlah</th>
                                <th>Lokasi</th><th>Pemakai</th><th>Keperluan</th><th class="text-end">Nilai Terpakai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pemakaian as $i => $row)
                                <tr>
                                    <td>{{ $pemakaian->firstItem() + $i }}</td>
                                    <td>{{ $row->tanggal_pemakaian->format('d/m/Y') }}</td>
                                    <td>{{ $row->pengadaan->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($row->jumlah) }} {{ $row->pengadaan->satuan->nama_satuan ?? '' }}</td>
                                    <td>{{ $row->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                    <td>{{ $row->pemakai }}</td>
                                    <td>{{ $row->keperluan }}</td>
                                    <td class="text-end">Rp {{ number_format($row->nilai_terpakai, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">{{ $pemakaian->links() }}</div>
            </div>
        </div>
    </div>
</x-layout.app>
