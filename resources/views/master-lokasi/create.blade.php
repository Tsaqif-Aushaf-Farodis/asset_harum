<x-layout.app title="Tambah Master Lokasi" activeMenu="master-lokasi.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Master Lokasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Lokasi', 'url' => route('master-lokasi.index')],
            ['label' => 'Tambah Master Lokasi'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-lokasi.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('master-lokasi.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('master-lokasi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>