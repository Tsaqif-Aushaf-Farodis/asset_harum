<x-layout.app title="Tambah Pengadaan Barang" activeMenu="pengadaan-barang.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang', 'url' => route('pengadaan-barang.index')],
            ['label' => 'Tambah Pengadaan Barang'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('pengadaan-barang.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('pengadaan-barang.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>