<x-layout.app title="Pengembalian Aset" activeMenu="peminjaman.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Pengembalian Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman', 'url' => route('peminjaman.index')],
            ['label' => 'Pengembalian'],
        ]" />

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Form Pengembalian Aset</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Kode Inventaris</th>
                                <td>: {{ $peminjaman->pengadaan->kode_inventaris ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>: {{ $peminjaman->pengadaan->barang->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Peminjam</th>
                                <td>: {{ $peminjaman->peminjam_nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="180">Tanggal Pinjam</th>
                                <td>: {{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Rencana Kembali</th>
                                <td>: {{ $peminjaman->tanggal_rencana_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Keperluan</th>
                                <td>: {{ $peminjaman->keperluan }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <x-error-list />

                <form action="{{ route('peminjaman.store-pengembalian', $peminjaman) }}" method="POST" role="form">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_kembali" class="form-label">Tanggal Kembali <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_kembali" id="tanggal_kembali" class="form-control" 
                                   value="{{ old('tanggal_kembali', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="kondisi_kembali" class="form-label">Kondisi Barang Saat Kembali <span class="text-danger">*</span></label>
                            <select name="kondisi_kembali" id="kondisi_kembali" class="form-select" required>
                                <option value="">Pilih Kondisi</option>

                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">
                                        {{ $status->nama_status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="keterangan_pengembalian" class="form-label">Keterangan Pengembalian</label>
                            <textarea name="keterangan_pengembalian" id="keterangan_pengembalian" class="form-control" rows="4">{{ old('keterangan_pengembalian') }}</textarea>
                            <small class="text-muted">Jelaskan kondisi barang atau hal-hal penting lainnya</small>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Simpan Pengembalian</button>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
