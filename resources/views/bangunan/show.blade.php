<x-layout.app title="Detail Data Bangunan" activeMenu="bangunan.show" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Data Bangunan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Bangunan', 'url' => route('bangunan.index')],
            ['label' => 'Detail Data Bangunan'],
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
                                <td>: {{ $bangunan->kode_inventaris }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Barang</strong></td>
                                <td>: {{ $bangunan->barang->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: {{ $bangunan->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>: {{ $bangunan->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $bangunan->lokasi->nama_sub_lokasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sumber</strong></td>
                                <td>: {{ $bangunan->sumber }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: {{ ucfirst($bangunan->status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kondisi</strong></td>
                                <td>: {{ $bangunan->status->nama_status ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengadaan</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($bangunan->tanggal_pengadaan)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td>: {{ number_format($bangunan->jumlah, 0, ',', '.') }} {{ $bangunan->satuan->nama_satuan ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Satuan</strong></td>
                                <td>: Rp {{ number_format($bangunan->harga_satuan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Harga</strong></td>
                                <td>: Rp {{ number_format($bangunan->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @if($bangunan->keterangan)
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: {{ $bangunan->keterangan }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Detail Bangunan</h6>
                    </div>
                    <div class="card-body">
                        @if($bangunan->bangunanDetail)
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Alamat</strong></td>
                                <td>: {{ $bangunan->bangunanDetail->alamat }}</td>
                            </tr>
                            <tr>
                                <td><strong>Luas</strong></td>
                                <td>: {{ number_format($bangunan->bangunanDetail->luas, 0, ',', '.') }} m²</td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah Lantai</strong></td>
                                <td>: {{ $bangunan->bangunanDetail->jumlah_lantai }}</td>
                            </tr>
                            <tr>
                                <td><strong>Bahan Bangunan</strong></td>
                                <td>: {{ $bangunan->bangunanDetail->bahan_bangunan }}</td>
                            </tr>
                            @if($bangunan->bangunanDetail->nomor_imb)
                            <tr>
                                <td><strong>Nomor IMB</strong></td>
                                <td>: {{ $bangunan->bangunanDetail->nomor_imb }}</td>
                            </tr>
                            @endif
                            @if($bangunan->bangunanDetail->tanggal_imb)
                            <tr>
                                <td><strong>Tanggal IMB</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($bangunan->bangunanDetail->tanggal_imb)->format('d/m/Y') }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Kondisi</strong></td>
                                <td>: {{ $bangunan->bangunanDetail->kondisi }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted">Detail bangunan tidak tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('bangunan.edit', $bangunan) }}" class="btn btn-primary me-2">
                <i class="bx bx-edit me-1"></i>Edit
            </a>
            <a href="{{ route('bangunan.qr-code', $bangunan) }}" class="btn btn-info me-2">
                <i class="bx bx-qr me-1"></i>QR Code
            </a>
            <a href="{{ route('bangunan.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i>Kembali
            </a>
        </div>
    </div>
</x-layout.app>
