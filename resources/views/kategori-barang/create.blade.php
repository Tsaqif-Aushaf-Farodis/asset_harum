<x-layout.app title="Tambah Kategori Barang" activeMenu="kategori-barang.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Kategori Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Kategori Barang', 'url' => route('kategori-barang.index')],
            ['label' => 'Tambah Kategori Barang'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('kategori-barang.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('kategori-barang.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('kategori-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>