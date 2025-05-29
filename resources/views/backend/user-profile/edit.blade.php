<x-layout.app title="Perbarui User Profile" activeMenu="user-profile.edit" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Perbarui User Profile" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'User Profile', 'url' => route('user-profile.index')],
            ['label' => 'Perbarui User Profile'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('user-profile.update', $userProfile) }}" method="POST" role="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @include('user-profile.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Perbarui</button>
                        <a href="{{ route('user-profile.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>