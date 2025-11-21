<x-layout.app title="Opname" activeMenu="opname.index" :withError="true">
    <div class="container my-5">
        <x-breadcrumb title="Opname" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Opname'],
        ]" />

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Daftar Sesi Opname</h5>
                <a href="{{ route('opname.create') }}" class="btn btn-primary">
                    <i class="bx bx-plus me-1"></i>Buat Sesi Opname
                </a>
            </div>
            <div class="card-body">
                @include('opname.includes.index-table')
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

            $('select[name="status"]').on('change', function() {
                loadTable();
            });

            function loadTable() {
                const formData = $('#searchForm').serialize();
                
                $.ajax({
                    url: "{{ route('opname.index') }}",
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
