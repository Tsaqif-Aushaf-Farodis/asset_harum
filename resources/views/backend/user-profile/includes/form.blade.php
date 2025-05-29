<div class="row">
    <div class="col-md-12">
        
                <div class="mb-4">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input 
                        type="text" 
                        name="full_name" 
                        class="form-control {{ $errors->has('full_name') ? 'is-invalid' : '' }}" 
                        id="full_name" 
                        value="{{ old('full_name', $userProfile?->full_name) }}" 
                        placeholder="Masukkan Full Name" />
                    @error('full_name')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="avatar_url" class="form-label">Avatar Url</label>
                    <input 
                        type="text" 
                        name="avatar_url" 
                        class="form-control {{ $errors->has('avatar_url') ? 'is-invalid' : '' }}" 
                        id="avatar_url" 
                        value="{{ old('avatar_url', $userProfile?->avatar_url) }}" 
                        placeholder="Masukkan Avatar Url" />
                    @error('avatar_url')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="no_telp" class="form-label">No Telp</label>
                    <input 
                        type="text" 
                        name="no_telp" 
                        class="form-control {{ $errors->has('no_telp') ? 'is-invalid' : '' }}" 
                        id="no_telp" 
                        value="{{ old('no_telp', $userProfile?->no_telp) }}" 
                        placeholder="Masukkan No Telp" />
                    @error('no_telp')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="alamat" class="form-label">Alamat</label>
                    <input 
                        type="text" 
                        name="alamat" 
                        class="form-control {{ $errors->has('alamat') ? 'is-invalid' : '' }}" 
                        id="alamat" 
                        value="{{ old('alamat', $userProfile?->alamat) }}" 
                        placeholder="Masukkan Alamat" />
                    @error('alamat')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                <div class="mb-4">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <x-input.select2 
                        name="jenis_kelamin" 
                        id="jenis_kelamin"
                        class="form-control {{ $errors->has('jenis_kelamin') ? 'is-invalid' : '' }}" 
                        placeholder="Pilih Jenis Kelamin"
                        :options="array (
  'L' => 'L',
  'P' => 'P',
)" 
                        selected="{{ old('jenis_kelamin', $userProfile?->jenis_kelamin) }}" />
                    @error('jenis_kelamin')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
    </div>
</div>