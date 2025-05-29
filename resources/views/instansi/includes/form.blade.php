<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="nama_instansi" class="form-label">Nama Instansi</label>
                    <input 
                        type="text" 
                        name="nama_instansi" 
                        class="form-control {{ $errors->has('nama_instansi') ? 'is-invalid' : '' }}" 
                        id="nama_instansi" 
                        value="{{ old('nama_instansi', $instansi?->nama_instansi) }}" 
                        placeholder="Masukkan Nama Instansi" />
                    @error('nama_instansi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input 
                        type="text" 
                        name="alamat" 
                        class="form-control {{ $errors->has('alamat') ? 'is-invalid' : '' }}" 
                        id="alamat" 
                        value="{{ old('alamat', $instansi?->alamat) }}" 
                        placeholder="Masukkan Alamat" />
                    @error('alamat')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="telepon" class="form-label">Telepon</label>
                    <input 
                        type="text" 
                        name="telepon" 
                        class="form-control {{ $errors->has('telepon') ? 'is-invalid' : '' }}" 
                        id="telepon" 
                        value="{{ old('telepon', $instansi?->telepon) }}" 
                        placeholder="Masukkan Telepon" />
                    @error('telepon')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="text" 
                        name="email" 
                        class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" 
                        id="email" 
                        value="{{ old('email', $instansi?->email) }}" 
                        placeholder="Masukkan Email" />
                    @error('email')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="logo" class="form-label">Logo</label>
                    <input 
                        type="text" 
                        name="logo" 
                        class="form-control {{ $errors->has('logo') ? 'is-invalid' : '' }}" 
                        id="logo" 
                        value="{{ old('logo', $instansi?->logo) }}" 
                        placeholder="Masukkan Logo" />
                    @error('logo')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <input 
                        type="text" 
                        name="deskripsi" 
                        class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : '' }}" 
                        id="deskripsi" 
                        value="{{ old('deskripsi', $instansi?->deskripsi) }}" 
                        placeholder="Masukkan Deskripsi" />
                    @error('deskripsi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>