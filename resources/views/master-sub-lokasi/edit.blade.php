<x-layout.app title="Perbarui Master Sub Lokasi" activeMenu="master-sub-lokasi.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Master Sub Lokasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Sub Lokasi', 'url' => route('master-sub-lokasi.index')],
            ['label' => 'Perbarui Master Sub Lokasi'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-sub-lokasi.update', $masterSubLokasi) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('master-sub-lokasi.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('master-sub-lokasi.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>