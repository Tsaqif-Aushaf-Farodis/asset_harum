<x-layout.app title="Form Permohonan Kebutuhan" activeMenu="dummy.form-permohonan" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="Form Permohonan Kebutuhan" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Form Permohonan Kebutuhan'],
        ]" />

        <livewire:form-permohonan />

    </div>
</x-layout.app>