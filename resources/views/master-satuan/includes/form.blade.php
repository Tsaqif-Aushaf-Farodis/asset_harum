<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="kode_satuan" class="form-label">Kode Satuan</label>
                    <input 
                        type="text" 
                        name="kode_satuan" 
                        class="form-control {{ $errors->has('kode_satuan') ? 'is-invalid' : '' }}" 
                        id="kode_satuan" 
                        value="{{ old('kode_satuan', $masterSatuan?->kode_satuan) }}" 
                        placeholder="Masukkan Kode Satuan" />
                    @error('kode_satuan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="nama_satuan" class="form-label">Nama Satuan</label>
                    <input 
                        type="text" 
                        name="nama_satuan" 
                        class="form-control {{ $errors->has('nama_satuan') ? 'is-invalid' : '' }}" 
                        id="nama_satuan" 
                        value="{{ old('nama_satuan', $masterSatuan?->nama_satuan) }}" 
                        placeholder="Masukkan Nama Satuan" />
                    @error('nama_satuan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>