<x-layout.app title="Stok Perlengkapan" activeMenu="stok-perlengkapan" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Stok Perlengkapan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Stok Perlengkapan'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Jenis Perlengkapan</h6>
                        <h3 class="mb-0">{{ number_format($total['jenis_barang']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-success border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Sisa Stok</h6>
                        <h3 class="mb-0">{{ number_format($total['sisa']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-start border-info border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Nilai Persediaan</h6>
                        <h3 class="mb-0">{{ \App\Helpers\Format::rupiah($total['nilai_persediaan']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    @can('pemakaian create')
                        <div class="col-auto">
                            <a href="{{ route('pemakaian-perlengkapan.create') }}" class="btn btn-primary">
                                <i class="bx bx-minus-circle me-1"></i>Catat Pemakaian
                            </a>
                        </div>
                    @endcan
                    <div class="col">
                        <form method="GET" action="{{ route('stok-perlengkapan.index') }}" class="row g-2 justify-content-end">
                            <div class="col-md-4">
                                <select name="lokasi_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi Penyimpanan</option>
                                    @foreach ($lokasiList as $lokasi)
                                        <option value="{{ $lokasi->id }}" @selected(request('lokasi_id') == $lokasi->id)>
                                            {{ $lokasi->lokasi->nama_lokasi }} - {{ $lokasi->nama_sub_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="checkbox" name="hanya_sisa" value="1" id="hanya_sisa"
                                        @checked(request('hanya_sisa')) onchange="this.form.submit()">
                                    <label class="form-check-label" for="hanya_sisa">Hanya yang masih ada sisa</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Cari kode / nama barang..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="bx bx-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h6 class="mb-3">Ringkasan per Barang</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th class="text-end">Masuk</th>
                                <th class="text-end">Terpakai</th>
                                <th class="text-end">Sisa</th>
                                <th class="text-end">Nilai Persediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ringkasan as $r)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $r->barang->nama_barang }}</td>
                                    <td class="text-end">{{ number_format($r->masuk) }} {{ $r->satuan }}</td>
                                    <td class="text-end">{{ number_format($r->terpakai) }}</td>
                                    <td class="text-end {{ $r->sisa <= 0 ? 'text-danger' : 'fw-bold' }}">{{ number_format($r->sisa) }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($r->nilai_persediaan) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada stok perlengkapan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h5 class="mb-0">Rincian per Batch Pengadaan</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Inventaris</th>
                                <th>Barang</th>
                                <th>Lokasi Penyimpanan</th>
                                <th>Tanggal Terima</th>
                                <th class="text-end">Masuk</th>
                                <th class="text-end">Terpakai</th>
                                <th class="text-end">Sisa</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-end">Nilai Persediaan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($batch as $b)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td><a href="{{ route('pengadaan-barang.show', $b) }}">{{ $b->kode_inventaris }}</a></td>
                                    <td>{{ $b->barang->nama_barang }}</td>
                                    <td>{{ $b->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $b->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                    <td>{{ \App\Helpers\Format::tglIndo($b->tanggal_pengadaan, 'd/m/Y') }}</td>
                                    <td class="text-end">{{ number_format($b->jumlah) }} {{ $b->satuan->nama_satuan ?? '' }}</td>
                                    <td class="text-end">{{ number_format($b->stok_terpakai) }}</td>
                                    <td class="text-end {{ $b->stok_tersedia <= 0 ? 'text-danger' : 'fw-bold' }}">{{ number_format($b->stok_tersedia) }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($b->harga_satuan) }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($b->nilaiSaatIni()) }}</td>
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
