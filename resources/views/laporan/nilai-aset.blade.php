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
                        <h6 class="text-muted">Jumlah Data</h6>
                        <h3 class="text-primary">{{ number_format($summary['total_aset'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Nilai Perolehan</h6>
                        <h3 class="text-secondary">Rp {{ number_format($summary['total_perolehan'], 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="text-muted">Total Nilai per {{ $asOf->format('d/m/Y') }}</h6>
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
                    <div class="col-md-2">
                        <label class="form-label">Jenis</label>
                        <select name="jenis" class="form-select">
                            <option value="">Semua</option>
                            <option value="peralatan" @selected(request('jenis') === 'peralatan')>Peralatan</option>
                            <option value="perlengkapan" @selected(request('jenis') === 'perlengkapan')>Perlengkapan</option>
                        </select>
                    </div>
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
                        <label class="form-label">Kategori</label>
                        <select name="kategori_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" @selected(request('kategori_id') == $kat->id)>{{ $kat->nama_kategori_barang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Per Tanggal</label>
                        <input type="date" name="per_tanggal" class="form-control" value="{{ request('per_tanggal', $asOf->toDateString()) }}">
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
                                <th>Jenis</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th class="text-end">Jumlah</th>
                                <th class="text-end">Nilai Perolehan</th>
                                <th class="text-end">Penyusutan / Terpakai</th>
                                <th class="text-end">Nilai Saat Ini</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventaris as $index => $row)
                            @php $item = $row->pengadaan; @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode_inventaris }}</td>
                                <td>{{ $item->barang->nama_barang }}</td>
                                <td>{{ $row->jenis }}</td>
                                <td>{{ $item->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                                <td>{{ $item->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                <td class="text-end">{{ number_format($item->jumlah ?? 1, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($row->nilai_perolehan, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($row->pengurang, 0, ',', '.') }}</td>
                                <td class="text-end"><strong>Rp {{ number_format($row->nilai_saat_ini, 0, ',', '.') }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="7" class="text-end">TOTAL:</th>
                                <th class="text-end">Rp {{ number_format($summary['total_perolehan'], 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format($summary['total_perolehan'] - $summary['total_nilai'], 0, ',', '.') }}</th>
                                <th class="text-end">Rp {{ number_format($summary['total_nilai'], 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <small class="text-muted">
                    Peralatan: nilai buku (harga perolehan dikurangi penyusutan). Perlengkapan: nilai persediaan (sisa stok × harga);
                    perlengkapan yang sudah dipakai bernilai 0.
                </small>
            </div>
        </div>
    </div>
</x-layout.app>
