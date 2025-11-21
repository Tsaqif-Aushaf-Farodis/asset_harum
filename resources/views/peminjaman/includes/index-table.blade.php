<div id="table-container">
    <div class="mb-3">
        <form id="searchForm" class="row g-3">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Cari kode inventaris atau nama peminjam..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status_peminjaman" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status_peminjaman') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status_peminjaman') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="borrowed" {{ request('status_peminjaman') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="overdue" {{ request('status_peminjaman') == 'overdue' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="bx bx-search me-1"></i>Cari
                </button>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary">
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
                    <th>Peminjam</th>
                    <th>Tanggal Pinjam</th>
                    <th>Rencana Kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peminjaman as $index => $item)
                <tr>
                    <td>{{ $peminjaman->firstItem() + $index }}</td>
                    <td>{{ $item->pengadaan->kode_inventaris ?? '-' }}</td>
                    <td>{{ $item->pengadaan->barang->nama_barang ?? '-' }}</td>
                    <td>{{ $item->nama_peminjam }}</td>
                    <td>{{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-' }}</td>
                    <td>{{ $item->tanggal_rencana_kembali ? \Carbon\Carbon::parse($item->tanggal_rencana_kembali)->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($item->status_peminjaman == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($item->status_peminjaman == 'approved')
                            <span class="badge bg-info">Approved</span>
                        @elseif($item->status_peminjaman == 'borrowed')
                            <span class="badge bg-primary">Dipinjam</span>
                        @elseif($item->status_peminjaman == 'overdue')
                            <span class="badge bg-danger">Terlambat</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst($item->status_peminjaman) }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="dropdown">
                            <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Aksi
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('peminjaman.show', $item) }}">
                                    <i class="bx bx-show me-1"></i>Detail
                                </a>
                                @if($item->status_peminjaman == 'pending')
                                    <a class="dropdown-item" href="{{ route('peminjaman.edit', $item) }}">
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
                                    <form action="{{ route('peminjaman.destroy', $item) }}" method="POST" 
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bx bx-trash me-1"></i>Hapus
                                        </button>
                                    </form>
                                @endif
                                @if(in_array($item->status_peminjaman, ['borrowed', 'overdue']))
                                    <a class="dropdown-item text-primary" href="{{ route('peminjaman.pengembalian', $item) }}">
                                        <i class="bx bx-undo me-1"></i>Pengembalian
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Approve Modal -->
                        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('peminjaman.approve', $item) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Approve Peminjaman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menyetujui peminjaman ini?</p>
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
                                    <form action="{{ route('peminjaman.reject', $item) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Reject Peminjaman</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Yakin ingin menolak peminjaman ini?</p>
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
                    <td colspan="8" class="text-center">Tidak ada data peminjaman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $peminjaman->links() }}
    </div>
</div>
