<x-layout.app title="Perbarui Master Barang" activeMenu="master-barang.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Master Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Barang', 'url' => route('master-barang.index')],
            ['label' => 'Perbarui Master Barang'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-barang.update', $masterBarang) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('master-barang.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>