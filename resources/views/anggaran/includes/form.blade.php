<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" name="tahun" id="tahun"
                class="form-control {{ $errors->has('tahun') ? 'is-invalid' : '' }}"
                value="{{ old('tahun', $anggaran?->tahun) }}" placeholder="Contoh: {{ date('Y') }}" />
            @error('tahun')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="lokasi_id" class="form-label">Lokasi (Unit)</label>
            <x-input.select2 name="lokasi_id" id="lokasi_id"
                class="form-control {{ $errors->has('lokasi_id') ? 'is-invalid' : '' }}"
                placeholder="Pilih Lokasi" :options="$lokasiList"
                selected="{{ old('lokasi_id', $anggaran?->lokasi_id) }}" />
            <small class="text-muted">Satu anggaran per tahun untuk setiap lokasi/unit.</small>
            @error('lokasi_id')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="pagu" class="form-label">Pagu Anggaran</label>
            <x-input.currency name="pagu" id="pagu" value="{{ old('pagu', $anggaran?->pagu) }}"
                placeholder="Masukkan Pagu"
                class="form-control text-end {{ $errors->has('pagu') ? 'is-invalid' : '' }}" />
            @error('pagu')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="keterangan" class="form-label">Keterangan</label>
            <textarea name="keterangan" id="keterangan" rows="3"
                class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}">{{ old('keterangan', $anggaran?->keterangan) }}</textarea>
            @error('keterangan')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <div class="form-check form-switch">
                <input type="hidden" name="is_active" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" id="is_active" value="1"
                    @checked((bool) old('is_active', $anggaran?->is_active ?? true))>
                <label class="form-check-label" for="is_active">Aktif (bisa dipilih pada pengadaan)</label>
            </div>
        </div>
    </div>
</div>
