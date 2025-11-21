<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form wire:submit.prevent="save">
        <!-- Header Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="tahun_anggaran" class="form-label">Tahun Anggaran <span class="text-danger">*</span></label>
                    <select wire:model="tahun_anggaran" class="form-select @error('tahun_anggaran') is-invalid @enderror">
                        <option value="">Pilih Tahun Anggaran</option>
                        @for($year = date('Y') - 1; $year <= date('Y') + 5; $year++)
                            <option value="{{ $year }}">{{ $year }}/{{ $year + 1 }}</option>
                        @endfor
                    </select>
                    @error('tahun_anggaran') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="bidang" class="form-label">Bidang <span class="text-danger">*</span></label>
                    <select wire:model="bidang" class="form-select @error('bidang') is-invalid @enderror">
                        <option value="">Pilih Bidang</option>
                        <option value="Kesekretariatan">Kesekretariatan</option>
                        <option value="Keuangan">Keuangan</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Pengasuhan">Pengasuhan</option>
                        <option value="Sarana dan Prasarana">Sarana dan Prasarana</option>
                        <option value="Kerumahtanggaan">Kerumahtanggaan</option>
                    </select>
                    @error('bidang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label for="unit_kegiatan" class="form-label">Unit Kegiatan <span class="text-danger">*</span></label>
                    <input type="text" wire:model="unit_kegiatan" class="form-control @error('unit_kegiatan') is-invalid @enderror"
                        placeholder="Contoh: MTs Putra, MA Putri, dll">
                    @error('unit_kegiatan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea wire:model="keterangan" class="form-control @error('keterangan') is-invalid @enderror" 
                        rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
                    @error('keterangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <!-- Detail Barang -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail Pengadaan Barang</h5>
                <button type="button" class="btn btn-sm btn-success" wire:click="addDetail">
                    <i class="bx bx-plus"></i> Tambah Barang
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th width="3%">No</th>
                                <th width="20%">Uraian Barang <span class="text-danger">*</span></th>
                                <th width="8%">Volume <span class="text-danger">*</span></th>
                                <th width="10%">Satuan <span class="text-danger">*</span></th>
                                <th width="12%">Harga Satuan <span class="text-danger">*</span></th>
                                <th width="12%">Jumlah</th>
                                <th width="10%">Kode MA</th>
                                <th width="15%">Keterangan</th>
                                <th width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($details as $index => $detail)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <select class="form-select form-select-sm @error('details.'.$index.'.barang_id') is-invalid @enderror" 
                                        wire:model="details.{{ $index }}.barang_id">
                                        <option value="">Pilih Barang</option>
                                        @foreach($barangOptions as $barang)
                                            <option value="{{ $barang->id }}">{{ $barang->nama_barang }}</option>
                                        @endforeach
                                    </select>
                                    @error('details.'.$index.'.barang_id') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        wire:model="details.{{ $index }}.volume"
                                        class="form-control form-control-sm text-center @error('details.'.$index.'.volume') is-invalid @enderror" />
                                    @error('details.'.$index.'.volume') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
                                </td>
                                <td>
                                    <select class="form-select form-select-sm @error('details.'.$index.'.satuan') is-invalid @enderror" 
                                        wire:model="details.{{ $index }}.satuan">
                                        <option value="">Pilih</option>
                                        @foreach($satuanOptions as $satuan)
                                            <option value="{{ $satuan->kode_satuan }}">{{ $satuan->nama_satuan }}</option>
                                        @endforeach
                                    </select>
                                    @error('details.'.$index.'.satuan') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0"
                                        wire:model="details.{{ $index }}.harga"
                                        class="form-control form-control-sm text-end @error('details.'.$index.'.harga') is-invalid @enderror" 
                                        placeholder="0" />
                                    @error('details.'.$index.'.harga') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
                                </td>
                                <td>
                                    <input type="text" readonly
                                        value="Rp {{ number_format($detail['jumlah'], 0, ',', '.') }}"
                                        class="form-control form-control-sm text-end bg-light" />
                                </td>
                                <td>
                                    <input type="text" wire:model="details.{{ $index }}.kode_ma"
                                        class="form-control form-control-sm text-center" 
                                        placeholder="Kode MA" />
                                </td>
                                <td>
                                    <input type="text" wire:model="details.{{ $index }}.keterangan"
                                        class="form-control form-control-sm" 
                                        placeholder="Keterangan" />
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger" 
                                        wire:click="removeDetail({{ $index }})"
                                        @if(count($details) <= 1) disabled @endif>
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-3 text-muted">
                                    Belum ada detail barang. Klik tombol "Tambah Barang" untuk menambah.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                        @if(count($details) > 0)
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Total:</th>
                                <th class="text-end">
                                    Rp {{ number_format(collect($details)->sum('jumlah'), 0, ',', '.') }}
                                </th>
                                <th colspan="3"></th>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="{{ route('permohonan.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back"></i> Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bx bx-save"></i> Simpan Permohonan
            </button>
        </div>
    </form>
</div>