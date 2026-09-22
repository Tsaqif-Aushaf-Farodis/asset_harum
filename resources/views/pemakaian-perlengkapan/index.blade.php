<x-layout.app title="Pemakaian Perlengkapan" activeMenu="pemakaian-perlengkapan" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Pemakaian Perlengkapan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pemakaian Perlengkapan'],
        ]" />

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-start border-primary border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Jumlah Transaksi</h6>
                        <h3 class="mb-0">{{ number_format($ringkasan['jumlah_transaksi']) }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-start border-warning border-4">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Nilai Terpakai</h6>
                        <h3 class="mb-0">{{ \App\Helpers\Format::rupiah($ringkasan['nilai_terpakai']) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
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
                        <form method="GET" action="{{ route('pemakaian-perlengkapan.index') }}" class="row g-2">
                            <div class="col-md-2">
                                <input type="date" name="tanggal_mulai" class="form-control form-control-sm"
                                    value="{{ request('tanggal_mulai') }}" title="Dari tanggal">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="tanggal_akhir" class="form-control form-control-sm"
                                    value="{{ request('tanggal_akhir') }}" title="Sampai tanggal">
                            </div>
                            <div class="col-md-3">
                                <select name="barang_id" class="form-select form-select-sm">
                                    <option value="">Semua Barang</option>
                                    @foreach ($barangList as $id => $nama)
                                        <option value="{{ $id }}" @selected(request('barang_id') == $id)>{{ $nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="lokasi_id" class="form-select form-select-sm">
                                    <option value="">Semua Lokasi</option>
                                    @foreach ($lokasiList as $lokasi)
                                        <option value="{{ $lokasi->id }}" @selected(request('lokasi_id') == $lokasi->id)>
                                            {{ $lokasi->lokasi->nama_lokasi }} - {{ $lokasi->nama_sub_lokasi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <div class="input-group input-group-sm">
                                    <input type="text" name="pemakai" class="form-control" placeholder="Nama pemakai"
                                        value="{{ request('pemakai') }}">
                                    <button type="submit" class="btn btn-outline-secondary"><i class="bx bx-filter"></i></button>
                                    @if (request()->hasAny(['tanggal_mulai', 'tanggal_akhir', 'barang_id', 'lokasi_id', 'pemakai']))
                                        <a href="{{ route('pemakaian-perlengkapan.index') }}" class="btn btn-outline-danger"><i class="bx bx-x"></i></a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Barang</th>
                                <th class="text-end">Jumlah</th>
                                <th>Lokasi</th>
                                <th>Pemakai</th>
                                <th>Keperluan</th>
                                <th class="text-end">Nilai Terpakai</th>
                                <th>Dari Pengadaan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pemakaian as $row)
                                <tr>
                                    <td>{{ $loop->iteration + ($pemakaian->currentPage() - 1) * $pemakaian->perPage() }}</td>
                                    <td>{{ $row->tanggal_pemakaian->format('d/m/Y') }}</td>
                                    <td>{{ $row->pengadaan->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-end">{{ number_format($row->jumlah) }} {{ $row->pengadaan->satuan->nama_satuan ?? '' }}</td>
                                    <td>{{ $row->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $row->lokasi->nama_sub_lokasi ?? '-' }}</td>
                                    <td>{{ $row->pemakai }}</td>
                                    <td>{{ $row->keperluan }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($row->nilai_terpakai) }}</td>
                                    <td><small>{{ $row->pengadaan->kode_inventaris ?? '-' }}</small></td>
                                    <td class="text-center">
                                        @can('pemakaian delete')
                                            <form action="{{ route('pemakaian-perlengkapan.destroy', $row) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <x-input.confirm-button text="Pemakaian ini akan dibatalkan dan stok dikembalikan!"
                                                    positive="Ya, batalkan!" icon="info"
                                                    class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip"
                                                    data-bs-title="Batalkan pemakaian" data-bs-placement="top">
                                                    <span class="bx bx-undo"></span>
                                                </x-input.confirm-button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="text-center text-muted">Belum ada pemakaian.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    {!! $pemakaian->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
