<x-layout.app title="Laporan Peminjaman" activeMenu="laporan.peminjaman" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan Peminjaman" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Peminjaman'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Peminjaman Aset</h5>
                <a href="{{ route('laporan.peminjaman.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status_peminjaman" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status_peminjaman') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status_peminjaman') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="borrowed" {{ request('status_peminjaman') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                            <option value="returned" {{ request('status_peminjaman') == 'returned' ? 'selected' : '' }}>Dikembalikan</option>
                            <option value="overdue" {{ request('status_peminjaman') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
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
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Tgl Kembali</th>
                                <th>Status</th>
                                <th>Kondisi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $index => $item)
                            <tr>
                                <td>{{ $peminjaman->firstItem() + $index }}</td>
                                <td>{{ $item->pengadaan->kode_inventaris ?? '-' }}</td>
                                <td>{{ $item->pengadaan->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $item->peminjam_nama }}</td>
                                <td>{{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_rencana_kembali ? \Carbon\Carbon::parse($item->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if($item->status_peminjaman == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($item->status_peminjaman == 'borrowed')
                                        <span class="badge bg-primary">Dipinjam</span>
                                    @elseif($item->status_peminjaman == 'returned')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($item->status_peminjaman == 'overdue')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($item->status_peminjaman) }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->kondisi_kembali ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $peminjaman->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
