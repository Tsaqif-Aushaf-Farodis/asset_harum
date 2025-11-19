<x-layout.app title="Edit Data Bangunan" activeMenu="bangunan.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Edit Data Bangunan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Bangunan', 'url' => route('bangunan.index')],
            ['label' => 'Edit Data Bangunan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('bangunan.update', $bangunan) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php
                        $pengadaanBarang = $bangunan;
                        $bangunanDetail = $bangunan->bangunanDetail;
                    @endphp

                    @include('bangunan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('bangunan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
