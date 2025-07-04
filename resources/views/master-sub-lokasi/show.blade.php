<x-layout.app title="Detail Master Sub Lokasi" activeMenu="master-sub-lokasi.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Master Sub Lokasi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Sub Lokasi', 'url' => route('master-sub-lokasi.index')],
            ['label' => 'Detail Master Sub Lokasi'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-sub-lokasi.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('master-sub-lokasi view')
                        <a href="{{ route('master-sub-lokasi.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('master-sub-lokasi edit')
                        <a href="{{ route('master-sub-lokasi.edit', $masterSubLokasi) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('master-sub-lokasi delete')
                            <form action="{{ route('master-sub-lokasi.destroy', $masterSubLokasi) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data master sub lokasi ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Lokasi Id</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterSubLokasi->lokasi_id }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Kode Sub Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterSubLokasi->kode_sub_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nama Sub Lokasi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterSubLokasi->nama_sub_lokasi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Keterangan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterSubLokasi->keterangan }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
