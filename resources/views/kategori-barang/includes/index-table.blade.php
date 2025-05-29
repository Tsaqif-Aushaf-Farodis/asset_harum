<div class="table-responsive" >
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                
                    <th class="align-middle">Kode Kategori Barang</th>
                    <th class="align-middle">Nama Kategori Barang</th>
                    <th class="align-middle">Deskripsi Kategori Barang</th>
                    <th class="align-middle">Status Kategori Barang</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($kategoriBarang as $row)
                <tr>
                    <td>{{ $loop->iteration + ($kategoriBarang->currentPage() - 1) * $kategoriBarang->perPage() }}</td>
                    
                    <td>{{ $row?->kode_kategori_barang }}</td>
                    <td>{{ $row?->nama_kategori_barang }}</td>
                    <td>{{ $row?->deskripsi_kategori_barang }}</td>
                    <td>{{ $row?->status_kategori_barang }}</td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            @can('kategori-barang view')
                                <div class="me-1">
                                    <a href="{{ route('kategori-barang.show', $row) }}"
                                        class="btn btn-icon btn-outline-info btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Detail"
                                        data-bs-placement="top">
                                        <span class="bx bx-show"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('kategori-barang edit')
                                <div class="me-1">
                                    <a href="{{ route('kategori-barang.edit', $row) }}"
                                        class="btn btn-icon btn-outline-primary btn-sm"
                                        data-bs-toggle="tooltip" data-bs-title="Edit"
                                        data-bs-placement="top">
                                        <span class="bx bx-pencil"></span>
                                    </a>
                                </div>
                            @endcan
                            @can('kategori-barang delete')
                                <form action="{{ route('kategori-barang.destroy', $row) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-input.confirm-button text="Data kategori barang ini akan dihapus!"
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
    {!! $kategoriBarang->withQueryString()->links() !!}
</div>    