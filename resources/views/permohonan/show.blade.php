<x-layout.app title="Detail Permohonan" activeMenu="permohonan.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Permohonan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Permohonan', 'url' => route('permohonan.index')],
            ['label' => 'Detail Permohonan'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('permohonan.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('permohonan view')
                        <a href="{{ route('permohonan.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('permohonan edit')
                        <a href="{{ route('permohonan.edit', $permohonan) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('permohonan delete')
                            <form action="{{ route('permohonan.destroy', $permohonan) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data permohonan ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Bidang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $permohonan->bidang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Tahun Anggaran</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $permohonan->tahun_anggaran }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Unit Kegiatan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $permohonan->unit_kegiatan }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Keterangan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $permohonan->keterangan }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Status</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $permohonan->status }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
