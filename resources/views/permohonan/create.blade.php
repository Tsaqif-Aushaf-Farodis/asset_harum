<x-layout.app title="Tambah Permohonan" activeMenu="permohonan.create" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Tambah Permohonan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Permohonan', 'url' => route('permohonan.index')],
            ['label' => 'Tambah Permohonan'],
        ]" />

        <div class="card">
            <div class="card-body">
      


                    @include('permohonan.includes.form')

               
            </div>
        </div>
    </div>
</x-layout.app>