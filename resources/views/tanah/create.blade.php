<x-layout.app title="Tambah Data Tanah" activeMenu="tanah.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Data Tanah" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Tanah', 'url' => route('tanah.index')],
            ['label' => 'Tambah Data Tanah'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('tanah.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('tanah.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('tanah.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
