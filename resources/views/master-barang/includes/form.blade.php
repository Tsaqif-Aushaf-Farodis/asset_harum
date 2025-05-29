<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="kode_barang" class="form-label">Kode Barang</label>
                    <input 
                        type="text" 
                        name="kode_barang" 
                        class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}" 
                        id="kode_barang" 
                        value="{{ old('kode_barang', $masterBarang?->kode_barang) }}" 
                        placeholder="Masukkan Kode Barang" />
                    @error('kode_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="nama_barang" class="form-label">Nama Barang</label>
                    <input 
                        type="text" 
                        name="nama_barang" 
                        class="form-control {{ $errors->has('nama_barang') ? 'is-invalid' : '' }}" 
                        id="nama_barang" 
                        value="{{ old('nama_barang', $masterBarang?->nama_barang) }}" 
                        placeholder="Masukkan Nama Barang" />
                    @error('nama_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="kategori_barang_id" class="form-label">Kategori Barang Id</label>
                    <x-input.currency name="kategori_barang_id" id="kategori_barang_id"
                        value="{{ old('kategori_barang_id', $masterBarang?->kategori_barang_id) }}" 
                        placeholder="Masukkan Kategori Barang Id"
                        class="form-control text-end {{ $errors->has('kategori_barang_id') ? 'is-invalid' : '' }}" />
                    @error('kategori_barang_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>