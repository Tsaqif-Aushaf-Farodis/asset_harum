<div>
    <div class="row mb-6 gy-6">
        <div class="col-xl">
            <div class="mb-4">
                <label for="ta" class="form-label">Tahun Anggaran</label>
                <select wire:model.defer="tahun_anggaran" class="form-control">
                    <option value="2024">2024/2025</option>
                    <option value="2025">2025/2026</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="bdg" class="form-label">Nama Bidang</label>
                <select wire:model.defer="bidang" class="form-control">
                    <option value="kerumahtanggaan">Kerumahtanggaan</option>
                    <option value="sekretarian">Sekretarian</option>
                    <option value="sarpras">Sarana dan Prasarana</option>
                </select>
            </div>
        </div>
        <div class="col-xl">
            <div class="mb-4">
                <label for="kegiatan" class="form-label">Unit Kegiatan</label>
                <input type="text" wire:model.defer="unit_kegiatan" class="form-control"
                    placeholder="Masukkan Unit Kegiatan" />
            </div>
            <div class="mb-4">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea class="form-control" wire:model.defer="keterangan"></textarea>
            </div>
        </div>
    </div>
    <div class="card">
        <h5 class="card-header">Detail Pengadaan Barang</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr class="text-center">
                        <th width="5%">#</th>
                        <th width="25%">Uraian Barang</th>
                        <th width="8%">Volume</th>
                        <th width="7%">Satuan</th>
                        <th width="15%">Harga</th>
                        <th width="15%">Jumlah</th>
                        <th width="10%">Kode MA</th>
                        <th width="15%">Keterangan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($details as $index => $detail)
                    <tr>
                        <td>{{ $index+1 }}</td>
                        <td>
                            <select class="form-control" wire:model="details.{{ $index }}.barang_id">
                                <option value="">Pilih Barang</option>
                                @foreach($barangOptions as $barang)
                                <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="volume" id="volume" wire:model="details.{{ $index }}.volume"
                                class="form-control text-center" />
                        </td>
                        <td>
                            <select class="form-control" wire:model="details.{{ $index }}.satuan">
                                <option value="">Pilih Satuan</option>
                                @foreach($satuanOptions as $satuan)
                                <option value="{{ $satuan->id }}">{{ $satuan->kode_satuan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="number" name="harga" id="harga" wire:model="details.{{ $index }}.harga"
                                class="form-control text-end" />
                        </td>
                        <td>
                            <input type="number" name="jumlah" id="jumlah" wire:model="details.{{ $index }}.jumlah"
                                class="form-control text-end" readonly />
                        </td>
                        <td>
                            <input type="text" wire:model="details.{{ $index }}.kode_ma"
                                class="form-control text-center" />
                        </td>
                        <td>
                            <input type="text" wire:model="details.{{ $index }}.keterangan"
                                class="form-control text-center" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm"
                                wire:click="removeDetail({{ $index }})">Hapus</button>
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="9">
                            <button type="button" class="btn btn-success btn-sm"
                                wire:click="addDetail">Tambah Barang</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center" colspan="5">Jumlah Total</td>
                        <td class="text-end">
                            {{
                                    collect($details)->sum(function($item) {
                                        return (int) $item['jumlah'];
                                    })
                                }}
                        </td>

                        <td colspan="3"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        <button type="button" class="btn btn-primary me-2" wire:click="save">Simpan</button>
        <a href="{{ route('permohonan.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
    <x-error-list />

</div>