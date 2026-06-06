<x-layout.app title="Pratinjau Import" activeMenu="pengadaan-barang" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Pratinjau Import Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang', 'url' => route('pengadaan-barang.index')],
            ['label' => 'Import', 'url' => route('pengadaan-barang.import')],
            ['label' => 'Pratinjau'],
        ]" />

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Lokasi Tujuan</h6>
                        <p class="mb-0 fw-semibold">
                            {{ $subLokasi->lokasi->nama_lokasi ?? '-' }} &mdash; {{ $subLokasi->nama_sub_lokasi }}
                            <span class="text-muted">({{ $subLokasi->kode_sub_lokasi }})</span>
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-success me-1">{{ $validCount }} valid</span>
                        <span class="badge bg-danger me-1">{{ $errorCount }} error</span>
                        <span class="badge bg-info">{{ $newBarangCount }} barang baru</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pratinjau Data</h5>
                <div>
                    <a href="{{ route('pengadaan-barang.import') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>
                    @if ($validCount > 0)
                        <form action="{{ route('pengadaan-barang.import.store') }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Simpan {{ $validCount }} data valid? Baris error akan dilewati.');">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="sub_lokasi_id" value="{{ $subLokasi->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bx bx-save me-1"></i>Simpan {{ $validCount }} Data
                            </button>
                        </form>
                    @else
                        <button class="btn btn-primary btn-sm" disabled>
                            <i class="bx bx-save me-1"></i>Simpan
                        </button>
                    @endif
                </div>
            </div>
            <div class="card-body">
                @if (count($results) === 0)
                    <div class="alert alert-warning mb-0">Tidak ada baris data yang terbaca dari file.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Baris</th>
                                    <th>Status</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Sumber</th>
                                    <th>Kondisi</th>
                                    <th>Tanggal</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Satuan</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Total</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $r)
                                    <tr class="{{ $r['valid'] ? '' : 'table-danger' }}">
                                        <td>{{ $r['row_number'] }}</td>
                                        <td>
                                            @if ($r['valid'])
                                                <span class="badge bg-success"><i class="bx bx-check"></i> Valid</span>
                                                @if ($r['barang_mode'] === 'new')
                                                    <span class="badge bg-info">Barang Baru</span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger"><i class="bx bx-x"></i> Error</span>
                                                <ul class="mb-0 ps-3 small text-danger">
                                                    @foreach ($r['errors'] as $err)
                                                        <li>{{ $err }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </td>
                                        <td>{{ $r['kode_barang'] }}</td>
                                        <td>{{ $r['nama_barang'] }}</td>
                                        <td>{{ $r['kategori'] }}</td>
                                        <td>{{ $r['sumber'] }}</td>
                                        <td>{{ $r['kondisi'] }}</td>
                                        <td>{{ $r['tanggal_pengadaan'] }}</td>
                                        <td class="text-end">{{ $r['jumlah'] }}</td>
                                        <td>{{ $r['satuan'] }}</td>
                                        <td class="text-end">{{ number_format($r['harga_satuan'], 0, ',', '.') }}</td>
                                        <td class="text-end">{{ number_format($r['total_harga'], 0, ',', '.') }}</td>
                                        <td>{{ $r['keterangan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layout.app>
