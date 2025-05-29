<x-layout.app title="Perbarui Master Satuan" activeMenu="master-satuan.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui Master Satuan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Master Satuan', 'url' => route('master-satuan.index')],
            ['label' => 'Perbarui Master Satuan'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('master-satuan.update', $masterSatuan) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('master-satuan.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('master-satuan.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>