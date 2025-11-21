<x-layout.app title="Detail Opname" activeMenu="opname.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Opname" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Opname', 'url' => route('opname.index')],
            ['label' => 'Detail'],
        ]" />

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Informasi Opname</h5>
                        <span class="badge {{ $opname->status == 'draft' ? 'bg-secondary' : ($opname->status == 'ongoing' ? 'bg-primary' : 'bg-success') }}">
                            {{ ucfirst($opname->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Kode Opname</th>
                                <td>: {{ $opname->kode_opname }}</td>
                            </tr>
                            <tr>
                                <th>Nama Opname</th>
                                <td>: {{ $opname->nama_opname }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td>: {{ $opname->lokasi->nama_sub_lokasi ?? 'Semua Lokasi' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td>: {{ $opname->tanggal_mulai ? \Carbon\Carbon::parse($opname->tanggal_mulai)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td>: {{ $opname->tanggal_selesai ? \Carbon\Carbon::parse($opname->tanggal_selesai)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>: {{ $opname->createdBy->name ?? '-' }}</td>
                            </tr>
                            @if($opname->keterangan)
                            <tr>
                                <th>Keterangan</th>
                                <td>: {{ $opname->keterangan }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Ringkasan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Total Aset</td>
                                <td class="text-end"><strong>{{ $summary['total'] }}</strong></td>
                            </tr>
                            <tr class="table-success">
                                <td>Sesuai</td>
                                <td class="text-end"><strong>{{ $summary['sesuai'] }}</strong></td>
                            </tr>
                            <tr class="table-warning">
                                <td>Tidak Sesuai</td>
                                <td class="text-end"><strong>{{ $summary['tidak_sesuai'] }}</strong></td>
                            </tr>
                            <tr class="table-danger">
                                <td>Hilang</td>
                                <td class="text-end"><strong>{{ $summary['hilang'] }}</strong></td>
                            </tr>
                            <tr class="table-info">
                                <td>Rusak</td>
                                <td class="text-end"><strong>{{ $summary['rusak'] }}</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td>Baru Ditemukan</td>
                                <td class="text-end"><strong>{{ $summary['baru'] }}</strong></td>
                            </tr>
                        </table>

                        <div class="d-grid gap-2 mt-3">
                            @if($opname->status == 'ongoing')
                                <a href="{{ route('opname.input-hasil', $opname) }}" class="btn btn-primary">
                                    <i class="bx bx-edit me-1"></i>Input Hasil
                                </a>
                                <a href="{{ route('opname.export-kertas-kerja', $opname) }}" class="btn btn-outline-primary">
                                    <i class="bx bx-download me-1"></i>Export Kertas Kerja
                                </a>
                            @endif
                            @if($opname->status == 'completed')
                                <a href="{{ route('opname.export-laporan', $opname) }}" class="btn btn-success">
                                    <i class="bx bx-download me-1"></i>Export Laporan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Aset</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-sm">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode Inventaris</th>
                                <th>Nama Barang</th>
                                <th>Kondisi Sebelum</th>
                                <th>Status Keberadaan</th>
                                <th>Kondisi Sesudah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($opname->details as $index => $detail)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $detail->pengadaan->kode_inventaris ?? '-' }}</td>
                                <td>{{ $detail->pengadaan->barang->nama_barang ?? '-' }}</td>
                                <td>{{ $detail->kondisi_sebelum ?? '-' }}</td>
                                <td>
                                    @if($detail->status_keberadaan == 'sesuai')
                                        <span class="badge bg-success">Sesuai</span>
                                    @elseif($detail->status_keberadaan == 'tidak_sesuai')
                                        <span class="badge bg-warning">Tidak Sesuai</span>
                                    @elseif($detail->status_keberadaan == 'hilang')
                                        <span class="badge bg-danger">Hilang</span>
                                    @elseif($detail->status_keberadaan == 'rusak')
                                        <span class="badge bg-info">Rusak</span>
                                    @elseif($detail->status_keberadaan == 'baru')
                                        <span class="badge bg-primary">Baru</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Dicek</span>
                                    @endif
                                </td>
                                <td>{{ $detail->kondisi_sesudah ?? '-' }}</td>
                                <td>{{ $detail->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('opname.index') }}" class="btn btn-secondary">Kembali</a>
            @if($opname->status == 'draft')
                <a href="{{ route('opname.edit', $opname) }}" class="btn btn-primary">Edit</a>
            @endif
        </div>
    </div>
</x-layout.app>
