<x-layout.app title="Laporan Inventaris" activeMenu="laporan.inventaris" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan Inventaris" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Inventaris'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Inventaris</h5>
                <a href="{{ route('laporan.inventaris.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Lokasi</label>
                        <select name="lokasi_id" class="form-select">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasi as $lok)
                                <option value="{{ $lok->id }}" {{ request('lokasi_id') == $lok->id ? 'selected' : '' }}>
                                    {{ $lok->nama_sub_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tahun Perolehan</label>
                        <input type="number" name="tahun_perolehan" class="form-control" 
                               value="{{ request('tahun_perolehan') }}" placeholder="2024">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter me-1"></i>Filter
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Jumlah</th>
                                {{-- <th>Satuan</th> --}}
                                <th>Harga Satuan</th>
                                <th>Total Nilai</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventaris as $index => $item)
                            <tr>
                                <td>{{ $inventaris->firstItem() + $index }}</td>
                                <td>{{ $item->kode_inventaris }}</td>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $item->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                                <td>{{ $item->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                <td>{{ $item->jumlah ?? 1 }}</td>
                                {{-- <td>{{ $item->satuan->nama_satuan ?? '-' }}</td> --}}
                                <td>Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format(($item->jumlah ?? 1) * ($item->harga_satuan ?? 0), 0, ',', '.') }}</td>
                                <td>{{ $item->statusKondisi->nama_status ?? '-' }}</td>
                                <td>
                                    @if($item->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $inventaris->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
