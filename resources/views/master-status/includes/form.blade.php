<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="kode_status" class="form-label">Kode Status</label>
                    <input 
                        type="text" 
                        name="kode_status" 
                        class="form-control {{ $errors->has('kode_status') ? 'is-invalid' : '' }}" 
                        id="kode_status" 
                        value="{{ old('kode_status', $masterStatus?->kode_status) }}" 
                        placeholder="Masukkan Kode Status" />
                    @error('kode_status')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="nama_status" class="form-label">Nama Status</label>
                    <input 
                        type="text" 
                        name="nama_status" 
                        class="form-control {{ $errors->has('nama_status') ? 'is-invalid' : '' }}" 
                        id="nama_status" 
                        value="{{ old('nama_status', $masterStatus?->nama_status) }}" 
                        placeholder="Masukkan Nama Status" />
                    @error('nama_status')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>