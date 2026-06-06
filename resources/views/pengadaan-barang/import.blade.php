<x-layout.app title="Import Pengadaan Barang" activeMenu="pengadaan-barang" :withError="true">
    <div class="my-5 container-fluid">
        <x-breadcrumb title="Import Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang', 'url' => route('pengadaan-barang.index')],
            ['label' => 'Import'],
        ]" />

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            {{-- LANGKAH 1: Download template --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-download me-1"></i>1. Download Template</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Pilih lokasi & sub lokasi, lalu unduh template. Template berisi sheet
                            <strong>Data Pengadaan</strong> (untuk diisi) dan sheet <strong>Referensi</strong>
                            (daftar barang, satuan, kondisi, serta petunjuk pengisian).
                        </p>
                        <form action="{{ route('pengadaan-barang.import.template') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <x-input.select2 name="dl_lokasi_id" :options="$lokasiList"
                                    placeholder="Pilih Lokasi" clearable="true" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sub Lokasi</label>
                                <x-input.depdrop-select2 name="sub_lokasi_id" placeholder="Pilih Sub Lokasi"
                                    clearable="true" depends="dl_lokasi_id"
                                    url="{{ route('pengadaan-barang.import.sublokasi') }}" />
                            </div>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bx bx-download me-1"></i>Download Template
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- LANGKAH 2: Upload template terisi --}}
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bx bx-upload me-1"></i>2. Upload Template Terisi</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">
                            Pilih sub lokasi yang sama dengan template, lalu upload file yang sudah diisi.
                            Data akan ditampilkan sebagai <strong>pratinjau</strong> sebelum disimpan.
                        </p>
                        <form action="{{ route('pengadaan-barang.import.preview') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Lokasi</label>
                                <x-input.select2 name="up_lokasi_id" :options="$lokasiList"
                                    placeholder="Pilih Lokasi" clearable="true" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Sub Lokasi</label>
                                <x-input.depdrop-select2 name="sub_lokasi_id" placeholder="Pilih Sub Lokasi"
                                    clearable="true" depends="up_lokasi_id"
                                    url="{{ route('pengadaan-barang.import.sublokasi') }}" />
                                @error('sub_lokasi_id')<small class="text-danger">{{ $message }}</small>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">File Template (.xlsx)</label>
                                <input type="file" name="file" accept=".xlsx,.xls"
                                    class="form-control {{ $errors->has('file') ? 'is-invalid' : '' }}" required>
                                @error('file')<small class="invalid-feedback">{{ $message }}</small>@enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-show me-1"></i>Pratinjau Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
