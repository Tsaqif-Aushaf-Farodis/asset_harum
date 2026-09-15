<x-layout.app title="Detail Instansi" activeMenu="instansi.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Instansi" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Instansi', 'url' => route('instansi.index')],
            ['label' => 'Detail Instansi'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('instansi.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('instansi view')
                        <a href="{{ route('instansi.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('instansi edit')
                        <a href="{{ route('instansi.edit', $instansi) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('instansi delete')
                            <form action="{{ route('instansi.destroy', $instansi) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data instansi ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Nama Instansi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $instansi->nama_instansi }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Alamat</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $instansi->alamat }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Telepon</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $instansi->telepon }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Email</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $instansi->email }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Logo</label>
                                </div>
                                <div class="col-md-8 form-group">
                                    : @if ($instansi->logo)
                                        <img src="{{ $instansi->logo_url }}" alt="Logo" style="max-height: 100px;" class="d-block rounded border p-1 mt-1">
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Deskripsi</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $instansi->deskripsi }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
