<x-layout.app title="Detail Pengadaan Barang" activeMenu="pengadaan-barang.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang', 'url' => route('pengadaan-barang.index')],
            ['label' => 'Detail Pengadaan Barang'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('pengadaan-barang view')
                        <a href="{{ route('pengadaan-barang.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('pengadaan-barang edit')
                        <a href="{{ route('pengadaan-barang.edit', $pengadaanBarang) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('pengadaan-barang delete')
                            <form action="{{ route('pengadaan-barang.destroy', $pengadaanBarang) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data pengadaan barang ini akan dihapus!"
                                    positive="Ya, hapus!" icon="info"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Hapus"
                                    data-bs-placement="top">
                                    <i class="bx bx-trash me-1"></i>Hapus
                                </x-input.confirm-button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="row g-3">
                    
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Kode Inventaris</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->kode_inventaris }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Barang Id</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->barang_id }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Lokasi Id</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->lokasi_id }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Sumber</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->sumber }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Status Id</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->status_id }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Tanggal Pengadaan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->tanggal_pengadaan }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Jumlah</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->jumlah }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Satuan Id</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->satuan_id }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Harga Satuan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->harga_satuan }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Total Harga</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->total_harga }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Keterangan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $pengadaanBarang->keterangan }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
