<x-layout.app title="Laporan Mutasi" activeMenu="laporan.mutasi" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Laporan Mutasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Laporan', 'url' => route('laporan.index')],
            ['label' => 'Mutasi'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Laporan Mutasi Aset</h5>
                <a href="{{ route('laporan.mutasi.export', request()->query()) }}" class="btn btn-success">
                    <i class="bx bx-download me-1"></i>Export Excel
                </a>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Jenis Mutasi</label>
                        <select name="jenis_mutasi" class="form-select">
                            <option value="">Semua Jenis</option>
                            <option value="pindah_lokasi" {{ request('jenis_mutasi') == 'pindah_lokasi' ? 'selected' : '' }}>Pindah Lokasi</option>
                            <option value="ubah_pengguna" {{ request('jenis_mutasi') == 'ubah_pengguna' ? 'selected' : '' }}>Ubah Pengguna</option>
                            <option value="non_aktif" {{ request('jenis_mutasi') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                            <option value="barang_keluar" {{ request('jenis_mutasi') == 'barang_keluar' ? 'selected' : '' }}>Barang Keluar</option>
                            <option value="penghapusan" {{ request('jenis_mutasi') == 'penghapusan' ? 'selected' : '' }}>Penghapusan</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-filter"></i>
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jenis Mutasi</th>
                                <th>Detail</th>
                                <th>Alasan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mutasi as $index => $item)
                            <tr>
                                <td>{{ $mutasi->firstItem() + $index }}</td>
                                <td>{{ $item->tanggal_mutasi ? \Carbon\Carbon::parse($item->tanggal_mutasi)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->pengadaan->kode_inventaris ?? '-' }}</td>
                                <td>{{ $item->pengadaan->barang->nama_barang ?? '-' }}</td>
                                <td><span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $item->jenis_mutasi)) }}</span></td>
                                <td>
                                    @if($item->jenis_mutasi == 'pindah_lokasi')
                                        {{ $item->lokasiAsal->nama_sub_lokasi ?? '-' }} → {{ $item->lokasiTujuan->nama_sub_lokasi ?? '-' }}
                                    @elseif($item->jenis_mutasi == 'ubah_pengguna')
                                        {{ $item->pengguna_asal ?? '-' }} → {{ $item->pengguna_tujuan ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ Str::limit($item->alasan, 50) }}</td>
                                <td>
                                    @if($item->status_mutasi == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @elseif($item->status_mutasi == 'rejected')
                                        <span class="badge bg-danger">Rejected</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $mutasi->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
