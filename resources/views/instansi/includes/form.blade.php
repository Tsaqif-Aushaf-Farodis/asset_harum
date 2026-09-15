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
                    @if ($instansi?->logo)
                        <div class="mb-2">
                            <img src="{{ $instansi->logo_url }}" alt="Logo" style="max-height: 100px;" class="d-block rounded border p-1">
                        </div>
                    @endif
                    <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="form-control {{ $errors->has('logo') ? 'is-invalid' : '' }}"
                        id="logo" />
                    @error('logo')<small class="invalid-feedback">{{ $message }}</small>@enderror
                    @if ($instansi?->logo)
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah logo.</small>
                    @endif
                </div>
                <div class="mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea
                        name="deskripsi"
                        class="form-control {{ $errors->has('deskripsi') ? 'is-invalid' : '' }}"
                        id="deskripsi"
                        rows="4"
                        placeholder="Masukkan Deskripsi">{{ old('deskripsi', $instansi?->deskripsi) }}</textarea>
                    @error('deskripsi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>