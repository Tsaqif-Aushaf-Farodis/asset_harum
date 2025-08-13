<x-layout.app title="Edit Data Tanah" activeMenu="tanah.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Edit Data Tanah" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Tanah', 'url' => route('tanah.index')],
            ['label' => 'Edit Data Tanah'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('tanah.update', $tanah) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php
                        $pengadaanBarang = $tanah;
                        $tanahDetail = $tanah->tanahDetail;
                    @endphp

                    @include('tanah.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('tanah.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
