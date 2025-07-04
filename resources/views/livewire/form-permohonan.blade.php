<div>
    <form wire:submit.prevent="submit">
        <div class="card">
            <div class="card-body">
                <x-error-list />
                <div class="row mb-6 gy-6">
                    <div class="col-xl">
                        <div class="mb-4">
                            <label for="ta" class="form-label">Tahun Anggaran</label>
                            <select wire:model="tahun_anggaran" class="form-control">
                                <option value="2024">2024/2025</option>
                                <option value="2025">2025/2026</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="bdg" class="form-label">Nama Bidang</label>
                            <select wire:model="bidang" class="form-control">
                                <option value="kerumahtanggaan">Kerumahtanggaan</option>
                                <option value="sekretarian">Sekretarian</option>
                                <option value="sarpras">Sarana dan Prasarana</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-xl">
                        <div class="mb-4">
                            <label for="kegiatan" class="form-label">Unit Kegiatan</label>
                            <input type="text" wire:model="unit_kegiatan" class="form-control"
                                placeholder="Masukkan Unit Kegiatan" />
                        </div>
                        <div class="mb-4">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" wire:model="keterangan"></textarea>
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
                                @foreach($details as $i => $detail)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>
                                        <x-input.select2 name="barang_id_{{ $i }}" id="barang_id_{{ $i }}"
                                            class="form-control" wire.model.lazy="details.{{ $i }}.barang_id"
                                            placeholder="Pilih Barang" :options="$barangOptions" />
                                    </td>
                                    <td>
                                        <x-input.currency name="volume" id="volume" wire:model="details.{{ $i }}.volume"
                                            class="form-control text-center" />
                                    </td>
                                    <td>

                                        <select wire:model="details.{{ $i }}.satuan"
                                            class="form-control px-1 text-center">
                                            @foreach($satuanOptions as $id => $nama)
                                            <option value="{{ $id }}">{{ $nama }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <x-input.currency name="harga" id="harga" wire:model="details.{{ $i }}.harga"
                                            class="form-control text-end" />
                                    </td>
                                    <td>
                                        <x-input.currency name="jumlah" id="jumlah" wire:model="details.{{ $i }}.jumlah"
                                            class="form-control text-end" readonly />
                                    </td>
                                    <td>
                                        <input type="text" wire:model="details.{{ $i }}.kode_ma"
                                            class="form-control text-center" />
                                    </td>
                                    <td>
                                        <input type="text" wire:model="details.{{ $i }}.keterangan"
                                            class="form-control text-center" />
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm"
                                            wire:click="removeDetail({{ $i }})">Hapus</button>
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
                                        {{ collect($details)->sum('jumlah') }}
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-5 align-content-end">
                    <button type="submit" class="btn btn-primary me-2">Kirim</button>
                    <a href="#" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </form>
</div>