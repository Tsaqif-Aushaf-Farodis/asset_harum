<x-layout.app title="Laporan Penyusutan" activeMenu="laporan.penyusutan" :withError="false">
    <div class="container-fluid my-5">
        <x-breadcrumb title="Laporan Penyusutan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Penyusutan'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Jumlah Aset</h6>
                    <h3 class="text-primary">{{ number_format($summary['jumlah']) }}</h3>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Nilai Perolehan</h6>
                    <h4 class="text-secondary">Rp {{ number_format($summary['nilai_perolehan'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Akumulasi Penyusutan</h6>
                    <h4 class="text-danger">Rp {{ number_format($summary['akumulasi'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
            <div class="col-md-3">
                <div class="card"><div class="card-body text-center">
                    <h6 class="text-muted">Nilai Buku per {{ $asOf->format('d/m/Y') }}</h6>
                    <h4 class="text-success">Rp {{ number_format($summary['nilai_buku'], 0, ',', '.') }}</h4>
                </div></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Penyusutan Peralatan</h5>
                <a href="{{ route('laporan.penyusutan.export', request()->query()) }}" class="btn btn-success">
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
                                <option value="{{ $lok->id }}" @selected(request('lokasi_id') == $lok->id)>{{ $lok->nama_sub_lokasi }}</option>
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
                        <label class="form-label d-block">&nbsp;</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="semua" value="1" id="semua" @checked(request('semua'))>
                            <label class="form-check-label" for="semua">Termasuk yang tidak disusutkan</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100"><i class="bx bx-filter me-1"></i>Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Lokasi</th>
                                <th>Tgl Perolehan</th>
                                <th class="text-end">Nilai Perolehan</th>
                                <th>Aturan</th>
                                <th class="text-end">Akumulasi</th>
                                <th class="text-end">Nilai Buku</th>
                                <th>Turun Berikutnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $i => $row)
                                @php $p = $row->pengadaan; $s = $row->susut; @endphp
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $p->kode_inventaris }}</td>
                                    <td>{{ $p->barang->nama_barang }}</td>
                                    <td>{{ $p->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                    <td>{{ \App\Helpers\Format::tglIndo($p->tanggal_pengadaan, 'd/m/Y') }}</td>
                                    <td class="text-end">Rp {{ number_format($s['nilai_perolehan'], 0, ',', '.') }}</td>
                                    <td>
                                        @if ($s['disusutkan'])
                                            Turun Rp {{ number_format($s['penyusutan_per_langkah'], 0, ',', '.') }} tiap {{ $s['interval_tahun'] }} th
                                            <small class="text-muted">({{ $s['langkah_berjalan'] }}/{{ $s['jumlah_langkah'] }})</small>
                                        @else
                                            <span class="text-muted">Tidak disusutkan</span>
                                        @endif
                                    </td>
                                    <td class="text-end">Rp {{ number_format($s['akumulasi'], 0, ',', '.') }}</td>
                                    <td class="text-end"><strong>Rp {{ number_format($s['nilai_buku'], 0, ',', '.') }}</strong></td>
                                    <td>{{ $s['tanggal_penyusutan_berikutnya']?->format('d/m/Y') ?? ($s['disusutkan'] ? 'Habis' : '-') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted">Tidak ada aset yang disusutkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
