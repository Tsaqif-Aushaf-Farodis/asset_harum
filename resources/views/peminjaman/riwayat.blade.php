<x-layout.app title="Riwayat Peminjaman" activeMenu="peminjaman.riwayat" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Riwayat Peminjaman" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman', 'url' => route('peminjaman.index')],
            ['label' => 'Riwayat'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Riwayat Peminjaman Aset</h5>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-primary">
                    <i class="bx bx-arrow-back me-1"></i>Kembali ke Daftar Peminjaman
                </a>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari kode inventaris atau nama barang..." 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bx bx-search me-1"></i>Cari
                            </button>
                            <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-outline-secondary">
                                <i class="bx bx-refresh me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Inventaris</th>
                                <th>Nama Barang</th>
                                <th>Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali</th>
                                <th>Lama Pinjam</th>
                                <th>Status</th>
                                <th>Kondisi Kembali</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayat as $index => $item)
                            @php
                                $lamaPinjam = '-';
                                if($item->tanggal_pinjam && $item->tanggal_kembali) {
                                    $diff = \Carbon\Carbon::parse($item->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($item->tanggal_kembali));
                                    $lamaPinjam = $diff . ' hari';
                                }
                            @endphp
                            <tr>
                                <td>{{ $riwayat->firstItem() + $index }}</td>
                                <td>{{ $item->pengadaan->kode_inventaris ?? '-' }}</td>
                                <td>{{ $item->pengadaan->nama_barang ?? '-' }}</td>
                                <td>{{ $item->nama_peminjam }}</td>
                                <td>{{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $lamaPinjam }}</td>
                                <td>
                                    @if($item->status_peminjaman == 'returned')
                                        <span class="badge bg-success">Dikembalikan</span>
                                    @elseif($item->status_peminjaman == 'overdue')
                                        <span class="badge bg-danger">Terlambat</span>
                                    @elseif($item->status_peminjaman == 'lost')
                                        <span class="badge bg-dark">Hilang</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($item->status_peminjaman) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->kondisi_kembali == 'Baik')
                                        <span class="badge bg-success">{{ $item->kondisi_kembali }}</span>
                                    @elseif($item->kondisi_kembali == 'Rusak Ringan')
                                        <span class="badge bg-warning">{{ $item->kondisi_kembali }}</span>
                                    @elseif($item->kondisi_kembali == 'Rusak Berat')
                                        <span class="badge bg-danger">{{ $item->kondisi_kembali }}</span>
                                    @elseif($item->kondisi_kembali == 'Hilang')
                                        <span class="badge bg-dark">{{ $item->kondisi_kembali }}</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('peminjaman.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-show me-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Tidak ada riwayat peminjaman</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $riwayat->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
