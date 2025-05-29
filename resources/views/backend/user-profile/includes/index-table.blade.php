<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                    <th class="align-middle">Full Name</th>
                    <th class="align-middle">Avatar Url</th>
                    <th class="align-middle">No Telp</th>
                    <th class="align-middle">Alamat</th>
                    <th class="align-middle">Jenis Kelamin</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($userProfile as $row)
                <tr>
                    <td>{{ $loop->iteration + ($userProfile->currentPage() - 1) * $userProfile->perPage() }}</td>
                    
                    <td>{{ $row?->full_name }}</td>
                    <td>{{ $row?->avatar_url }}</td>
                    <td>{{ $row?->no_telp }}</td>
                    <td>{{ $row?->alamat }}</td>
                    <td>{{ $row?->jenis_kelamin }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('user-profile view')
                                <div class="me-1">
                                    <a href="{{ route('user-profile.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('user-profile edit')
                                <div class="me-1">
                                    <a href="{{ route('user-profile.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('user-profile delete')
                                <form action="{{ route('user-profile.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data user profile ini akan dihapus!"
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
    {!! $userProfile->withQueryString()->links() !!}
</div>    