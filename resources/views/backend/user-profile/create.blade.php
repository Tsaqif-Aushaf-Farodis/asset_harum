<x-layout.app title="Tambah User Profile" activeMenu="user-profile.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah User Profile" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'User Profile', 'url' => route('user-profile.index')],
            ['label' => 'Tambah User Profile'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('user-profile.store') }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    @include('user-profile.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('user-profile.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>