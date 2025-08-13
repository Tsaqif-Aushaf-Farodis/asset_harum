<x-layout.app title="Edit Data Kendaraan" activeMenu="kendaraan.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Edit Data Kendaraan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Kendaraan', 'url' => route('kendaraan.index')],
            ['label' => 'Edit Data Kendaraan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('kendaraan.update', $kendaraan) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @php
                        $pengadaanBarang = $kendaraan;
                        $kendaraanDetail = $kendaraan->kendaraanDetail;
                    @endphp

                    @include('kendaraan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('kendaraan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
