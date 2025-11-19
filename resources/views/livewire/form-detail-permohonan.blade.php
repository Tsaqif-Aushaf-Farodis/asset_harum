<div>
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
                {{ print_r($details) }}
                <tbody>
                    @foreach($details as $i => $detail)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>
                          <select wire:model="details[{{ $i }}][barang_id]"
                                class="form-control px-1 text-center">
                                @foreach($barangOptions as $item => $label)
                                <option value="{{ $item }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <x-input.currency name="volume" id="volume" wire:model="details[{{ $i }}][volume]"
                                class="form-control text-center" />
                        </td>
                        <td>

                            <select wire:model="details[{{ $i }}][satuan]"
                                class="form-control px-1 text-center">
                                @foreach($satuanOptions as $item)
                                <option value="{{ $item->id }}">{{ $item->kode_satuan }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <x-input.currency name="harga" id="harga" wire:model="details[{{ $i }}][harga]"
                                class="form-control text-end" />
                        </td>
                        <td>
                            <x-input.currency name="jumlah" id="jumlah" wire:model="details[{{ $i }}][jumlah]"
                                class="form-control text-end" readonly />
                        </td>
                        <td>
                            <input type="text" wire:model="details[{{ $i }}][kode_ma]"
                                class="form-control text-center" />
                        </td>
                        <td>
                            <input type="text" wire:model="details[{{ $i }}][keterangan]"
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
</div>