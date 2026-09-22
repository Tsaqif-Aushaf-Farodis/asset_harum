<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="barang_id" class="form-label">Nama Barang</label>
            <x-input.select2 name="barang_id" id="barang_id"
                class="form-control {{ $errors->has('barang_id') ? 'is-invalid' : '' }}" placeholder="Pilih Barang"
                :options="$barangList" selected="{{ old('barang_id', $pengadaanBarang?->barang_id) }}" />
            @error('barang_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="lokasi_id" class="form-label">Lokasi</label>
            <x-input.select2 name="lokasi_id" id="lokasi_id"
                class="form-control {{ $errors->has('lokasi_id') ? 'is-invalid' : '' }}" placeholder="Pilih Lokasi"
                :options="$lokasiList" selected="{{ old('lokasi_id', $pengadaanBarang?->lokasi_id) }}" />
            @error('lokasi_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="sumber" class="form-label">Sumber</label>
            <input type="text" name="sumber" class="form-control {{ $errors->has('sumber') ? 'is-invalid' : '' }}"
                id="sumber" value="{{ old('sumber', $pengadaanBarang?->sumber) }}" placeholder="Masukkan Sumber" />
            @error('sumber')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="tanggal_pengadaan" class="form-label">Tanggal Pengadaan</label>
            <x-input.daterangepicker name1="tanggal_pengadaan"
                value1="{{ old('tanggal_pengadaan', $pengadaanBarang?->tanggal_pengadaan) }}"
                placeholder="Pilih Tanggal Pengadaan" opens="right" singleDatePicker="true" :ranges="false" />
            @error('tanggal_pengadaan')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
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
        <div class="mb-4" id="blok-kondisi">
            <label for="status_id" class="form-label">Kondisi</label>
            <x-input.select2 name="status_id" id="status_id"
                class="form-control {{ $errors->has('status_id') ? 'is-invalid' : '' }}" placeholder="Pilih Status"
                :options="$statusList" selected="{{ old('status_id', $pengadaanBarang?->status_id) }}" />
            @error('status_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="jumlah" class="form-label">Jumlah</label>
            <x-input.currency name="jumlah" id="jumlah" value="{{ old('jumlah', $pengadaanBarang?->jumlah) }}"
                placeholder="Masukkan Jumlah"
                class="form-control text-end {{ $errors->has('jumlah') ? 'is-invalid' : '' }}" />
            @error('jumlah')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4">
            <label for="satuan_id" class="form-label">Satuan</label>
            <x-input.select2 name="satuan_id" id="satuan_id"
                class="form-control {{ $errors->has('satuan_id') ? 'is-invalid' : '' }}" placeholder="Pilih Satuan"
                :options="$satuanList" selected="{{ old('satuan_id', $pengadaanBarang?->satuan_id) }}" />
            @error('satuan_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4">
            <label for="harga_satuan" class="form-label">Harga Satuan</label>
            <x-input.currency name="harga_satuan" id="harga_satuan"
                value="{{ old('harga_satuan', $pengadaanBarang?->harga_satuan) }}" placeholder="Masukkan Harga Satuan"
                class="form-control text-end {{ $errors->has('harga_satuan') ? 'is-invalid' : '' }}" />
            @error('harga_satuan')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4">
            <label for="total_harga" class="form-label">Total Harga</label>
            <x-input.currency name="total_harga" id="total_harga"
                value="{{ old('total_harga', $pengadaanBarang?->total_harga) }}" placeholder="Total Harga"
                class="form-control text-end {{ $errors->has('total_harga') ? 'is-invalid' : '' }}" readonly />
            <input type="hidden" name="total_harga" id="total_harga_hidden"
                value="{{ old('total_harga', $pengadaanBarang?->total_harga) }}" />
            @error('total_harga')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4" id="blok-disusutkan-aset" style="display:none">
            <label for="disusutkan" class="form-label">Penyusutan Aset Ini</label>
            @php
                $disusutkanNilai = old('disusutkan', is_null($pengadaanBarang?->disusutkan) ? '' : (int) $pengadaanBarang->disusutkan);
            @endphp
            <select name="disusutkan" id="disusutkan" class="form-select {{ $errors->has('disusutkan') ? 'is-invalid' : '' }}">
                <option value="" @selected($disusutkanNilai === '' || $disusutkanNilai === null)>Ikut Master Barang</option>
                <option value="1" @selected((string) $disusutkanNilai === '1')>Disusutkan</option>
                <option value="0" @selected((string) $disusutkanNilai === '0')>Tidak disusutkan</option>
            </select>
            <small class="text-muted" id="info-disusutkan-master"></small>
            @error('disusutkan')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4">
            <label for="anggaran_id" class="form-label">Anggaran (opsional)</label>
            <x-input.select2 name="anggaran_id" id="anggaran_id"
                class="form-control {{ $errors->has('anggaran_id') ? 'is-invalid' : '' }}"
                placeholder="Pilih Anggaran" clearable="true" :options="$anggaranList"
                selected="{{ old('anggaran_id', $pengadaanBarang?->anggaran_id) }}" />
            <small class="text-muted">Terisi otomatis dari tahun pengadaan dan lokasi bila anggarannya tersedia.</small>
            @error('anggaran_id')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
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

            const barangMeta = @json($barangMeta);
            const lokasiInduk = @json($lokasiInduk);
            const anggaranMap = @json($anggaranMap);

            function sinkronJenis() {
                const meta = barangMeta[$('#barang_id').val()];
                const perlengkapan = !!meta && meta.jenis === 'perlengkapan';
                $('#blok-kondisi').toggle(!perlengkapan);
                $('#blok-disusutkan-aset').toggle(!!meta && !perlengkapan);
                if (meta && !perlengkapan) {
                    $('#info-disusutkan-master').text('Di Master Barang: ' + (meta.disusutkan ? 'disusutkan' : 'tidak disusutkan') + '.');
                }
            }

            function pilihAnggaranOtomatis() {
                if ($('#anggaran_id').val()) return; // jangan menimpa pilihan pengguna
                const tanggal = $('#tanggal_pengadaan').val() || '';
                const lokasi = lokasiInduk[$('#lokasi_id').val()];
                const id = anggaranMap[tanggal.substring(0, 4) + '-' + lokasi];
                if (id) $('#anggaran_id').val(id).trigger('change');
            }

            $(document).ready(function() {
                updateTotalHarga();
                sinkronJenis();
                $('#barang_id').on('change', sinkronJenis);
                $('#lokasi_id').on('change', pilihAnggaranOtomatis);
                $(document).on('apply.daterangepicker', '.date-range-picker-wrapper input[type=text]', function () {
                    setTimeout(pilihAnggaranOtomatis, 50);
                });
                @if (!$pengadaanBarang?->exists)
                    setTimeout(pilihAnggaranOtomatis, 300);
                @endif
            });
        </script>
        @endpush

    </div>
</div>