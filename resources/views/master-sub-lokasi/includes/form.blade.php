<div class="row">
    <div class="col-md-12">

        <div class="mb-4">
            <label for="lokasi_id" class="form-label">Lokasi</label>
            <x-input.select2 name="lokasi_id" id="lokasi_id"
                class="form-control {{ $errors->has('lokasi_id') ? 'is-invalid' : '' }}" placeholder="Pilih Barang"
                :options="$lokasiList" selected="{{ old('lokasi_id', $masterSubLokasi?->lokasi_id) }}" />
            @error('lokasi_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="kode_sub_lokasi" class="form-label">Kode Sub Lokasi</label>
            <input type="text" name="kode_sub_lokasi"
                class="form-control {{ $errors->has('kode_sub_lokasi') ? 'is-invalid' : '' }}" id="kode_sub_lokasi"
                value="{{ old('kode_sub_lokasi', $masterSubLokasi?->kode_sub_lokasi) }}"
                placeholder="Masukkan Kode Sub Lokasi" />
            @error('kode_sub_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="nama_sub_lokasi" class="form-label">Nama Sub Lokasi</label>
            <input type="text" name="nama_sub_lokasi"
                class="form-control {{ $errors->has('nama_sub_lokasi') ? 'is-invalid' : '' }}" id="nama_sub_lokasi"
                value="{{ old('nama_sub_lokasi', $masterSubLokasi?->nama_sub_lokasi) }}"
                placeholder="Masukkan Nama Sub Lokasi" />
            @error('nama_sub_lokasi')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
    </div>
</div>