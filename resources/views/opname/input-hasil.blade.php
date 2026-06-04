<x-layout.app title="Input Hasil Opname" activeMenu="opname.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Input Hasil Opname" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Opname', 'url' => route('opname.index')],
            ['label' => 'Input Hasil'],
        ]" />

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $opname->nama_opname }} ({{ $opname->kode_opname }})</h5>
            </div>
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('opname.save-hasil', $opname) }}" method="POST" role="form">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-striped table-sm" id="opnameTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Kode Inventaris</th>
                                    <th>Nama Barang</th>
                                    <th>Kondisi Sebelum</th>
                                    <th>Status Keberadaan</th>
                                    <th>Kondisi Sesudah</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($opname->details as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->pengadaan->kode_inventaris ?? '-' }}</td>
                                    <td>{{ $detail->pengadaan->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $detail->kondisi_sistem ?? '-' }}</td>
                                    <td>
                                        <input type="hidden" name="details[{{ $index }}][id]" value="{{ $detail->id }}">
                                        <select name="details[{{ $index }}][status_keberadaan]" class="form-select form-select-sm" required>
                                            <option value="">Pilih Status</option>
                                            <option value="sesuai" {{ $detail->status_keberadaan == 'sesuai' ? 'selected' : '' }}>Sesuai</option>
                                            <option value="tidak_sesuai" {{ $detail->status_keberadaan == 'tidak_sesuai' ? 'selected' : '' }}>Tidak Sesuai</option>
                                            <option value="hilang" {{ $detail->status_keberadaan == 'hilang' ? 'selected' : '' }}>Hilang</option>
                                            <option value="rusak" {{ $detail->status_keberadaan == 'rusak' ? 'selected' : '' }}>Rusak</option>
                                            <option value="baru" {{ $detail->status_keberadaan == 'baru' ? 'selected' : '' }}>Baru Ditemukan</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="details[{{ $index }}][kondisi_fisik]"
                                                class="form-select form-select-sm">
                                            <option value="">Pilih Kondisi</option>

                                            @foreach($statuses as $status)
                                                <option value="{{ $status->nama_status }}"
                                                    {{ ($detail->kondisi_fisik ?? '') == $status->nama_status ? 'selected' : '' }}>
                                                    {{ $status->nama_status }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="details[{{ $index }}][catatan]" 
                                               class="form-control form-control-sm" 
                                               value="{{ $detail->catatan ?? '' }}" 
                                               placeholder="Catatan">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Simpan Hasil</button>
                        <a href="{{ route('opname.show', $opname) }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('script')
    <script>
        $(document).ready(function() {
            // Optional: Add DataTables for better UX
            if ($.fn.DataTable) {
                $('#opnameTable').DataTable({
                    paging: true,
                    pageLength: 25,
                    ordering: false,
                    searching: true,
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ data per halaman",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data",
                        infoFiltered: "(difilter dari _MAX_ total data)",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Selanjutnya",
                            previous: "Sebelumnya"
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout.app>
