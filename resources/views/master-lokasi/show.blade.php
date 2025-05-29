<x-layout.app title="Detail Master Lokasi" activeMenu="master-lokasi.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Master Lokasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Lokasi', 'url' => route('master-lokasi.index')],
            ['label' => 'Detail Master Lokasi'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-lokasi.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('master-lokasi view')
                        <a href="{{ route('master-lokasi.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('master-lokasi edit')
                        <a href="{{ route('master-lokasi.edit', $masterLokasi) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('master-lokasi delete')
                            <form action="{{ route('master-lokasi.destroy', $masterLokasi) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data master lokasi ini akan dihapus!"
                                    positive="Ya, hapus!" icon="info"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Hapus"
                                    data-bs-placement="top">
                                    <i class="bx bx-trash me-1"></i>Hapus
                                </x-input.confirm-button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="row g-3">
                    
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Kode Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterLokasi->kode_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nama Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterLokasi->nama_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Deskripsi Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterLokasi->deskripsi_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Alamat Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterLokasi->alamat_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Telepon Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterLokasi->telepon_lokasi }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
