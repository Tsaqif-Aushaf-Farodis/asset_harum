<x-layout.app title="Edit Mutasi Aset" activeMenu="mutasi-aset.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Edit Mutasi Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Mutasi Aset', 'url' => route('mutasi-aset.index')],
            ['label' => 'Edit Mutasi Aset'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('mutasi-aset.update', $mutasiAset) }}" method="POST" role="form">
                    @csrf
                    @method('PUT')

                    @include('mutasi-aset.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('mutasi-aset.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
