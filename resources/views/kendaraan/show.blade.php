<x-layout.app title="Detail Data Kendaraan" activeMenu="kendaraan.show" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Data Kendaraan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Kendaraan', 'url' => route('kendaraan.index')],
            ['label' => 'Detail Data Kendaraan'],
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
                                <td>: {{ $kendaraan->kode_inventaris }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nama Barang</strong></td>
                                <td>: {{ $kendaraan->barang->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: {{ $kendaraan->barang->kategori->nama_kategori_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>: {{ $kendaraan->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $kendaraan->lokasi->nama_sub_lokasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Sumber</strong></td>
                                <td>: {{ $kendaraan->sumber }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: {{ ucfirst($kendaraan->status) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kondisi</strong></td>
                                <td>: {{ $kendaraan->status->nama_status ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengadaan</strong></td>
                                <td>: {{ \Carbon\Carbon::parse($kendaraan->tanggal_pengadaan)->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Jumlah</strong></td>
                                <td>: {{ number_format($kendaraan->jumlah, 0, ',', '.') }} {{ $kendaraan->satuan->nama_satuan ?? '' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Harga Satuan</strong></td>
                                <td>: Rp {{ number_format($kendaraan->harga_satuan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Harga</strong></td>
                                <td>: Rp {{ number_format($kendaraan->total_harga, 0, ',', '.') }}</td>
                            </tr>
                            @if($kendaraan->keterangan)
                            <tr>
                                <td><strong>Keterangan</strong></td>
                                <td>: {{ $kendaraan->keterangan }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">Detail Kendaraan</h6>
                    </div>
                    <div class="card-body">
                        @if($kendaraan->kendaraanDetail)
                        <table class="table table-borderless">
                            <tr>
                                <td width="40%"><strong>Merk</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->merk }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipe</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->tipe }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tahun Perakitan</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->tahun_perakitan }}</td>
                            </tr>
                            @if($kendaraan->kendaraanDetail->no_polisi)
                            <tr>
                                <td><strong>Nomor Polisi</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->no_polisi }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Nomor Rangka</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->no_rangka }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nomor Mesin</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->no_mesin }}</td>
                            </tr>
                            @if($kendaraan->kendaraanDetail->kapasitas_cc)
                            <tr>
                                <td><strong>Kapasitas</strong></td>
                                <td>: {{ number_format($kendaraan->kendaraanDetail->kapasitas_cc, 0, ',', '.') }} CC</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Warna</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->warna }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kondisi</strong></td>
                                <td>: {{ $kendaraan->kendaraanDetail->kondisi }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted">Detail kendaraan tidak tersedia</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('kendaraan.edit', $kendaraan) }}" class="btn btn-primary me-2">
                <i class="bx bx-edit me-1"></i>Edit
            </a>
            <a href="{{ route('kendaraan.qr-code', $kendaraan) }}" class="btn btn-info me-2">
                <i class="bx bx-qr me-1"></i>QR Code
            </a>
            <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">
                <i class="bx bx-arrow-back me-1"></i>Kembali
            </a>
        </div>
    </div>
</x-layout.app>
