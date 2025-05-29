<x-layout.app title="Perbarui Master Lokasi" activeMenu="master-lokasi.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Master Lokasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Lokasi', 'url' => route('master-lokasi.index')],
            ['label' => 'Perbarui Master Lokasi'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-lokasi.update', $masterLokasi) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('master-lokasi.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('master-lokasi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>