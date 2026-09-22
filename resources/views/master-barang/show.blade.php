<x-layout.app title="Detail Master Barang" activeMenu="master-barang.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Master Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Barang', 'url' => route('master-barang.index')],
            ['label' => 'Detail Master Barang'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('master-barang.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('master-barang view')
                        <a href="{{ route('master-barang.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('master-barang edit')
                        <a href="{{ route('master-barang.edit', $masterBarang) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('master-barang delete')
                            <form action="{{ route('master-barang.destroy', $masterBarang) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data master barang ini akan dihapus!"
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
                                    <label for="first-name-horizontal">Kode Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->kode_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nama Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->nama_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Merk Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->merk_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Tipe Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->tipe_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Tahun Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->tahun_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Deskripsi Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->deskripsi_barang }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Kategori Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->kategori->nama_kategori_barang ?? '-' }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Jenis Barang</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->isPerlengkapan() ? 'Perlengkapan (habis pakai)' : 'Peralatan (aset)' }}</div>
                                @if ($masterBarang->isPeralatan())
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Disusutkan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->disusutkan ? 'Ya' : 'Tidak' }}</div>
                                @endif
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Masa Pemakaian</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->masa_pemakaian_label }}</div>
                                @if ($masterBarang->isPeralatan() && $masterBarang->disusutkan)
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Nilai Turun Setiap</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->interval_penyusutan_tahun }} tahun</div>
                                @endif
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Butuh Perawatan</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $masterBarang->butuh_perawatan ? 'Ya' : 'Tidak' }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
