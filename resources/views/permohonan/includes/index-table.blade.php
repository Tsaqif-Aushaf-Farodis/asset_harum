<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                    <th class="align-middle">Bidang</th>
                    <th class="align-middle">Tahun Anggaran</th>
                    <th class="align-middle">Unit Kegiatan</th>
                    <th class="align-middle">Keterangan</th>
                    <th class="align-middle">Status</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permohonan as $row)
                <tr>
                    <td>{{ $loop->iteration + ($permohonan->currentPage() - 1) * $permohonan->perPage() }}</td>
                    
                    <td>{{ $row?->bidang }}</td>
                    <td>{{ $row?->tahun_anggaran }}</td>
                    <td>{{ $row?->unit_kegiatan }}</td>
                    <td>{{ $row?->keterangan }}</td>
                    <td>{{ $row?->status }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('permohonan view')
                                <div class="me-1">
                                    <a href="{{ route('permohonan.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('permohonan edit')
                                <div class="me-1">
                                    <a href="{{ route('permohonan.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('permohonan delete')
                                <form action="{{ route('permohonan.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data permohonan ini akan dihapus!"
                                        positive="Ya, hapus!" icon="info"
                                        class="btn btn-icon btn-outline-danger btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Hapus"
                                        data-bs-placement="top">
                                        <span class="bx bx-trash"></span>
                                    </x-input.confirm-button>
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-3 d-flex justify-content-end">
    {!! $permohonan->withQueryString()->links() !!}
</div>    