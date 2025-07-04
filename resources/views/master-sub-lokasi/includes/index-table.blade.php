<div class="table-responsive">
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>

                <th class="align-middle">Lokasi</th>
                <th class="align-middle">Kode Sub Lokasi</th>
                <th class="align-middle">Nama Sub Lokasi</th>
                <th class="align-middle">Keterangan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($masterSubLokasi as $row)
            <tr>
                <td>{{ $loop->iteration + ($masterSubLokasi->currentPage() - 1) * $masterSubLokasi->perPage() }}</td>

                <td>{{ $row?->lokasi->nama_lokasi }}</td>
                <td>{{ $row?->kode_sub_lokasi }}</td>
                <td>{{ $row?->nama_sub_lokasi }}</td>
                <td>{{ $row?->keterangan }}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        @can('master-sub-lokasi view')
                        <div class="me-1">
                            <a href="{{ route('master-sub-lokasi.show', $row) }}"
                                class="btn btn-icon btn-outline-info btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Detail" data-bs-placement="top">
                                <span class="bx bx-show"></span>
                            </a>
                        </div>
                        @endcan
                        @can('master-sub-lokasi edit')
                        <div class="me-1">
                            <a href="{{ route('master-sub-lokasi.edit', $row) }}"
                                class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Edit" data-bs-placement="top">
                                <span class="bx bx-pencil"></span>
                            </a>
                        </div>
                        @endcan
                        @can('master-sub-lokasi delete')
                        <form action="{{ route('master-sub-lokasi.destroy', $row) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-input.confirm-button text="Data master sub lokasi ini akan dihapus!"
                                positive="Ya, hapus!" icon="info" class="btn btn-icon btn-outline-danger btn-sm"
                                data-bs-toggle="tooltip" data-bs-title="Hapus" data-bs-placement="top">
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
    {!! $masterSubLokasi->withQueryString()->links() !!}
</div>