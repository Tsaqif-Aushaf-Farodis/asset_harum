<x-layout.app title="Tambah Peminjaman" activeMenu="peminjaman.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Peminjaman" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Peminjaman', 'url' => route('peminjaman.index')],
            ['label' => 'Tambah Peminjaman'],
        ]" />

        <div class="card">
            <div class="card-body">
                <x-error-list />

                <form action="{{ route('peminjaman.store') }}" method="POST" role="form">
                    @csrf
                    @method('POST')

                    @include('peminjaman.includes.form')

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">Tambah</button>
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layout.app>
