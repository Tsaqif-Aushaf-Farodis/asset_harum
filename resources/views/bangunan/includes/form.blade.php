<div class="row">
    <div class="col-md-6">
        <!-- Informasi Pengadaan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informasi Pengadaan</h6>
            </div>
            <div class="card-body">
                @if(isset($bangunan) && $bangunan->exists)
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
        <!-- Detail Bangunan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Detail Bangunan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control {{ $errors->has('alamat') ? 'is-invalid' : '' }}"
                        id="alamat" rows="3" placeholder="Masukkan Alamat Bangunan">{{ old('alamat', $bangunanDetail?->alamat) }}</textarea>
                    @error('alamat')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="luas" class="form-label">Luas (m²)</label>
                    <input type="number" step="0.01" name="luas" class="form-control {{ $errors->has('luas') ? 'is-invalid' : '' }}"
                        id="luas" value="{{ old('luas', $bangunanDetail?->luas) }}" placeholder="Masukkan Luas Bangunan" />
                    @error('luas')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="jumlah_lantai" class="form-label">Jumlah Lantai</label>
                    <input type="number" name="jumlah_lantai" class="form-control {{ $errors->has('jumlah_lantai') ? 'is-invalid' : '' }}"
                        id="jumlah_lantai" value="{{ old('jumlah_lantai', $bangunanDetail?->jumlah_lantai) }}" placeholder="Masukkan Jumlah Lantai" />
                    @error('jumlah_lantai')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="bahan_bangunan" class="form-label">Bahan Bangunan</label>
                    <x-input.select2 name="bahan_bangunan" id="bahan_bangunan"
                        class="form-control {{ $errors->has('bahan_bangunan') ? 'is-invalid' : '' }}" placeholder="Pilih Bahan Bangunan"
                        :options="array (
                        'Beton Bertulang' => 'Beton Bertulang',
                        'Batu Bata' => 'Batu Bata',
                        'Batako' => 'Batako',
                        'Kayu' => 'Kayu',
                        'Bambu' => 'Bambu',
                        'Semi Permanen' => 'Semi Permanen',
                        'Lainnya' => 'Lainnya',
                        )" selected="{{ old('bahan_bangunan', $bangunanDetail?->bahan_bangunan) }}" />
                    @error('bahan_bangunan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="nomor_imb" class="form-label">Nomor IMB</label>
                    <input type="text" name="nomor_imb" class="form-control {{ $errors->has('nomor_imb') ? 'is-invalid' : '' }}"
                        id="nomor_imb" value="{{ old('nomor_imb', $bangunanDetail?->nomor_imb) }}" placeholder="Masukkan Nomor IMB" />
                    @error('nomor_imb')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="tanggal_imb" class="form-label">Tanggal IMB</label>
                    <x-input.daterangepicker name1="tanggal_imb"
                        value1="{{ old('tanggal_imb', $bangunanDetail?->tanggal_imb) }}"
                        placeholder="Pilih Tanggal IMB" opens="right" singleDatePicker="true" :ranges="false" />
                    @error('tanggal_imb')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="kondisi" class="form-label">Kondisi Bangunan</label>
                    <x-input.select2 name="kondisi" id="kondisi"
                        class="form-control {{ $errors->has('kondisi') ? 'is-invalid' : '' }}" placeholder="Pilih Kondisi"
                        :options="array (
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Sedang' => 'Rusak Sedang',
                        'Rusak Berat' => 'Rusak Berat',
                        )" selected="{{ old('kondisi', $bangunanDetail?->kondisi) }}" />
                    @error('kondisi')<small class="invalid-feedback">{{ $message }}</small>@enderror
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
