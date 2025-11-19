<x-layout.app title="Tambah Data Kendaraan" activeMenu="kendaraan.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Data Kendaraan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Kendaraan', 'url' => route('kendaraan.index')],
            ['label' => 'Tambah Data Kendaraan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('kendaraan.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('kendaraan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
