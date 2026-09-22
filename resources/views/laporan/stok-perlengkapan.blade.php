<x-layout.app title="Laporan Stok Perlengkapan" activeMenu="laporan.stok-perlengkapan" :withError="false">
    <div class="container-fluid my-5">
        <x-breadcrumb title="Laporan Stok Perlengkapan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Stok Perlengkapan'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Stok Perlengkapan</h5>
                <a href="{{ route('laporan.stok-perlengkapan.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Barang</label>
                        <select name="barang_id" class="form-select">
                            <option value="">Semua Perlengkapan</option>
                            @foreach($barangList as $id => $nama)
                                <option value="{{ $id }}" @selected(request('barang_id') == $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Lokasi Penyimpanan</label>
                        <select name="lokasi_id" class="form-select">
                            <option value="">Semua Lokasi</option>
                            @foreach($lokasi as $lok)
                                <option value="{{ $lok->id }}" @selected(request('lokasi_id') == $lok->id)>
                                    {{ $lok->lokasi->nama_lokasi }} - {{ $lok->nama_sub_lokasi }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="hanya_sisa" value="1" id="hanya_sisa" @checked(request('hanya_sisa'))>
                            <label class="form-check-label" for="hanya_sisa">Hanya yang ada sisa</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                    </div>
                </form>

                <h6 class="mb-3">Ringkasan per Barang</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Barang</th>
                                <th class="text-end">Masuk</th><th class="text-end">Terpakai</th>
                                <th class="text-end">Sisa</th><th class="text-end">Nilai Persediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ringkasan as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->barang->nama_barang }}</td>
                                    <td class="text-end">{{ number_format($r->masuk) }} {{ $r->satuan }}</td>
                                    <td class="text-end">{{ number_format($r->terpakai) }}</td>
                                    <td class="text-end"><strong>{{ number_format($r->sisa) }}</strong></td>
                                    <td class="text-end">Rp {{ number_format($r->nilai_persediaan, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-dark">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL NILAI PERSEDIAAN:</th>
                                <th class="text-end">Rp {{ number_format($ringkasan->sum('nilai_persediaan'), 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <h6 class="mb-3">Rincian per Batch Pengadaan</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th><th>Kode</th><th>Barang</th><th>Lokasi</th><th>Tgl Terima</th>
                                <th class="text-end">Masuk</th><th class="text-end">Terpakai</th><th class="text-end">Sisa</th>
                                <th class="text-end">Harga Satuan</th><th class="text-end">Nilai Persediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($batch as $b)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $b->kode_inventaris }}</td>
                                    <td>{{ $b->barang->nama_barang }}</td>
                                    <td>{{ $b->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                    <td>{{ \App\Helpers\Format::tglIndo($b->tanggal_pengadaan, 'd/m/Y') }}</td>
                                    <td class="text-end">{{ number_format($b->jumlah) }}</td>
                                    <td class="text-end">{{ number_format($b->stok_terpakai) }}</td>
                                    <td class="text-end"><strong>{{ number_format($b->stok_tersedia) }}</strong></td>
                                    <td class="text-end">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($b->nilaiSaatIni(), 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted">Tidak ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
