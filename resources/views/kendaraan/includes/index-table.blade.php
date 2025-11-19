<div id="table-container">
    <div class="mb-3">
        <form id="searchForm" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari data kendaraan..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search me-1"></i>Cari
                </button>
                <a href="{{ route('kendaraan.index') }}" class="btn btn-outline-secondary">
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
                    <th>Lokasi</th>
                    <th>Merk/Tipe</th>
                    <th>No. Polisi</th>
                    <th>Tahun</th>
                    <th>Kondisi</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kendaraanData as $index => $item)
                <tr>
                    <td>{{ $kendaraanData->firstItem() + $index }}</td>
                    <td>{{ $item->kode_inventaris }}</td>
                    <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $item->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $item->lokasi->nama_sub_lokasi ?? '-' }}</td>
                    <td>{{ $item->kendaraanDetail->merk ?? '-' }} / {{ $item->kendaraanDetail->tipe ?? '-' }}</td>
                    <td>{{ $item->kendaraanDetail->no_polisi ?? '-' }}</td>
                    <td>{{ $item->kendaraanDetail->tahun_perakitan ?? '-' }}</td>
                    <td>{{ $item->kendaraanDetail->kondisi ?? '-' }}</td>
                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('kendaraan.show', $item) }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </a>
                                <a class="dropdown-item" href="{{ route('kendaraan.edit', $item) }}">
                                    <i class="bx bx-edit me-1"></i>Edit
                                </a>
                                <a class="dropdown-item" href="{{ route('kendaraan.qr-code', $item) }}">
                                    <i class="bx bx-qr me-1"></i>QR Code
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('kendaraan.destroy', $item) }}" method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bx bx-trash me-1"></i>Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center">Tidak ada data kendaraan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kendaraanData->hasPages())
    <div class="d-flex justify-content-center">
        {{ $kendaraanData->links() }}
    </div>
    @endif
</div>
