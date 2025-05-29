<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="kode_kategori_barang" class="form-label">Kode Kategori Barang</label>
                    <input 
                        type="text" 
                        name="kode_kategori_barang" 
                        class="form-control {{ $errors->has('kode_kategori_barang') ? 'is-invalid' : '' }}" 
                        id="kode_kategori_barang" 
                        value="{{ old('kode_kategori_barang', $kategoriBarang?->kode_kategori_barang) }}" 
                        placeholder="Masukkan Kode Kategori Barang" />
                    @error('kode_kategori_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="nama_kategori_barang" class="form-label">Nama Kategori Barang</label>
                    <input 
                        type="text" 
                        name="nama_kategori_barang" 
                        class="form-control {{ $errors->has('nama_kategori_barang') ? 'is-invalid' : '' }}" 
                        id="nama_kategori_barang" 
                        value="{{ old('nama_kategori_barang', $kategoriBarang?->nama_kategori_barang) }}" 
                        placeholder="Masukkan Nama Kategori Barang" />
                    @error('nama_kategori_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="status_kategori_barang" class="form-label">Status Kategori Barang</label>
                    <x-input.select2 
                        name="status_kategori_barang" 
                        id="status_kategori_barang"
                        class="form-control {{ $errors->has('status_kategori_barang') ? 'is-invalid' : '' }}" 
                        placeholder="Pilih Status Kategori Barang"
                        :options="array (
  'aktif' => 'aktif',
  'nonaktif' => 'nonaktif',
)" 
                        selected="{{ old('status_kategori_barang', $kategoriBarang?->status_kategori_barang) }}" />
                    @error('status_kategori_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>