<x-layout.app title="Tambah Mutasi Aset" activeMenu="mutasi-aset.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Mutasi Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Mutasi Aset', 'url' => route('mutasi-aset.index')],
            ['label' => 'Tambah Mutasi Aset'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('mutasi-aset.store') }}" method="POST" role="form">
                    @csrf
                    @method('POST')

                    @include('mutasi-aset.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('mutasi-aset.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
