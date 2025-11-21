<x-layout.app title="Laporan Nilai Aset" activeMenu="laporan.nilai-aset" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan Nilai Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Nilai Aset'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Aset</h6>
                        <h3 class="text-primary">{{ number_format($summary['total_aset'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Nilai Aset</h6>
                        <h3 class="text-success">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Nilai Aset</h5>
                <a href="{{ route('laporan.nilai-aset.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-5">
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
                    <div class="col-md-5">
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">Semua Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-2">
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
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Total Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalNilai = 0; @endphp
                            @foreach($inventaris as $index => $item)
                            @php 
                                $nilaiItem = ($item->jumlah ?? 1) * ($item->harga_satuan ?? 0);
                                $totalNilai += $nilaiItem;
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_inventaris }}</td>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $item->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                                <td>{{ $item->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->jumlah ?? 1, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($item->harga_satuan ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end"><strong>Rp {{ number_format($nilaiItem, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="7" class="text-end">TOTAL:</th>
                                <th class="text-end">Rp {{ number_format($totalNilai, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
