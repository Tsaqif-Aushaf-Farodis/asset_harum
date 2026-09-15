<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                    <th class="align-middle">Nama Instansi</th>
                    <th class="align-middle">Alamat</th>
                    <th class="align-middle">Telepon</th>
                    <th class="align-middle">Email</th>
                    <th class="align-middle">Logo</th>
                    <th class="align-middle">Deskripsi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($instansi as $row)
                <tr>
                    <td>{{ $loop->iteration + ($instansi->currentPage() - 1) * $instansi->perPage() }}</td>
                    
                    <td>{{ $row?->nama_instansi }}</td>
                    <td>{{ $row?->alamat }}</td>
                    <td>{{ $row?->telepon }}</td>
                    <td>{{ $row?->email }}</td>
                    <td>
                        @if ($row?->logo)
                            <img src="{{ $row->logo_url }}" alt="Logo" style="max-height: 40px;" class="rounded border p-1">
                        @endif
                    </td>
                    <td>{{ Str::limit($row?->deskripsi, 100) }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('instansi view')
                                <div class="me-1">
                                    <a href="{{ route('instansi.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('instansi edit')
                                <div class="me-1">
                                    <a href="{{ route('instansi.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('instansi delete')
                                <form action="{{ route('instansi.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data instansi ini akan dihapus!"
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
    {!! $instansi->withQueryString()->links() !!}
</div>    