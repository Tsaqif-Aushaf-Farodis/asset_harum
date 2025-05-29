<x-layout.app title="Perbarui Kategori Barang" activeMenu="kategori-barang.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Kategori Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Kategori Barang', 'url' => route('kategori-barang.index')],
            ['label' => 'Perbarui Kategori Barang'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('kategori-barang.update', $kategoriBarang) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('kategori-barang.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('kategori-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>