<div class="row">
    <div class="col-md-12">
        <div class="mb-4">
            <label for="kategori_barang_id" class="form-label">Kategori Barang</label>
            <x-input.select2 name="kategori_barang_id" id="kategori_barang_id"
                class="form-control {{ $errors->has('kategori_barang_id') ? 'is-invalid' : '' }}"
                placeholder="Pilih Kategori Barang" :options="$kategoriList"
                selected="{{ old('kategori_barang_id', $masterBarang?->kategori_barang_id) }}" />
            @error('kategori_barang_id')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="kode_barang" class="form-label">Kode Barang</label>
            <input type="text" name="kode_barang"
                class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}" id="kode_barang"
                value="{{ old('kode_barang', $masterBarang?->kode_barang) }}" placeholder="Masukkan Kode Barang" />
            @error('kode_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="nama_barang" class="form-label">Nama Barang</label>
            <input type="text" name="nama_barang"
                class="form-control {{ $errors->has('nama_barang') ? 'is-invalid' : '' }}" id="nama_barang"
                value="{{ old('nama_barang', $masterBarang?->nama_barang) }}" placeholder="Masukkan Nama Barang" />
            @error('nama_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="merk_barang" class="form-label">Merk Barang</label>
            <input type="text" name="merk_barang"
                class="form-control {{ $errors->has('merk_barang') ? 'is-invalid' : '' }}" id="merk_barang"
                value="{{ old('merk_barang', $masterBarang?->merk_barang) }}" placeholder="Masukkan Merk Barang" />
            @error('merk_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="tipe_barang" class="form-label">Tipe Barang</label>
            <input type="text" name="tipe_barang"
                class="form-control {{ $errors->has('tipe_barang') ? 'is-invalid' : '' }}" id="tipe_barang"
                value="{{ old('tipe_barang', $masterBarang?->tipe_barang) }}" placeholder="Masukkan Tipe Barang" />
            @error('tipe_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>
        <div class="mb-4">
            <label for="tahun_barang" class="form-label">Tahun Barang</label>
            <input type="text" name="tahun_barang"
                class="form-control {{ $errors->has('tahun_barang') ? 'is-invalid' : '' }}" id="tahun_barang"
                value="{{ old('tahun_barang', $masterBarang?->tahun_barang) }}" placeholder="Masukkan Tahun Barang" />
            @error('tahun_barang')<small class="invalid-feedback">{{ $message }}</small>@enderror
        </div>

        @php
            $jenisTerkunci = $masterBarang?->exists && $masterBarang->pengadaan()->exists();
            $jenisSaatIni = old('jenis_barang', $masterBarang?->jenis_barang ?: 'peralatan');
        @endphp

        <hr class="my-4">
        <h6 class="mb-3">Jenis &amp; Penyusutan</h6>

        <div class="mb-4">
            <label for="jenis_barang" class="form-label">Jenis Barang</label>
            <select name="jenis_barang" id="jenis_barang"
                class="form-select {{ $errors->has('jenis_barang') ? 'is-invalid' : '' }}"
                @if ($jenisTerkunci) disabled @endif>
                <option value="peralatan" @selected($jenisSaatIni === 'peralatan')>Peralatan (dipakai lebih dari 1 tahun, tercatat sebagai aset)</option>
                <option value="perlengkapan" @selected($jenisSaatIni === 'perlengkapan')>Perlengkapan (barang habis pakai, tercatat sebagai stok)</option>
            </select>
            @if ($jenisTerkunci)
                <input type="hidden" name="jenis_barang" value="{{ $masterBarang->jenis_barang }}">
                <small class="text-muted">Jenis tidak dapat diubah karena barang ini sudah memiliki data pengadaan.</small>
            @endif
            @error('jenis_barang')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
        </div>

        <div class="mb-4" id="blok-disusutkan">
            <div class="form-check form-switch">
                <input type="hidden" name="disusutkan" value="0" class="js-peralatan-only">
                <input class="form-check-input js-peralatan-only" type="checkbox" role="switch" name="disusutkan"
                    id="disusutkan" value="1"
                    @checked((bool) old('disusutkan', $masterBarang?->disusutkan))>
                <label class="form-check-label" for="disusutkan">Barang ini disusutkan (nilainya turun seiring waktu)</label>
            </div>
            <small class="text-muted">Matikan untuk barang yang tidak disusutkan, misalnya tanah. Nilainya akan tetap sebesar harga beli.</small>
        </div>

        <div id="blok-masa">
            <div class="mb-4">
                <label for="masa_pemakaian" class="form-label" id="label-masa">Masa Pemakaian (tahun)</label>
                <input type="number" step="any" min="0" name="masa_pemakaian" id="masa_pemakaian"
                    class="form-control {{ $errors->has('masa_pemakaian') ? 'is-invalid' : '' }}"
                    value="{{ old('masa_pemakaian', $masterBarang?->masa_pemakaian) }}" placeholder="Contoh: 4" />
                <small class="text-muted" id="bantuan-masa"></small>
                @error('masa_pemakaian')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
            </div>

            <div class="mb-4" id="blok-interval">
                <label for="interval_penyusutan_tahun" class="form-label">Nilai Turun Setiap</label>
                <select name="interval_penyusutan_tahun" id="interval_penyusutan_tahun"
                    class="form-select {{ $errors->has('interval_penyusutan_tahun') ? 'is-invalid' : '' }}">
                    @foreach (range(1, 10) as $n)
                        <option value="{{ $n }}" @selected((int) old('interval_penyusutan_tahun', $masterBarang?->interval_penyusutan_tahun ?: 1) === $n)>{{ $n }} tahun</option>
                    @endforeach
                </select>
                <small class="text-muted">Masa pemakaian harus kelipatan angka ini. <span id="contoh-susut"></span></small>
                @error('interval_penyusutan_tahun')<small class="invalid-feedback d-block">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input type="hidden" name="butuh_perawatan" value="0">
                <input class="form-check-input" type="checkbox" role="switch" name="butuh_perawatan"
                    id="butuh_perawatan" value="1"
                    @checked((bool) old('butuh_perawatan', $masterBarang?->butuh_perawatan))>
                <label class="form-check-label" for="butuh_perawatan">Butuh perawatan</label>
            </div>
        </div>

        @push('script')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const jenis = document.getElementById('jenis_barang');
                const disusutkan = document.getElementById('disusutkan');
                const blokDisusutkan = document.getElementById('blok-disusutkan');
                const blokMasa = document.getElementById('blok-masa');
                const blokInterval = document.getElementById('blok-interval');
                const masa = document.getElementById('masa_pemakaian');
                const interval = document.getElementById('interval_penyusutan_tahun');

                function setAktif(el, aktif) {
                    el.style.display = aktif ? '' : 'none';
                    el.querySelectorAll('input, select').forEach(i => i.disabled = !aktif);
                }

                function contoh() {
                    const m = parseFloat(masa.value), n = parseInt(interval.value);
                    const teks = document.getElementById('contoh-susut');
                    if (!(m > 0) || !(n > 0) || !disusutkan.checked) { teks.textContent = ''; return; }
                    const langkah = Math.ceil(m / n);
                    const turun = Math.round(12000000 / langkah);
                    teks.textContent = 'Contoh: harga Rp 12.000.000 turun Rp ' + turun.toLocaleString('id-ID')
                        + ' setiap ' + n + ' tahun, habis setelah ' + m + ' tahun.';
                }

                function sinkron() {
                    const peralatan = jenis.value === 'peralatan';
                    setAktif(blokDisusutkan, peralatan);
                    if (peralatan) {
                        setAktif(blokMasa, disusutkan.checked);
                        setAktif(blokInterval, disusutkan.checked);
                        document.getElementById('label-masa').textContent = 'Masa Pemakaian (tahun)';
                        document.getElementById('bantuan-masa').textContent = 'Wajib diisi, lebih dari 1 tahun.';
                        masa.placeholder = 'Contoh: 4';
                    } else {
                        setAktif(blokMasa, true);
                        setAktif(blokInterval, false);
                        document.getElementById('label-masa').textContent = 'Masa Pemakaian (bulan, opsional)';
                        document.getElementById('bantuan-masa').textContent = 'Hanya informasi, 1–12 bulan. Nilai perlengkapan langsung 0 saat dipakai.';
                        masa.placeholder = 'Contoh: 6';
                    }
                    contoh();
                }

                jenis.addEventListener('change', sinkron);
                disusutkan.addEventListener('change', sinkron);
                masa.addEventListener('input', contoh);
                interval.addEventListener('change', contoh);
                sinkron();
            });
        </script>
        @endpush

    </div>
</div>