<div class="row">
    <div class="col-md-6">
        <!-- Informasi Pengadaan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informasi Pengadaan</h6>
            </div>
            <div class="card-body">
                @if(isset($tanah) && $tanah->exists)
                <div class="mb-3">
                    <label for="kode_inventaris" class="form-label">Kode Inventaris</label>
                    <input type="text" name="kode_inventaris" class="form-control {{ $errors->has('kode_inventaris') ? 'is-invalid' : '' }}"
                        id="kode_inventaris" value="{{ old('kode_inventaris', $pengadaanBarang?->kode_inventaris) }}" placeholder="Masukkan Kode Inventaris" />
                    @error('kode_inventaris')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
                @endif

                <div class="mb-3">
                    <label for="barang_id" class="form-label">Nama Barang</label>
                    <x-input.select2 name="barang_id" id="barang_id"
                        class="form-control {{ $errors->has('barang_id') ? 'is-invalid' : '' }}" placeholder="Pilih Barang"
                        :options="$barangList" selected="{{ old('barang_id', $pengadaanBarang?->barang_id) }}" />
                    @error('barang_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi_id" class="form-label">Lokasi</label>
                    <x-input.select2 name="lokasi_id" id="lokasi_id"
                        class="form-control {{ $errors->has('lokasi_id') ? 'is-invalid' : '' }}" placeholder="Pilih Lokasi"
                        :options="$lokasiList" selected="{{ old('lokasi_id', $pengadaanBarang?->lokasi_id) }}" />
                    @error('lokasi_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="sumber" class="form-label">Sumber</label>
                    <input type="text" name="sumber" class="form-control {{ $errors->has('sumber') ? 'is-invalid' : '' }}"
                        id="sumber" value="{{ old('sumber', $pengadaanBarang?->sumber) }}" placeholder="Masukkan Sumber" />
                    @error('sumber')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_pengadaan" class="form-label">Tanggal Pengadaan</label>
                    <x-input.daterangepicker name1="tanggal_pengadaan"
                        value1="{{ old('tanggal_pengadaan', $pengadaanBarang?->tanggal_pengadaan) }}"
                        placeholder="Pilih Tanggal Pengadaan" opens="right" singleDatePicker="true" :ranges="false" />
                    @error('tanggal_pengadaan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <x-input.select2 name="status" id="status"
                        class="form-control {{ $errors->has('status') ? 'is-invalid' : '' }}" placeholder="Pilih Status"
                        :options="array (
                        'baru' => 'baru',
                        'bekas' => 'bekas',
                        'hibah' => 'hibah',
                        )" selected="{{ old('status', $pengadaanBarang?->status) }}" />
                    @error('status')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="status_id" class="form-label">Kondisi</label>
                    <x-input.select2 name="status_id" id="status_id"
                        class="form-control {{ $errors->has('status_id') ? 'is-invalid' : '' }}" placeholder="Pilih Status"
                        :options="$statusList" selected="{{ old('status_id', $pengadaanBarang?->status_id) }}" />
                    @error('status_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <x-input.currency name="jumlah" id="jumlah" value="{{ old('jumlah', $pengadaanBarang?->jumlah) }}"
                        placeholder="Masukkan Jumlah"
                        class="form-control text-end {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" />
                    @error('jumlah')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="satuan_id" class="form-label">Satuan</label>
                    <x-input.select2 name="satuan_id" id="satuan_id"
                        class="form-control {{ $errors->has('satuan_id') ? 'is-invalid' : '' }}" placeholder="Pilih Satuan"
                        :options="$satuanList" selected="{{ old('satuan_id', $pengadaanBarang?->satuan_id) }}" />
                    @error('satuan_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="harga_satuan" class="form-label">Harga Satuan</label>
                    <x-input.currency name="harga_satuan" id="harga_satuan"
                        value="{{ old('harga_satuan', $pengadaanBarang?->harga_satuan) }}" placeholder="Masukkan Harga Satuan"
                        class="form-control text-end {{ $errors->has('harga_satuan') ? 'is-invalid' : '' }}" />
                    @error('harga_satuan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="total_harga" class="form-label">Total Harga</label>
                    <x-input.currency name="total_harga" id="total_harga"
                        value="{{ old('total_harga', $pengadaanBarang?->total_harga) }}" placeholder="Total Harga"
                        class="form-control text-end {{ $errors->has('total_harga') ? 'is-invalid' : '' }}" readonly />
                    <input type="hidden" name="total_harga" id="total_harga_hidden"
                        value="{{ old('total_harga', $pengadaanBarang?->total_harga) }}" />
                    @error('total_harga')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea name="keterangan" class="form-control {{ $errors->has('keterangan') ? 'is-invalid' : '' }}"
                        id="keterangan" rows="3" placeholder="Masukkan Keterangan">{{ old('keterangan', $pengadaanBarang?->keterangan) }}</textarea>
                    @error('keterangan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Detail Tanah -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Detail Tanah</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="luas" class="form-label">Luas (m²)</label>
                    <input type="number" step="0.01" name="luas" class="form-control {{ $errors->has('luas') ? 'is-invalid' : '' }}"
                        id="luas" value="{{ old('luas', $tanahDetail?->luas) }}" placeholder="Masukkan Luas Tanah" />
                    @error('luas')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="status_tanah" class="form-label">Status Tanah</label>
                    <x-input.select2 name="status_tanah" id="status_tanah"
                        class="form-control {{ $errors->has('status_tanah') ? 'is-invalid' : '' }}" placeholder="Pilih Status Tanah"
                        :options="array (
                        'SHM' => 'SHM (Sertifikat Hak Milik)',
                        'SHGB' => 'SHGB (Sertifikat Hak Guna Bangunan)',
                        'SHGU' => 'SHGU (Sertifikat Hak Guna Usaha)',
                        'SHP' => 'SHP (Sertifikat Hak Pakai)',
                        'Girik' => 'Girik',
                        'Petok D' => 'Petok D',
                        'Lainnya' => 'Lainnya',
                        )" selected="{{ old('status_tanah', $tanahDetail?->status_tanah) }}" />
                    @error('status_tanah')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="sertifikat_nomor" class="form-label">Nomor Sertifikat</label>
                    <input type="text" name="sertifikat_nomor" class="form-control {{ $errors->has('sertifikat_nomor') ? 'is-invalid' : '' }}"
                        id="sertifikat_nomor" value="{{ old('sertifikat_nomor', $tanahDetail?->sertifikat_nomor) }}" placeholder="Masukkan Nomor Sertifikat" />
                    @error('sertifikat_nomor')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="sertifikat_tanggal" class="form-label">Tanggal Sertifikat</label>
                    <x-input.daterangepicker name1="sertifikat_tanggal"
                        value1="{{ old('sertifikat_tanggal', $tanahDetail?->sertifikat_tanggal) }}"
                        placeholder="Pilih Tanggal Sertifikat" opens="right" singleDatePicker="true" :ranges="false" />
                    @error('sertifikat_tanggal')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="penggunaan" class="form-label">Penggunaan</label>
                    <textarea name="penggunaan" class="form-control {{ $errors->has('penggunaan') ? 'is-invalid' : '' }}"
                        id="penggunaan" rows="3" placeholder="Masukkan Penggunaan Tanah">{{ old('penggunaan', $tanahDetail?->penggunaan) }}</textarea>
                    @error('penggunaan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="lokasi_detail" class="form-label">Lokasi Detail</label>
                    <textarea name="lokasi_detail" class="form-control {{ $errors->has('lokasi_detail') ? 'is-invalid' : '' }}"
                        id="lokasi_detail" rows="3" placeholder="Masukkan Detail Lokasi Tanah">{{ old('lokasi_detail', $tanahDetail?->lokasi) }}</textarea>
                    @error('lokasi_detail')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    function parseCurrency(val) {
        if (!val) return 0;
        val = val.replace(/[^0-9.,]/g, '');
        val = val.replace(/\./g, '');
        val = val.replace(',', '.');
        return parseFloat(val) || 0;
    }

    function formatCurrency(num) {
        return num.toLocaleString('id-ID', {minimumFractionDigits: 0});
    }

    function updateTotalHarga() {
        let jumlah = parseCurrency($('#jumlah').val());
        let hargaSatuan = parseCurrency($('#harga_satuan').val());
        let total = jumlah * hargaSatuan;

        $('#total_harga').val(total > 0 ? formatCurrency(total) : '');
        $('#total_harga_hidden').val(total);
    }

    $(document).on('input change', '#jumlah, #harga_satuan', function() {
        updateTotalHarga();
    });

    $(document).ready(function() {
        updateTotalHarga();
    });
</script>
@endpush
