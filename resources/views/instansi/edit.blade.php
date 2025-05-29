<x-layout.app title="Perbarui Instansi" activeMenu="instansi.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Instansi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Instansi', 'url' => route('instansi.index')],
            ['label' => 'Perbarui Instansi'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('instansi.update', $instansi) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('instansi.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('instansi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>