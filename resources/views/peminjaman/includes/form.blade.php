<div class="row">
    <div class="col-md-6 mb-3">
        <label for="pengadaan_barang_id" class="form-label">Aset yang Dipinjam <span class="text-danger">*</span></label>
        <select name="pengadaan_barang_id" id="pengadaan_barang_id" class="form-select" required>
            <option value="">Pilih Aset</option>
            @foreach($pengadaanBarang as $barang)
                <option value="{{ $barang->id }}" {{ old('pengadaan_barang_id', $peminjaman->pengadaan_barang_id ?? '') == $barang->id ? 'selected' : '' }}>
                    {{ $barang->kode_inventaris }} - {{ $barang->nama_barang }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6 mb-3">
        <label for="peminjam_id" class="form-label">Peminjam (User)</label>
        <select name="peminjam_id" id="peminjam_id" class="form-select">
            <option value="">Pilih User (Optional)</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ old('peminjam_id', $peminjaman->peminjam_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Pilih user jika peminjam adalah user terdaftar</small>
    </div>

    <div class="col-md-6 mb-3">
        <label for="nama_peminjam" class="form-label">Nama Peminjam <span class="text-danger">*</span></label>
        <input type="text" name="nama_peminjam" id="nama_peminjam" class="form-control" 
               value="{{ old('nama_peminjam', $peminjaman->nama_peminjam ?? '') }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="kontak_peminjam" class="form-label">Kontak Peminjam <span class="text-danger">*</span></label>
        <input type="text" name="kontak_peminjam" id="kontak_peminjam" class="form-control" 
               value="{{ old('kontak_peminjam', $peminjaman->kontak_peminjam ?? '') }}" 
               placeholder="No. HP / Email" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" 
               value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam ?? date('Y-m-d')) }}" required>
    </div>

    <div class="col-md-6 mb-3">
        <label for="tanggal_rencana_kembali" class="form-label">Rencana Tanggal Kembali <span class="text-danger">*</span></label>
        <input type="date" name="tanggal_rencana_kembali" id="tanggal_rencana_kembali" class="form-control" 
               value="{{ old('tanggal_rencana_kembali', $peminjaman->tanggal_rencana_kembali ?? '') }}" required>
    </div>

    <div class="col-md-12 mb-3">
        <label for="keperluan" class="form-label">Keperluan <span class="text-danger">*</span></label>
        <textarea name="keperluan" id="keperluan" class="form-control" rows="3" required>{{ old('keperluan', $peminjaman->keperluan ?? '') }}</textarea>
    </div>

    <div class="col-md-12 mb-3">
        <label for="keterangan" class="form-label">Keterangan</label>
        <textarea name="keterangan" id="keterangan" class="form-control" rows="3">{{ old('keterangan', $peminjaman->keterangan ?? '') }}</textarea>
    </div>
</div>

@push('script')
<script>
    $(document).ready(function() {
        $('#peminjam_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            if (selectedOption.val()) {
                $('#nama_peminjam').val(selectedOption.text());
            }
        });
    });
</script>
@endpush
