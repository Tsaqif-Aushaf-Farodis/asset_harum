<div class="table-responsive">
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                <th class="align-middle">Kategori Barang</th>
                <th class="align-middle">Kode Barang</th>
                <th class="align-middle">Nama Barang</th>
                <th class="align-middle">Merk Barang</th>
                <th class="align-middle">Tipe Barang</th>
                <th class="align-middle">Tahun Barang</th>
                <th class="align-middle">Jenis</th>
                <th class="align-middle">Penyusutan</th>
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($masterBarang as $row)
            <tr>
                <td>{{ $loop->iteration + ($masterBarang->currentPage() - 1) * $masterBarang->perPage() }}</td>
                <td>{{ $row?->kategori->nama_kategori_barang }}</td>
                <td>{{ $row?->kode_barang }}</td>
                <td>{{ $row?->nama_barang }}</td>
                <td>{{ $row?->merk_barang }}</td>
                <td>{{ $row?->tipe_barang }}</td>
                <td>{{ $row?->tahun_barang }}</td>
                <td>
                    @if ($row->isPerlengkapan())
                        <span class="badge bg-label-info">Perlengkapan</span>
                    @else
                        <span class="badge bg-label-primary">Peralatan</span>
                    @endif
                    @if ($row->butuh_perawatan)
                        <span class="badge bg-label-warning" title="Butuh perawatan">Perawatan</span>
                    @endif
                </td>
                <td>
                    @if ($row->isPerlengkapan())
                        <span class="text-muted">Habis pakai (nilai 0 saat dipakai)</span>
                    @elseif ($row->disusutkan)
                        {{ $row->masa_pemakaian_label }}, turun tiap {{ $row->interval_penyusutan_tahun }} tahun
                    @else
                        <span class="text-muted">Tidak disusutkan</span>
                    @endif
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        @can('master-barang view')
                        <div class="me-1">
                            <a href="{{ route('master-barang.show', $row) }}"
                                class="btn btn-icon btn-outline-info btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Detail" data-bs-placement="top">
                                <span class="bx bx-show"></span>
                            </a>
                        </div>
                        @endcan
                        @can('master-barang edit')
                        <div class="me-1">
                            <a href="{{ route('master-barang.edit', $row) }}"
                                class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Edit" data-bs-placement="top">
                                <span class="bx bx-pencil"></span>
                            </a>
                        </div>
                        @endcan
                        @can('master-barang delete')
                        <form action="{{ route('master-barang.destroy', $row) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-input.confirm-button text="Data master barang ini akan dihapus!" positive="Ya, hapus!"
                                icon="info" class="btn btn-icon btn-outline-danger btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Hapus" data-bs-placement="top">
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
    {!! $masterBarang->withQueryString()->links() !!}
</div>