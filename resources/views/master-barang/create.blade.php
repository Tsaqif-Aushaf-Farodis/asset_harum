<x-layout.app title="Tambah Master Barang" activeMenu="master-barang.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Master Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Barang', 'url' => route('master-barang.index')],
            ['label' => 'Tambah Master Barang'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-barang.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('master-barang.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>