<x-layout.app title="Detail Data Tanah" activeMenu="tanah.show" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Data Tanah" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Tanah', 'url' => route('tanah.index')],
            ['label' => 'Detail Data Tanah'],
        ]" />

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Informasi Pengadaan</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Kode Inventaris</strong></td>
                                <td>: {{ $tanah->kode_inventaris }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Barang</strong></td>
                                <td>: {{ $tanah->barang->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: {{ $tanah->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>: {{ $tanah->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $tanah->lokasi->nama_sub_lokasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sumber</strong></td>
                                <td>: {{ $tanah->sumber }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: {{ ucfirst($tanah->status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kondisi</strong></td>
                                <td>: {{ $tanah->status->nama_status ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengadaan</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($tanah->tanggal_pengadaan)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td>: {{ number_format($tanah->jumlah, 0, ',', '.') }} {{ $tanah->satuan->nama_satuan ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Satuan</strong></td>
                                <td>: Rp {{ number_format($tanah->harga_satuan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Harga</strong></td>
                                <td>: Rp {{ number_format($tanah->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @if($tanah->keterangan)
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: {{ $tanah->keterangan }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Detail Tanah</h6>
                    </div>
                    <div class="card-body">
                        @if($tanah->tanahDetail)
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Luas</strong></td>
                                <td>: {{ number_format($tanah->tanahDetail->luas, 0, ',', '.') }} m²</td>
                            </tr>
                            <tr>
                                <td><strong>Status Tanah</strong></td>
                                <td>: {{ $tanah->tanahDetail->status_tanah }}</td>
                            </tr>
                            @if($tanah->tanahDetail->sertifikat_nomor)
                            <tr>
                                <td><strong>Nomor Sertifikat</strong></td>
                                <td>: {{ $tanah->tanahDetail->sertifikat_nomor }}</td>
                            </tr>
                            @endif
                            @if($tanah->tanahDetail->sertifikat_tanggal)
                            <tr>
                                <td><strong>Tanggal Sertifikat</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($tanah->tanahDetail->sertifikat_tanggal)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            @if($tanah->tanahDetail->penggunaan)
                            <tr>
                                <td><strong>Penggunaan</strong></td>
                                <td>: {{ $tanah->tanahDetail->penggunaan }}</td>
                            </tr>
                            @endif
                            @if($tanah->tanahDetail->lokasi)
                            <tr>
                                <td><strong>Lokasi Detail</strong></td>
                                <td>: {{ $tanah->tanahDetail->lokasi }}</td>
                            </tr>
                            @endif
                        </table>
                        @else
                        <p class="text-muted">Detail tanah tidak tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('tanah.edit', $tanah) }}" class="btn btn-primary me-2">
                <i class="bx bx-edit me-1"></i>Edit
            </a>
            <a href="{{ route('tanah.qr-code', $tanah) }}" class="btn btn-info me-2">
                <i class="bx bx-qr me-1"></i>QR Code
            </a>
            <a href="{{ route('tanah.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i>Kembali
            </a>
        </div>
    </div>
</x-layout.app>
