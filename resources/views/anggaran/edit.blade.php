<x-layout.app title="Perbarui Anggaran" activeMenu="anggaran.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Anggaran" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Anggaran', 'url' => route('anggaran.index')],
            ['label' => 'Perbarui Anggaran'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('anggaran.update', $anggaran) }}" method="POST" role="form">
                    @csrf
                    @method('PUT')

                    @include('anggaran.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('anggaran.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
