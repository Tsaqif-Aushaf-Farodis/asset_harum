<div class="row">
    <div class="col-md-6 mb-3">
        <label for="pengadaan_barang_id" class="form-label">Aset <span class="text-danger">*</span></label>
        <select name="pengadaan_barang_id" id="pengadaan_barang_id" class="form-select" required>
            <option value="">Pilih Aset</option>
            @foreach($pengadaanBarang as $barang)
                <option value="{{ $barang->id }}" {{ old('pengadaan_barang_id', $mutasiAset->pengadaan_barang_id ?? '') == $barang->id ? 'selected' : '' }}>
                    {{ $barang->kode_inventaris }} ({{ $barang->barang->nama_barang }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="jenis_mutasi" class="form-label">Jenis Mutasi <span class="text-danger">*</span></label>
        <select name="jenis_mutasi" id="jenis_mutasi" class="form-select" required>
            <option value="">Pilih Jenis Mutasi</option>
            <option value="pindah_lokasi" {{ old('jenis_mutasi', $mutasiAset->jenis_mutasi ?? '') == 'pindah_lokasi' ? 'selected' : '' }}>Pindah Lokasi</option>
            <option value="ubah_pengguna" {{ old('jenis_mutasi', $mutasiAset->jenis_mutasi ?? '') == 'ubah_pengguna' ? 'selected' : '' }}>Ubah Pengguna</option>
            <option value="non_aktif" {{ old('jenis_mutasi', $mutasiAset->jenis_mutasi ?? '') == 'non_aktif' ? 'selected' : '' }}>Non Aktif</option>
            <option value="barang_keluar" {{ old('jenis_mutasi', $mutasiAset->jenis_mutasi ?? '') == 'barang_keluar' ? 'selected' : '' }}>Barang Keluar</option>
            <option value="penghapusan" {{ old('jenis_mutasi', $mutasiAset->jenis_mutasi ?? '') == 'penghapusan' ? 'selected' : '' }}>Penghapusan</option>
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="tanggal_mutasi" class="form-label">Tanggal Mutasi <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_mutasi" id="tanggal_mutasi" class="form-control" 
               value="{{ old('tanggal_mutasi', $mutasiAset->tanggal_mutasi ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3" id="lokasi_asal_group">
        <label for="lokasi_asal_id" class="form-label">Lokasi Asal</label>
        <select name="lokasi_asal_id" id="lokasi_asal_id" class="form-select">
            <option value="">Pilih Lokasi</option>
            @foreach($lokasi as $lok)
                <option value="{{ $lok->id }}" {{ old('lokasi_asal_id', $mutasiAset->lokasi_asal_id ?? '') == $lok->id ? 'selected' : '' }}>
                    {{ $lok->nama_sub_lokasi }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3" id="lokasi_tujuan_group" style="display:none;">
        <label for="lokasi_tujuan_id" class="form-label">Lokasi Tujuan</label>
        <select name="lokasi_tujuan_id" id="lokasi_tujuan_id" class="form-select">
            <option value="">Pilih Lokasi</option>
            @foreach($lokasi as $lok)
                <option value="{{ $lok->id }}" {{ old('lokasi_tujuan_id', $mutasiAset->lokasi_tujuan_id ?? '') == $lok->id ? 'selected' : '' }}>
                    {{ $lok->nama_sub_lokasi }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3" id="pengguna_asal_group" style="display:none;">
        <label for="pengguna_asal" class="form-label">Pengguna Asal</label>
        <input type="text" name="pengguna_asal" id="pengguna_asal" class="form-control" 
               value="{{ old('pengguna_asal', $mutasiAset->pengguna_asal ?? '') }}">
    </div>

    <div class="col-md-6 mb-3" id="pengguna_tujuan_group" style="display:none;">
        <label for="pengguna_tujuan" class="form-label">Pengguna Tujuan</label>
        <input type="text" name="pengguna_tujuan" id="pengguna_tujuan" class="form-control" 
               value="{{ old('pengguna_tujuan', $mutasiAset->pengguna_tujuan ?? '') }}">
    </div>

    <div class="col-md-12 mb-3">
        <label for="alasan" class="form-label">Alasan <span class="text-danger">*</span></label>
        <textarea name="alasan" id="alasan" class="form-control" rows="3" required>{{ old('alasan', $mutasiAset->alasan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $mutasiAset->keterangan ?? '') }}</textarea>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        function toggleFields() {
            const jenisMutasi = $('#jenis_mutasi').val();
            
            // Hide all conditional fields
            $('#lokasi_asal_group, #lokasi_tujuan_group, #pengguna_asal_group, #pengguna_tujuan_group').hide();
            
            if (jenisMutasi === 'pindah_lokasi') {
                $('#lokasi_asal_group, #lokasi_tujuan_group').show();
            } else if (jenisMutasi === 'ubah_pengguna') {
                $('#pengguna_asal_group, #pengguna_tujuan_group').show();
            }
        }

        $('#jenis_mutasi').on('change', toggleFields);
        toggleFields(); // Initial call
    });
</script>
@endpush
