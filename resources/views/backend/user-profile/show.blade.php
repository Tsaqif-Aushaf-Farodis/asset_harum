<x-layout.app title="Detail User Profile" activeMenu="user-profile.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail User Profile" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'User Profile', 'url' => route('user-profile.index')],
            ['label' => 'Detail User Profile'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('user-profile.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('user-profile view')
                        <a href="{{ route('user-profile.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('user-profile edit')
                        <a href="{{ route('user-profile.edit', $userProfile) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('user-profile delete')
                            <form action="{{ route('user-profile.destroy', $userProfile) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data user profile ini akan dihapus!"
                                    positive="Ya, hapus!" icon="info"
                                    class="btn btn-danger btn-sm"
                                    data-bs-toggle="tooltip"
                                    data-bs-title="Hapus"
                                    data-bs-placement="top">
                                    <i class="bx bx-trash me-1"></i>Hapus
                                </x-input.confirm-button>
                            </form>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form class="row g-3">
                    
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Full Name</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $userProfile->full_name }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Avatar Url</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $userProfile->avatar_url }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">No Telp</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $userProfile->no_telp }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Alamat</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $userProfile->alamat }}</div>
                                <div class="col-md-4">
                                    <label for="first-name-horizontal">Jenis Kelamin</label>
                                </div>
                                <div class="col-md-8 form-group">: {{ $userProfile->jenis_kelamin }}</div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
