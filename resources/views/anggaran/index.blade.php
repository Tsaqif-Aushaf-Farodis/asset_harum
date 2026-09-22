<x-layout.app title="Anggaran Tahunan" activeMenu="anggaran" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Anggaran Tahunan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Anggaran Tahunan'],
        ]" />

        @if ($tanpaAnggaran['jumlah'] > 0)
            <div class="alert alert-info">
                <i class="bx bx-info-circle me-1"></i>
                Ada <strong>{{ number_format($tanpaAnggaran['jumlah']) }}</strong> data pengadaan
                (nilai {{ \App\Helpers\Format::rupiah($tanpaAnggaran['nilai']) }}, tidak termasuk hibah)
                yang belum dikaitkan ke anggaran. Pilih anggarannya pada menu edit pengadaan.
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="row g-3 align-items-center">
                    @can('anggaran create')
                        <div class="col-auto">
                            <a href="{{ route('anggaran.create') }}" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i>Tambah Anggaran
                            </a>
                        </div>
                    @endcan
                    <div class="col">
                        <form method="GET" action="{{ route('anggaran.index') }}" class="row g-2 justify-content-end">
                            <div class="col-md-3">
                                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Tahun</option>
                                    @foreach ($tahunList as $tahun)
                                        <option value="{{ $tahun }}" @selected(request('tahun') == $tahun)>{{ $tahun }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="lokasi_id" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">Semua Lokasi</option>
                                    @foreach ($lokasiList as $id => $nama)
                                        <option value="{{ $id }}" @selected(request('lokasi_id') == $id)>{{ $nama }}</option>
                                    @endforeach
                                </select>
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
                                <th>Tahun</th>
                                <th>Lokasi (Unit)</th>
                                <th class="text-end">Pagu</th>
                                <th class="text-end">Realisasi</th>
                                <th class="text-end">Sisa</th>
                                <th style="min-width: 160px">% Realisasi</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($anggaran as $row)
                                @php
                                    $persen = $row->persen_realisasi;
                                    $warna = $persen > 100 ? 'bg-danger' : ($persen >= 80 ? 'bg-warning' : 'bg-success');
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration + ($anggaran->currentPage() - 1) * $anggaran->perPage() }}</td>
                                    <td>{{ $row->tahun }}</td>
                                    <td>{{ $row->lokasi->nama_lokasi ?? '-' }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($row->pagu) }}</td>
                                    <td class="text-end">{{ \App\Helpers\Format::rupiah($row->realisasi) }}</td>
                                    <td class="text-end {{ $row->sisa < 0 ? 'text-danger fw-bold' : '' }}">{{ \App\Helpers\Format::rupiah($row->sisa) }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px">
                                            <div class="progress-bar {{ $warna }}" role="progressbar"
                                                style="width: {{ min(100, $persen) }}%"></div>
                                        </div>
                                        <small>{{ number_format($persen, 1, ',', '.') }}%</small>
                                    </td>
                                    <td>
                                        @if ($row->is_active)
                                            <span class="badge bg-label-success">Aktif</span>
                                        @else
                                            <span class="badge bg-label-secondary">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            @can('anggaran edit')
                                                <div class="me-1">
                                                    <a href="{{ route('anggaran.edit', $row) }}"
                                                        class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                                        data-bs-title="Edit" data-bs-placement="top">
                                                        <span class="bx bx-pencil"></span>
                                                    </a>
                                                </div>
                                            @endcan
                                            @can('anggaran delete')
                                                <form action="{{ route('anggaran.destroy', $row) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-input.confirm-button text="Data anggaran ini akan dihapus!" positive="Ya, hapus!"
                                                        icon="info" class="btn btn-icon btn-outline-danger btn-sm"
                                                        data-bs-toggle="tooltip" data-bs-title="Hapus" data-bs-placement="top">
                                                        <span class="bx bx-trash"></span>
                                                    </x-input.confirm-button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Belum ada data anggaran.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    {!! $anggaran->withQueryString()->links() !!}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
