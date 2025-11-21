<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th width="50">No</th>
                <th>Bidang</th>
                <th>Tahun Anggaran</th>
                <th>Unit Kegiatan</th>
                <th>Status</th>
                <th>Pembuat</th>
                <th>Tanggal Dibuat</th>
                <th class="text-center" width="150">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($permohonan as $row)
                <tr>
                    <td>{{ $loop->iteration + ($permohonan->currentPage() - 1) * $permohonan->perPage() }}</td>
                    <td>
                        <strong>{{ $row->bidang }}</strong>
                        @if($row->keterangan)
                            <br><small class="text-muted">{{ \Str::limit($row->keterangan, 50) }}</small>
                        @endif
                    </td>
                    <td><span class="badge bg-secondary">{{ $row->tahun_anggaran }}</span></td>
                    <td>{{ $row->unit_kegiatan }}</td>
                    <td>
                        @if($row->status == 'pending')
                            <span class="badge bg-warning">
                                <i class="bx bx-time"></i> Pending
                            </span>
                        @elseif($row->status == 'approved')
                            <span class="badge bg-success">
                                <i class="bx bx-check-circle"></i> Disetujui
                            </span>
                        @elseif($row->status == 'rejected')
                            <span class="badge bg-danger">
                                <i class="bx bx-x-circle"></i> Ditolak
                            </span>
                        @endif
                    </td>
                    <td>
                        @if($row->createdBy)
                            <small>{{ $row->createdBy->name }}</small>
                        @else
                            <small class="text-muted">-</small>
                        @endif
                    </td>
                    <td><small>{{ $row->created_at->format('d/m/Y H:i') }}</small></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            @can('permohonan view')
                                <a href="{{ route('permohonan.show', $row) }}"
                                    class="btn btn-outline-info"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                            @endcan
                            
                            @if($row->status == 'pending')
                                @can('permohonan edit')
                                    <a href="{{ route('permohonan.edit', $row) }}"
                                        class="btn btn-outline-primary"
                                        data-bs-toggle="tooltip" 
                                        data-bs-title="Edit">
                                        <i class="bx bx-pencil"></i>
                                    </a>
                                    
                                    <form action="{{ route('permohonan.approve', $row) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                            class="btn btn-outline-success"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="Setujui"
                                            onclick="return confirm('Setujui permohonan ini?')">
                                            <i class="bx bx-check"></i>
                                        </button>
                                    </form>
                                @endcan
                                @can('permohonan delete')
                                    <form action="{{ route('permohonan.destroy', $row) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <x-input.confirm-button 
                                            text="Data permohonan ini akan dihapus!"
                                            positive="Ya, hapus!" 
                                            icon="warning"
                                            class="btn btn-outline-danger"
                                            data-bs-toggle="tooltip"
                                            data-bs-title="Hapus">
                                            <i class="bx bx-trash"></i>
                                        </x-input.confirm-button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="bx bx-info-circle fs-2 text-muted"></i>
                        <p class="text-muted mb-0">Tidak ada data permohonan</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($permohonan->hasPages())
    <div class="mt-3 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Menampilkan {{ $permohonan->firstItem() }} - {{ $permohonan->lastItem() }} dari {{ $permohonan->total() }} data
        </div>
        <div>
            {!! $permohonan->withQueryString()->links() !!}
        </div>
    </div>
@endif    