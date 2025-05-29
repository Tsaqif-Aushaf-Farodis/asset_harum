<x-layout.app title="Detail Master Satuan" activeMenu="master-satuan.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Master Satuan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Satuan', 'url' => route('master-satuan.index')],
            ['label' => 'Detail Master Satuan'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-satuan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('master-satuan view')
                        <a href="{{ route('master-satuan.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('master-satuan edit')
                        <a href="{{ route('master-satuan.edit', $masterSatuan) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('master-satuan delete')
                            <form action="{{ route('master-satuan.destroy', $masterSatuan) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data master satuan ini akan dihapus!"
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
                    
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
