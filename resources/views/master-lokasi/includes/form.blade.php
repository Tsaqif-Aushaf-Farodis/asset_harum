<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="kode_lokasi" class="form-label">Kode Lokasi</label>
                    <input 
                        type="text" 
                        name="kode_lokasi" 
                        class="form-control {{ $errors->has('kode_lokasi') ? 'is-invalid' : '' }}" 
                        id="kode_lokasi" 
                        value="{{ old('kode_lokasi', $masterLokasi?->kode_lokasi) }}" 
                        placeholder="Masukkan Kode Lokasi" />
                    @error('kode_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="nama_lokasi" class="form-label">Nama Lokasi</label>
                    <input 
                        type="text" 
                        name="nama_lokasi" 
                        class="form-control {{ $errors->has('nama_lokasi') ? 'is-invalid' : '' }}" 
                        id="nama_lokasi" 
                        value="{{ old('nama_lokasi', $masterLokasi?->nama_lokasi) }}" 
                        placeholder="Masukkan Nama Lokasi" />
                    @error('nama_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="alamat_lokasi" class="form-label">Alamat Lokasi</label>
                    <input 
                        type="text" 
                        name="alamat_lokasi" 
                        class="form-control {{ $errors->has('alamat_lokasi') ? 'is-invalid' : '' }}" 
                        id="alamat_lokasi" 
                        value="{{ old('alamat_lokasi', $masterLokasi?->alamat_lokasi) }}" 
                        placeholder="Masukkan Alamat Lokasi" />
                    @error('alamat_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="telepon_lokasi" class="form-label">Telepon Lokasi</label>
                    <input 
                        type="text" 
                        name="telepon_lokasi" 
                        class="form-control {{ $errors->has('telepon_lokasi') ? 'is-invalid' : '' }}" 
                        id="telepon_lokasi" 
                        value="{{ old('telepon_lokasi', $masterLokasi?->telepon_lokasi) }}" 
                        placeholder="Masukkan Telepon Lokasi" />
                    @error('telepon_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>