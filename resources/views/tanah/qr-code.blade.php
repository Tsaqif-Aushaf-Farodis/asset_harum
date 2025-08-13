<x-layout.app title="QR Code Tanah" activeMenu="tanah.qr-code" :withError="false">
    <div class="container my-5">
        <x-breadcrumb title="QR Code Data Tanah" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Data Tanah', 'url' => route('tanah.index')],
            ['label' => 'QR Code'],
        ]" />

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="card-title mb-0">QR Code - {{ $tanah->kode_inventaris }}</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            {!! $qrCode !!}
                        </div>
                        
                        <div class="mb-3">
                            <h6>{{ $tanah->barang->nama_barang ?? '-' }}</h6>
                            <p class="text-muted mb-1">{{ $tanah->kode_inventaris }}</p>
                            <p class="text-muted">{{ $tanah->lokasi->lokasi->nama_lokasi ?? '-' }} - {{ $tanah->lokasi->nama_sub_lokasi ?? '-' }}</p>
                        </div>

                        @if($tanah->tanahDetail)
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Luas: {{ number_format($tanah->tanahDetail->luas, 0, ',', '.') }} m²</small>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Status: {{ $tanah->tanahDetail->status_tanah }}</small>
                            </div>
                        </div>
                        @endif

                        <div class="mt-4">
                            <button onclick="window.print()" class="btn btn-primary me-2">
                                <i class="bx bx-printer me-1"></i>Print
                            </button>
                            <a href="{{ route('tanah.show', $tanah) }}" class="btn btn-info me-2">
                                <i class="bx bx-show me-1"></i>Detail
                            </a>
                            <a href="{{ route('tanah.index') }}" class="btn btn-secondary">
                                <i class="bx bx-arrow-back me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('style')
    <style>
        @media print {
            .container { margin: 0 !important; padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; }
            .btn { display: none !important; }
            .breadcrumb { display: none !important; }
        }
    </style>
    @endpush
</x-layout.app>
