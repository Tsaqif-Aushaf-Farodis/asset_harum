<x-layout.app title="Detail Permohonan" activeMenu="permohonan.show" :withError="true">
    <div class="container my-5">
        <x-breadcrumb title="Detail Permohonan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Permohonan', 'url' => route('permohonan.index')],
            ['label' => 'Detail Permohonan'],
        ]" />

        <div class="row">
            <!-- Informasi Utama -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bx bx-file me-2"></i>Informasi Permohonan</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%" class="fw-semibold">Bidang</td>
                                <td>{{ $permohonan->bidang }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Tahun Anggaran</td>
                                <td><span class="badge bg-secondary">{{ $permohonan->tahun_anggaran }}/{{ $permohonan->tahun_anggaran + 1 }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Unit Kegiatan</td>
                                <td>{{ $permohonan->unit_kegiatan }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Keterangan</td>
                                <td>{{ $permohonan->keterangan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Status</td>
                                <td>
                                    @if($permohonan->status == 'pending')
                                        <span class="badge bg-warning"><i class="bx bx-time"></i> Pending</span>
                                    @elseif($permohonan->status == 'approved')
                                        <span class="badge bg-success"><i class="bx bx-check-circle"></i> Disetujui</span>
                                    @elseif($permohonan->status == 'rejected')
                                        <span class="badge bg-danger"><i class="bx bx-x-circle"></i> Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold">Dibuat Oleh</td>
                                <td>{{ $permohonan->createdBy->name ?? '-' }} <small class="text-muted">({{ $permohonan->created_at->format('d/m/Y H:i') }})</small></td>
                            </tr>
                            @if($permohonan->approved_by)
                            <tr>
                                <td class="fw-semibold">{{ $permohonan->status == 'approved' ? 'Disetujui' : 'Ditolak' }} Oleh</td>
                                <td>{{ $permohonan->approvedBy->name }} <small class="text-muted">({{ $permohonan->approved_at->format('d/m/Y H:i') }})</small></td>
                            </tr>
                            @endif
                            @if($permohonan->catatan_approval)
                            <tr>
                                <td class="fw-semibold">Catatan</td>
                                <td><div class="alert alert-info mb-0">{{ $permohonan->catatan_approval }}</div></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Detail Barang -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="bx bx-list-ul me-2"></i>Detail Barang</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="25%">Uraian Barang</th>
                                        <th width="10%">Volume</th>
                                        <th width="10%">Satuan</th>
                                        <th width="15%">Harga Satuan</th>
                                        <th width="15%">Jumlah</th>
                                        <th width="10%">Kode MA</th>
                                        <th width="10%">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($permohonan->details as $detail)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $detail->barang->nama_barang ?? $detail->uraian_barang }}</td>
                                        <td class="text-center">{{ number_format($detail->volume, 2) }}</td>
                                        <td class="text-center">{{ $detail->satuan }}</td>
                                        <td class="text-end">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                                        <td class="text-end"><strong>Rp {{ number_format($detail->jumlah, 0, ',', '.') }}</strong></td>
                                        <td class="text-center">{{ $detail->kode_ma ?? '-' }}</td>
                                        <td>{{ $detail->keterangan ?? '-' }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Tidak ada detail barang</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                                @if($permohonan->details->count() > 0)
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="5" class="text-end">Total:</th>
                                        <th class="text-end">Rp {{ number_format($permohonan->details->sum('jumlah'), 0, ',', '.') }}</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Aksi -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-cog me-2"></i>Aksi</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>

                            @if($permohonan->status == 'pending')
                                @can('permohonan edit')
                                <a href="{{ route('permohonan.edit', $permohonan) }}" class="btn btn-primary">
                                    <i class="bx bx-pencil me-1"></i>Edit Permohonan
                                </a>
                                
                                <hr>
                                
                                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                                    <i class="bx bx-check-circle me-1"></i>Setujui
                                </button>
                                
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bx bx-x-circle me-1"></i>Tolak
                                </button>
                                @endcan

                                @can('permohonan delete')
                                <hr>
                                <form action="{{ route('permohonan.destroy', $permohonan) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <x-input.confirm-button 
                                        text="Data permohonan ini akan dihapus!"
                                        positive="Ya, hapus!" 
                                        icon="warning"
                                        class="btn btn-outline-danger w-100">
                                        <i class="bx bx-trash me-1"></i>Hapus Permohonan
                                    </x-input.confirm-button>
                                </form>
                                @endcan
                            @else
                                @can('permohonan create')
                                <a href="{{ route('permohonan.create') }}" class="btn btn-info">
                                    <i class="bx bx-plus me-1"></i>Permohonan Baru
                                </a>
                                @endcan
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('permohonan.approve', $permohonan) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="bx bx-check-circle me-2"></i>Setujui Permohonan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menyetujui permohonan ini?</p>
                        <div class="mb-3">
                            <label for="catatan_approval" class="form-label">Catatan (Opsional)</label>
                            <textarea name="catatan_approval" class="form-control" rows="3" placeholder="Tambahkan catatan approval..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bx bx-check me-1"></i>Ya, Setujui
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('permohonan.reject', $permohonan) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="bx bx-x-circle me-2"></i>Tolak Permohonan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menolak permohonan ini?</p>
                        <div class="mb-3">
                            <label for="catatan_approval" class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan_approval" class="form-control @error('catatan_approval') is-invalid @enderror" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                            @error('catatan_approval')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bx bx-x me-1"></i>Ya, Tolak
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
