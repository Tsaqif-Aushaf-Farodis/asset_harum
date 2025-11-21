<x-layout.app title="Detail Mutasi Aset" activeMenu="mutasi-aset.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Detail Mutasi Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Mutasi Aset', 'url' => route('mutasi-aset.index')],
            ['label' => 'Detail'],
        ]" />

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Detail Mutasi Aset</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Kode Inventaris</th>
                                <td>: {{ $mutasiAset->pengadaan->kode_inventaris ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td>: {{ $mutasiAset->pengadaan->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Mutasi</th>
                                <td>: <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $mutasiAset->jenis_mutasi)) }}</span></td>
                            </tr>
                            <tr>
                                <th>Tanggal Mutasi</th>
                                <td>: {{ $mutasiAset->tanggal_mutasi ? \Carbon\Carbon::parse($mutasiAset->tanggal_mutasi)->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>: 
                                    @if($mutasiAset->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($mutasiAset->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            @if(in_array($mutasiAset->jenis_mutasi, ['pindah_lokasi']))
                            <tr>
                                <th width="200">Lokasi Asal</th>
                                <td>: {{ $mutasiAset->lokasiAsal->nama_sub_lokasi ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Lokasi Tujuan</th>
                                <td>: {{ $mutasiAset->lokasiTujuan->nama_sub_lokasi ?? '-' }}</td>
                            </tr>
                            @endif
                            @if(in_array($mutasiAset->jenis_mutasi, ['ubah_pengguna']))
                            <tr>
                                <th width="200">Pengguna Asal</th>
                                <td>: {{ $mutasiAset->pengguna_asal ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Pengguna Tujuan</th>
                                <td>: {{ $mutasiAset->pengguna_tujuan ?? '-' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>Dibuat Oleh</th>
                                <td>: {{ $mutasiAset->createdBy->name ?? '-' }}</td>
                            </tr>
                            @if($mutasiAset->approved_by)
                            <tr>
                                <th>Disetujui Oleh</th>
                                <td>: {{ $mutasiAset->approvedBy->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Approval</th>
                                <td>: {{ $mutasiAset->approved_at ? \Carbon\Carbon::parse($mutasiAset->approved_at)->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Alasan:</h6>
                        <p>{{ $mutasiAset->alasan }}</p>
                    </div>
                </div>

                @if($mutasiAset->keterangan)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Keterangan:</h6>
                        <p>{{ $mutasiAset->keterangan }}</p>
                    </div>
                </div>
                @endif

                @if($mutasiAset->catatan_approval)
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6>Catatan Approval:</h6>
                        <p>{{ $mutasiAset->catatan_approval }}</p>
                    </div>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('mutasi-aset.index') }}" class="btn btn-secondary">Kembali</a>
                    @if($mutasiAset->status == 'pending')
                        <a href="{{ route('mutasi-aset.edit', $mutasiAset) }}" class="btn btn-primary">Edit</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
