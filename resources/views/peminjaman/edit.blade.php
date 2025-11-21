<x-layout.app title="Edit Peminjaman" activeMenu="peminjaman.index" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Edit Peminjaman" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman', 'url' => route('peminjaman.index')],
            ['label' => 'Edit Peminjaman'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('peminjaman.update', $peminjaman) }}" method="POST" role="form">
                    @csrf
                    @method('PUT')

                    @include('peminjaman.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Update</button>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
