<x-layout.app title="Tambah Instansi" activeMenu="instansi.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Instansi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Instansi', 'url' => route('instansi.index')],
            ['label' => 'Tambah Instansi'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('instansi.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('instansi.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('instansi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>