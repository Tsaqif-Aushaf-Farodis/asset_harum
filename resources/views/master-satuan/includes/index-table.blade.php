<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($masterSatuan as $row)
                <tr>
                    <td>{{ $loop->iteration + ($masterSatuan->currentPage() - 1) * $masterSatuan->perPage() }}</td>
                    
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('master-satuan view')
                                <div class="me-1">
                                    <a href="{{ route('master-satuan.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('master-satuan edit')
                                <div class="me-1">
                                    <a href="{{ route('master-satuan.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('master-satuan delete')
                                <form action="{{ route('master-satuan.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data master satuan ini akan dihapus!"
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
    {!! $masterSatuan->withQueryString()->links() !!}
</div>    