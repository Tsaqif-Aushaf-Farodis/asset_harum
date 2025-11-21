<x-layout.app title="Detail Peminjaman" activeMenu="peminjaman.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Peminjaman" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman', 'url' => route('peminjaman.index')],
            ['label' => 'Detail'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Detail Peminjaman Aset</h5>
                <span class="badge {{ $peminjaman->status_peminjaman == 'pending' ? 'bg-warning' : ($peminjaman->status_peminjaman == 'borrowed' ? 'bg-primary' : ($peminjaman->status_peminjaman == 'returned' ? 'bg-success' : 'bg-secondary')) }}">
                    {{ ucfirst($peminjaman->status_peminjaman) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Informasi Aset</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Kode Inventaris</th>
                                <td>: {{ $peminjaman->pengadaan->kode_inventaris ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>: {{ $peminjaman->pengadaan->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>: {{ $peminjaman->pengadaan->kategori->nama_kategori ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Informasi Peminjam</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th>Nama Peminjam</th>
                                <td>: {{ $peminjaman->peminjam_nama }}</td>
                            </tr>
                            <tr>
                                <th>NIP</th>
                                <td>: {{ $peminjaman->peminjam_nip ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Instansi</th>
                                <td>: {{ $peminjaman->peminjam_instansi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Telepon</th>
                                <td>: {{ $peminjaman->peminjam_telepon }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Tanggal & Waktu</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Tanggal Pinjam</th>
                                <td>: {{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Rencana Kembali</th>
                                <td>: {{ $peminjaman->tanggal_rencana_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            @if($peminjaman->tanggal_kembali)
                            <tr>
                                <th>Tanggal Kembali</th>
                                <td>: {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Status & Approval</h6>
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Status</th>
                                <td>: <span class="badge bg-primary">{{ ucfirst($peminjaman->status_peminjaman) }}</span></td>
                            </tr>
                            @if($peminjaman->approved_by)
                            <tr>
                                <th>Disetujui Oleh</th>
                                <td>: {{ $peminjaman->approvedBy->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Approval</th>
                                <td>: {{ $peminjaman->approved_at ? \Carbon\Carbon::parse($peminjaman->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Keperluan:</h6>
                        <p>{{ $peminjaman->keperluan }}</p>
                    </div>
                </div>

                @if($peminjaman->keterangan)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Keterangan:</h6>
                        <p>{{ $peminjaman->keterangan }}</p>
                    </div>
                </div>
                @endif

                @if($peminjaman->catatan_approval)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Catatan Approval:</h6>
                        <p>{{ $peminjaman->catatan_approval }}</p>
                    </div>
                </div>
                @endif

                @if($peminjaman->kondisi_kembali)
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted">Kondisi Saat Kembali:</h6>
                        <p><span class="badge bg-info">{{ $peminjaman->kondisi_kembali }}</span></p>
                    </div>
                </div>
                @endif

                @if($peminjaman->keterangan_pengembalian)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Keterangan Pengembalian:</h6>
                        <p>{{ $peminjaman->keterangan_pengembalian }}</p>
                    </div>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>
                    @if($peminjaman->status_peminjaman == 'pending')
                        <a href="{{ route('peminjaman.edit', $peminjaman) }}" class="btn btn-primary">Edit</a>
                    @endif
                    @if(in_array($peminjaman->status_peminjaman, ['borrowed', 'overdue']))
                        <a href="{{ route('peminjaman.pengembalian', $peminjaman) }}" class="btn btn-success">Pengembalian</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
