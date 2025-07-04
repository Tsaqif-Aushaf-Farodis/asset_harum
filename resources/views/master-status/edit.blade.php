<x-layout.app title="Perbarui Master Status" activeMenu="master-status.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Master Status" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Status', 'url' => route('master-status.index')],
            ['label' => 'Perbarui Master Status'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-status.update', $masterStatus) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('master-status.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('master-status.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>