<x-layout.app title="Detail Master Status" activeMenu="master-status.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Master Status" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Status', 'url' => route('master-status.index')],
            ['label' => 'Detail Master Status'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-status.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('master-status view')
                        <a href="{{ route('master-status.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('master-status edit')
                        <a href="{{ route('master-status.edit', $masterStatus) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('master-status delete')
                            <form action="{{ route('master-status.destroy', $masterStatus) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data master status ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Kode Status</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterStatus->kode_status }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nama Status</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterStatus->nama_status }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
