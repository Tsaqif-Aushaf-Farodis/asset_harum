<x-layout.app title="Data Tanah" activeMenu="tanah.index" :withError="true">
    <div class="container my-5">
        <x-breadcrumb title="Data Tanah" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Tanah'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Data Tanah</h5>
                <a href="{{ route('tanah.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Tambah Data Tanah
                </a>
            </div>
            <div class="card-body">
                @include('tanah.includes.index-table')
            </div>
        </div>
    </div>

    @push('script')
    <script>
        $(document).ready(function() {
            // Handle search form submission
            $('#searchForm').on('submit', function(e) {
                e.preventDefault();
                loadTable();
            });

            // Handle column selection change
            $('input[name="col[]"]').on('change', function() {
                loadTable();
            });

            function loadTable() {
                const formData = $('#searchForm').serialize();
                
                $.ajax({
                    url: "{{ route('tanah.index') }}",
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
