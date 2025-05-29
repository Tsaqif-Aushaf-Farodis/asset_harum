<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                    <th class="align-middle">Kode Lokasi</th>
                    <th class="align-middle">Nama Lokasi</th>
                    <th class="align-middle">Deskripsi Lokasi</th>
                    <th class="align-middle">Alamat Lokasi</th>
                    <th class="align-middle">Telepon Lokasi</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($masterLokasi as $row)
                <tr>
                    <td>{{ $loop->iteration + ($masterLokasi->currentPage() - 1) * $masterLokasi->perPage() }}</td>
                    
                    <td>{{ $row?->kode_lokasi }}</td>
                    <td>{{ $row?->nama_lokasi }}</td>
                    <td>{{ $row?->deskripsi_lokasi }}</td>
                    <td>{{ $row?->alamat_lokasi }}</td>
                    <td>{{ $row?->telepon_lokasi }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('master-lokasi view')
                                <div class="me-1">
                                    <a href="{{ route('master-lokasi.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('master-lokasi edit')
                                <div class="me-1">
                                    <a href="{{ route('master-lokasi.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('master-lokasi delete')
                                <form action="{{ route('master-lokasi.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data master lokasi ini akan dihapus!"
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
    {!! $masterLokasi->withQueryString()->links() !!}
</div>    