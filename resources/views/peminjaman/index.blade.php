<x-layout.app title="Peminjaman Aset" activeMenu="peminjaman.index" :withError="true">
    <div class="container my-5">
        <x-breadcrumb title="Peminjaman Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman Aset'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Peminjaman Aset</h5>
                <div>
                    <a href="{{ route('peminjaman.riwayat') }}" class="btn btn-outline-secondary me-2">
                        <i class="bx bx-history me-1"></i>Riwayat
                    </a>
                    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
                        <i class="bx bx-plus me-1"></i>Tambah Peminjaman
                    </a>
                </div>
            </div>
            <div class="card-body">
                @include('peminjaman.includes.index-table')
            </div>
        </div>
    </div>

    @push('script')
    <script>
        $(document).ready(function() {
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadTable();
            });

            $('select[name="status_peminjaman"]').on('change', function() {
                loadTable();
            });

            function loadTable() {
                const formData = $('#searchForm').serialize();
                
                $.ajax({
                    url: "{{ route('peminjaman.index') }}",
                    type: 'GET',
                    data: formData,
                    headers: {
                        'HX-Request': 'true'
                    },
                    success: function(response) {
                        $('#table-container').html(response);
                    }
                });
            }
        });
    </script>
    @endpush
</x-layout.app>
