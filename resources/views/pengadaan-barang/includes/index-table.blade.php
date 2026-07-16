<div class="table-responsive">
    <table class="table table-striped" id="data-table" style="height: 100px;">
        <thead>
            <tr>
                <th>No</th>
                <th class="align-middle">QR Code</th>
                <th class="align-middle">Kode Inventaris</th>
                <th class="align-middle">Nama Barang</th>
                <th class="align-middle">Lokasi</th>
                <th class="align-middle">Sumber</th>
                <th class="align-middle">Kondisi</th>
                <th class="align-middle">Status</th>
                <th class="align-middle">Tanggal Pengadaan</th>
                <th class="align-middle">Harga Satuan</th>
                <th class="align-middle">Jumlah</th>
                <th class="align-middle">Total Harga</th>
                {{-- <th class="align-middle">Keterangan</th> --}}
                <th class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pengadaanBarang as $row)
            <tr>
                <td>{{ $loop->iteration + ($pengadaanBarang->currentPage() - 1) * $pengadaanBarang->perPage() }}</td>
                <td>{!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->generate($row?->kode_inventaris) !!}</td>
                <td>{{ $row?->kode_inventaris }}</td>
                <td>{{ $row?->barang->nama_barang }}</td>
                <td>({{ $row?->lokasi->lokasi->nama_lokasi }})<br>
                    {{ $row?->lokasi->nama_sub_lokasi }}</td>
                <td>{{ $row?->sumber }}</td>
                <td>{{ $row?->status }}</td>
                <td>{{ $row?->statusKondisi->nama_status }}</td>
                <td>{{ $row?->tanggal_pengadaan }}</td>
                <td>{{ \App\Helpers\Format::rupiah($row?->harga_satuan) }}</td>
                <td>{{ $row?->jumlah }} {{ $row?->satuan->nama_satuan }}</td>
                <td>{{ \App\Helpers\Format::rupiah($row?->total_harga) }}</td>
                {{-- <td>{{ $row?->keterangan }}</td> --}}
                <td class="text-center">
                    <div class="btn-group" role="group">
                        @can('pengadaan-barang view')
                        <div class="me-1">
                            <a href="{{ route('pengadaan-barang.qr-code.download', $row) }}" target="_blank"
                                class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Preview QR Code" data-bs-placement="top">
                                <span class="bx bx-qr-scan"></span>
                            </a>
                        </div>
                        <div class="me-1">
                            <a href="{{ route('pengadaan-barang.show', $row) }}"
                                class="btn btn-icon btn-outline-info btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Detail" data-bs-placement="top">
                                <span class="bx bx-show"></span>
                            </a>
                        </div>
                        @endcan
                        @can('pengadaan-barang edit')
                        <div class="me-1">
                            <a href="{{ route('pengadaan-barang.edit', $row) }}"
                                class="btn btn-icon btn-outline-primary btn-sm" data-bs-toggle="tooltip"
                                data-bs-title="Edit" data-bs-placement="top">
                                <span class="bx bx-pencil"></span>
                            </a>
                        </div>
                        @endcan
                        @can('pengadaan-barang delete')
                        <form action="{{ route('pengadaan-barang.destroy', $row) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <x-input.confirm-button text="Data pengadaan barang ini akan dihapus!" positive="Ya, hapus!"
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
    {!! $pengadaanBarang->withQueryString()->links() !!}
</div>