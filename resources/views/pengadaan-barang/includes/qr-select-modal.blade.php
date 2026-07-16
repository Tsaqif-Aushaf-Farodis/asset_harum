<div class="modal fade" id="qrSelectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="{{ route('pengadaan-barang.qr-code.download-bulk') }}" id="qrSelectForm"
                target="_blank">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Pilih Barang untuk Download QR Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" id="qrSearchInput" class="form-control mb-2"
                        placeholder="Cari kode inventaris atau nama barang...">
                    <div class="form-check mb-2 border-bottom pb-2">
                        <input class="form-check-input" type="checkbox" id="qrSelectAll">
                        <label class="form-check-label" for="qrSelectAll">Pilih Semua</label>
                    </div>
                    <div style="max-height:400px; overflow-y:auto;">
                        @foreach ($allPengadaanBarangForQr as $item)
                            <div class="form-check qr-item-row">
                                <input class="form-check-input qr-item-checkbox" type="checkbox" name="ids[]"
                                    value="{{ $item->id }}" id="qrItem{{ $item->id }}"
                                    data-search="{{ strtolower($item->kode_inventaris . ' ' . ($item->barang->nama_barang ?? '')) }}">
                                <label class="form-check-label" for="qrItem{{ $item->id }}">
                                    {{ $item->kode_inventaris }} &mdash; {{ $item->barang->nama_barang ?? '-' }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mx-2" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="qrSelectSubmit" disabled>
                        <i class="bx bx-show me-1"></i>Preview PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('qrSearchInput');
            const selectAll = document.getElementById('qrSelectAll');
            const submitBtn = document.getElementById('qrSelectSubmit');
            const rows = document.querySelectorAll('.qr-item-row');

            function updateSubmitState() {
                submitBtn.disabled = document.querySelectorAll('.qr-item-checkbox:checked').length === 0;
            }

            search?.addEventListener('input', function () {
                const term = this.value.toLowerCase();
                rows.forEach(row => {
                    const cb = row.querySelector('.qr-item-checkbox');
                    row.style.display = cb.dataset.search.includes(term) ? '' : 'none';
                });
            });

            selectAll?.addEventListener('change', function () {
                rows.forEach(row => {
                    if (row.style.display !== 'none') {
                        row.querySelector('.qr-item-checkbox').checked = selectAll.checked;
                    }
                });
                updateSubmitState();
            });

            document.querySelectorAll('.qr-item-checkbox').forEach(cb => cb.addEventListener('change', updateSubmitState));
        });
    </script>
@endpush
