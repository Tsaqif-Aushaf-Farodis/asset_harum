<div class="row">
    <div class="col-md-6">
        <!-- Informasi Pengadaan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informasi Pengadaan</h6>
            </div>
            <div class="card-body">
                @if(isset($kendaraan) && $kendaraan->exists)
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
        <!-- Detail Kendaraan -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Detail Kendaraan</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="merk" class="form-label">Merk</label>
                    <input type="text" name="merk" class="form-control {{ $errors->has('merk') ? 'is-invalid' : '' }}"
                        id="merk" value="{{ old('merk', $kendaraanDetail?->merk) }}" placeholder="Masukkan Merk Kendaraan" />
                    @error('merk')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe</label>
                    <input type="text" name="tipe" class="form-control {{ $errors->has('tipe') ? 'is-invalid' : '' }}"
                        id="tipe" value="{{ old('tipe', $kendaraanDetail?->tipe) }}" placeholder="Masukkan Tipe Kendaraan" />
                    @error('tipe')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="tahun_perakitan" class="form-label">Tahun Perakitan</label>
                    <input type="number" name="tahun_perakitan" class="form-control {{ $errors->has('tahun_perakitan') ? 'is-invalid' : '' }}"
                        id="tahun_perakitan" value="{{ old('tahun_perakitan', $kendaraanDetail?->tahun_perakitan) }}" 
                        placeholder="Masukkan Tahun Perakitan" min="1900" max="{{ date('Y') }}" />
                    @error('tahun_perakitan')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="no_polisi" class="form-label">Nomor Polisi</label>
                    <input type="text" name="no_polisi" class="form-control {{ $errors->has('no_polisi') ? 'is-invalid' : '' }}"
                        id="no_polisi" value="{{ old('no_polisi', $kendaraanDetail?->no_polisi) }}" placeholder="Masukkan Nomor Polisi" />
                    @error('no_polisi')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="no_rangka" class="form-label">Nomor Rangka</label>
                    <input type="text" name="no_rangka" class="form-control {{ $errors->has('no_rangka') ? 'is-invalid' : '' }}"
                        id="no_rangka" value="{{ old('no_rangka', $kendaraanDetail?->no_rangka) }}" placeholder="Masukkan Nomor Rangka" />
                    @error('no_rangka')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="no_mesin" class="form-label">Nomor Mesin</label>
                    <input type="text" name="no_mesin" class="form-control {{ $errors->has('no_mesin') ? 'is-invalid' : '' }}"
                        id="no_mesin" value="{{ old('no_mesin', $kendaraanDetail?->no_mesin) }}" placeholder="Masukkan Nomor Mesin" />
                    @error('no_mesin')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="kapasitas_cc" class="form-label">Kapasitas (CC)</label>
                    <input type="number" name="kapasitas_cc" class="form-control {{ $errors->has('kapasitas_cc') ? 'is-invalid' : '' }}"
                        id="kapasitas_cc" value="{{ old('kapasitas_cc', $kendaraanDetail?->kapasitas_cc) }}" placeholder="Masukkan Kapasitas CC" />
                    @error('kapasitas_cc')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="warna" class="form-label">Warna</label>
                    <input type="text" name="warna" class="form-control {{ $errors->has('warna') ? 'is-invalid' : '' }}"
                        id="warna" value="{{ old('warna', $kendaraanDetail?->warna) }}" placeholder="Masukkan Warna Kendaraan" />
                    @error('warna')<small class="invalid-feedback">{{ $message }}</small>@enderror
                </div>

                <div class="mb-3">
                    <label for="kondisi" class="form-label">Kondisi Kendaraan</label>
                    <x-input.select2 name="kondisi" id="kondisi"
                        class="form-control {{ $errors->has('kondisi') ? 'is-invalid' : '' }}" placeholder="Pilih Kondisi"
                        :options="array (
                        'Baik' => 'Baik',
                        'Rusak Ringan' => 'Rusak Ringan',
                        'Rusak Sedang' => 'Rusak Sedang',
                        'Rusak Berat' => 'Rusak Berat',
                        )" selected="{{ old('kondisi', $kendaraanDetail?->kondisi) }}" />
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
