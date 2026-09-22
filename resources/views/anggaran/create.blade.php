<x-layout.app title="Tambah Anggaran" activeMenu="anggaran.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Anggaran" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Anggaran', 'url' => route('anggaran.index')],
            ['label' => 'Tambah Anggaran'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('anggaran.store') }}" method="POST" role="form">
                    @csrf

                    @include('anggaran.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('anggaran.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
