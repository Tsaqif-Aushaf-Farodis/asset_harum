<x-layout.app title="Perbarui Permohonan" activeMenu="permohonan.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Permohonan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Permohonan', 'url' => route('permohonan.index')],
            ['label' => 'Perbarui Permohonan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('permohonan.update', $permohonan) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('permohonan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>