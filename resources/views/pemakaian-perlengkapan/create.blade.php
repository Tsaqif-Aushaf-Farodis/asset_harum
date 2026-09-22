<x-layout.app title="Catat Pemakaian Perlengkapan" activeMenu="pemakaian-perlengkapan.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Catat Pemakaian Perlengkapan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pemakaian Perlengkapan', 'url' => route('pemakaian-perlengkapan.index')],
            ['label' => 'Catat Pemakaian'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                @if (empty($barangList))
                    <div class="alert alert-warning">
                        Belum ada perlengkapan dengan stok tersedia. Catat dulu pengadaan barang berjenis Perlengkapan.
                    </div>
                @endif

                <form action="{{ route('pemakaian-perlengkapan.store') }}" method="POST" role="form">
                    @csrf

                    <div class="mb-4">
                        <label for="barang_id" class="form-label">Barang (sisa stok)</label>
                        <x-input.select2 name="barang_id" id="barang_id"
                            class="form-control {{ $errors->has('barang_id') ? 'is-invalid' : '' }}"
                            placeholder="Pilih Perlengkapan" :options="$barangList"
                            selected="{{ old('barang_id') }}" />
                        @error('barang_id')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" min="1" name="jumlah" id="jumlah"
                            class="form-control {{ $errors->has('jumlah') ? 'is-invalid' : '' }}"
                            value="{{ old('jumlah') }}" placeholder="Jumlah yang dipakai" />
                        <small class="text-muted">Bila melebihi satu pembelian, stok diambil dari pembelian yang paling lama lebih dulu.</small>
                        @error('jumlah')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="tanggal_pemakaian" class="form-label">Tanggal Pemakaian</label>
                        <input type="date" name="tanggal_pemakaian" id="tanggal_pemakaian" max="{{ date('Y-m-d') }}"
                            class="form-control {{ $errors->has('tanggal_pemakaian') ? 'is-invalid' : '' }}"
                            value="{{ old('tanggal_pemakaian', date('Y-m-d')) }}" />
                        @error('tanggal_pemakaian')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="lokasi_id" class="form-label">Dipakai di Lokasi</label>
                        <x-input.select2 name="lokasi_id" id="lokasi_id"
                            class="form-control {{ $errors->has('lokasi_id') ? 'is-invalid' : '' }}"
                            placeholder="Pilih Lokasi" :options="$lokasiList"
                            selected="{{ old('lokasi_id') }}" />
                        @error('lokasi_id')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="pemakai" class="form-label">Nama Pemakai</label>
                        <input type="text" name="pemakai" id="pemakai" maxlength="150"
                            class="form-control {{ $errors->has('pemakai') ? 'is-invalid' : '' }}"
                            value="{{ old('pemakai') }}" placeholder="Nama orang yang memakai" />
                        @error('pemakai')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    </div>
                    <div class="mb-4">
                        <label for="keperluan" class="form-label">Keperluan (opsional)</label>
                        <textarea name="keperluan" id="keperluan" rows="3"
                            class="form-control {{ $errors->has('keperluan') ? 'is-invalid' : '' }}">{{ old('keperluan') }}</textarea>
                        @error('keperluan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2" @disabled(empty($barangList))>Simpan</button>
                        <a href="{{ route('pemakaian-perlengkapan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
