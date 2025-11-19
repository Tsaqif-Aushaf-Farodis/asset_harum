<div id="table-container">
    <div class="mb-3">
        <form id="searchForm" class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari data tanah..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search me-1"></i>Cari
                </button>
                <a href="{{ route('tanah.index') }}" class="btn btn-outline-secondary">
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
                    <th>Luas (m²)</th>
                    <th>Status Tanah</th>
                    <th>Sertifikat No</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tanahData as $index => $item)
                <tr>
                    <td>{{ $tanahData->firstItem() + $index }}</td>
                    <td>{{ $item->kode_inventaris }}</td>
                    <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $item->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $item->lokasi->nama_sub_lokasi ?? '-' }}</td>
                    <td>{{ number_format($item->tanahDetail->luas ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $item->tanahDetail->status_tanah ?? '-' }}</td>
                    <td>{{ $item->tanahDetail->sertifikat_nomor ?? '-' }}</td>
                    <td>Rp {{ number_format($item->total_harga, 0, ',', '.') }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('tanah.show', $item) }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </a>
                                <a class="dropdown-item" href="{{ route('tanah.edit', $item) }}">
                                    <i class="bx bx-edit me-1"></i>Edit
                                </a>
                                <a class="dropdown-item" href="{{ route('tanah.qr-code', $item) }}">
                                    <i class="bx bx-qr me-1"></i>QR Code
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('tanah.destroy', $item) }}" method="POST" 
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
                    <td colspan="9" class="text-center">Tidak ada data tanah</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tanahData->hasPages())
    <div class="d-flex justify-content-center">
        {{ $tanahData->links() }}
    </div>
    @endif
</div>
