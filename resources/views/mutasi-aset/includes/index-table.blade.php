<div id="table-container">
    <div class="mb-3">
        <form id="searchForm" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari kode inventaris atau nama barang..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="jenis_mutasi" class="form-select">
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="pindah_lokasi" {{ request('jenis_mutasi') == 'pindah_lokasi' ? 'selected' : '' }}>Pindah Lokasi</option>
                    <option value="ubah_pengguna" {{ request('jenis_mutasi') == 'ubah_pengguna' ? 'selected' : '' }}>Ubah Pengguna</option>
                    <option value="non_aktif" {{ request('jenis_mutasi') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
                    <option value="barang_keluar" {{ request('jenis_mutasi') == 'barang_keluar' ? 'selected' : '' }}>Barang Keluar</option>
                    <option value="penghapusan" {{ request('jenis_mutasi') == 'penghapusan' ? 'selected' : '' }}>Penghapusan</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search me-1"></i>Cari
                </button>
                <a href="{{ route('mutasi-aset.index') }}" class="btn btn-outline-secondary">
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
                    <th>Tanggal</th>
                    <th>Kode Inventaris</th>
                    <th>Nama Barang</th>
                    <th>Jenis Mutasi</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mutasi as $index => $item)
                <tr>
                    <td>{{ $mutasi->firstItem() + $index }}</td>
                    <td>{{ $item->tanggal_mutasi ? \Carbon\Carbon::parse($item->tanggal_mutasi)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->pengadaan->kode_inventaris ?? '-' }}</td>
                    <td>{{ $item->pengadaan->barang->nama_barang ?? '-' }}</td>
                    <td>
                        <span class="badge bg-info">{{ ucwords(str_replace('_', ' ', $item->jenis_mutasi)) }}</span>
                    </td>
                    <td>
                        @if($item->status_mutasi == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($item->status_mutasi == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>
                    <td>{{ $item->createdBy->name ?? '-' }}</td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('mutasi-aset.show', $item) }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </a>
                                @if($item->status_mutasi== 'pending')
                                    <a class="dropdown-item" href="{{ route('mutasi-aset.edit', $item) }}">
                                        <i class="bx bx-edit me-1"></i>Edit
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-success" href="#" data-bs-toggle="modal" 
                                       data-bs-target="#approveModal{{ $item->id }}">
                                        <i class="bx bx-check me-1"></i>Approve
                                    </a>
                                    <a class="dropdown-item text-warning" href="#" data-bs-toggle="modal" 
                                       data-bs-target="#rejectModal{{ $item->id }}">
                                        <i class="bx bx-x me-1"></i>Reject
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <form action="{{ route('mutasi-aset.destroy', $item) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('mutasi-aset.approve', $item) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Mutasi Aset</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menyetujui mutasi aset ini?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="catatan_approval" class="form-control" rows="3"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Reject Modal -->
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('mutasi-aset.reject', $item) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Mutasi Aset</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menolak mutasi aset ini?</p>
                                            <div class="mb-3">
                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                <textarea name="catatan_approval" class="form-control" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning">Reject</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data mutasi aset</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $mutasi->links() }}
    </div>
</div>
