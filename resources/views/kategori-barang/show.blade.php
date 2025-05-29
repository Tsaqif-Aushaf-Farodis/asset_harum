<x-layout.app title="Detail Kategori Barang" activeMenu="kategori-barang.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Kategori Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Kategori Barang', 'url' => route('kategori-barang.index')],
            ['label' => 'Detail Kategori Barang'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('kategori-barang.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('kategori-barang view')
                        <a href="{{ route('kategori-barang.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('kategori-barang edit')
                        <a href="{{ route('kategori-barang.edit', $kategoriBarang) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('kategori-barang delete')
                            <form action="{{ route('kategori-barang.destroy', $kategoriBarang) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data kategori barang ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Kode Kategori Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $kategoriBarang->kode_kategori_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nama Kategori Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $kategoriBarang->nama_kategori_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Deskripsi Kategori Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $kategoriBarang->deskripsi_kategori_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Status Kategori Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $kategoriBarang->status_kategori_barang }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
