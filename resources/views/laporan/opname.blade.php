<x-layout.app title="Laporan Opname" activeMenu="laporan.opname" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan Opname" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Opname'],
        ]" />

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Laporan Opname</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir') }}">
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
                                <th>Kode Opname</th>
                                <th>Nama Opname</th>
                                <th>Lokasi</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Status</th>
                                <th>Total Aset</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opname as $index => $item)
                            <tr>
                                <td>{{ $opname->firstItem() + $index }}</td>
                                <td>{{ $item->id }}</td>
                                <td>{{ $item->nama_opname }}</td>
                                <td>{{ $item->lokasi->nama_sub_lokasi ?? 'Semua Lokasi' }}</td>
                                <td>{{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($item->status == 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($item->status == 'ongoing')
                                        <span class="badge bg-primary">Ongoing</span>
                                    @else
                                        <span class="badge bg-success">Completed</span>
                                    @endif
                                </td>
                                <td>{{ $item->details->count() }}</td>
                                <td>
                                    <a href="{{ route('opname.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show me-1"></i>Detail
                                    </a>
                                    @if($item->status == 'completed')
                                        <a href="{{ route('opname.export-laporan', $item) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bx bx-download me-1"></i>Export
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $opname->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
