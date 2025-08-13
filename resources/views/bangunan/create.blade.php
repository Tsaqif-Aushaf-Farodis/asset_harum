<x-layout.app title="Tambah Data Bangunan" activeMenu="bangunan.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Data Bangunan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Bangunan', 'url' => route('bangunan.index')],
            ['label' => 'Tambah Data Bangunan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('bangunan.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('bangunan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('bangunan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
