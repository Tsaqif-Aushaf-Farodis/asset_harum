<x-layout.app title="Mutasi Aset" activeMenu="mutasi-aset.index" :withError="true">
    <div class="container my-5">
        <x-breadcrumb title="Mutasi Aset" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Mutasi Aset'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Mutasi Aset</h5>
                <a href="{{ route('mutasi-aset.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Tambah Mutasi Aset
                </a>
            </div>
            <div class="card-body">
                @include('mutasi-aset.includes.index-table')
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

            $('select[name="jenis_mutasi"], select[name="status"]').on('change', function() {
                loadTable();
            });

            function loadTable() {
                const formData = $('#searchForm').serialize();
                
                $.ajax({
                    url: "{{ route('mutasi-aset.index') }}",
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
