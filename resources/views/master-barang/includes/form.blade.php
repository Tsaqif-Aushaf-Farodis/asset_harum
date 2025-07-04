<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="kategori_barang_id" class="form-label">Kategori Barang</label>
            <x-input.select2 name="kategori_barang_id" id="kategori_barang_id"
                class="form-control {{ $errors->has('kategori_barang_id') ? 'is-invalid' : '' }}"
                placeholder="Pilih Kategori Barang" :options="$kategoriList"
                selected="{{ old('kategori_barang_id', $masterBarang?->kategori_barang_id) }}" />
            @error('kategori_barang_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="kode_barang" class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang"
                class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}" id="kode_barang"
                value="{{ old('kode_barang', $masterBarang?->kode_barang) }}" placeholder="Masukkan Kode Barang" />
            @error('kode_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang"
                class="form-control {{ $errors->has('nama_barang') ? 'is-invalid' : '' }}" id="nama_barang"
                value="{{ old('nama_barang', $masterBarang?->nama_barang) }}" placeholder="Masukkan Nama Barang" />
            @error('nama_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="merk_barang" class="form-label">Merk Barang</label>
            <input type="text" name="merk_barang"
                class="form-control {{ $errors->has('merk_barang') ? 'is-invalid' : '' }}" id="merk_barang"
                value="{{ old('merk_barang', $masterBarang?->merk_barang) }}" placeholder="Masukkan Merk Barang" />
            @error('merk_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="tipe_barang" class="form-label">Tipe Barang</label>
            <input type="text" name="tipe_barang"
                class="form-control {{ $errors->has('tipe_barang') ? 'is-invalid' : '' }}" id="tipe_barang"
                value="{{ old('tipe_barang', $masterBarang?->tipe_barang) }}" placeholder="Masukkan Tipe Barang" />
            @error('tipe_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="tahun_barang" class="form-label">Tahun Barang</label>
            <input type="text" name="tahun_barang"
                class="form-control {{ $errors->has('tahun_barang') ? 'is-invalid' : '' }}" id="tahun_barang"
                value="{{ old('tahun_barang', $masterBarang?->tahun_barang) }}" placeholder="Masukkan Tahun Barang" />
            @error('tahun_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

    </div>
</div>