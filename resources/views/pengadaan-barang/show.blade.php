<x-layout.app title="Detail Pengadaan Barang" activeMenu="pengadaan-barang.show" :withError="true">
     <div class="container my-5">
        <x-breadcrumb title="Detail Pengadaan Barang" :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => url('/')],
            ['label' => 'Pengadaan Barang', 'url' => route('pengadaan-barang.index')],
            ['label' => 'Detail Pengadaan Barang'],
        ]" />

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('pengadaan-barang.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-arrow-back me-1"></i>Kembali
                    </a>

                    <div>
                        @can('pengadaan-barang view')
                        <a href="{{ route('pengadaan-barang.create') }}"
                            class="btn btn-sm btn-info">
                            <i class="bx bx-plus me-1"></i>Baru
                        </a>
                        @endcan
                        @can('pengadaan-barang edit')
                        <a href="{{ route('pengadaan-barang.edit', $pengadaanBarang) }}"
                            class="btn btn-sm btn-primary">
                            <i class="bx bx-pencil me-1"></i>Edit
                        </a>
                        @endcan
                        @can('pengadaan-barang delete')
                            <form action="{{ route('pengadaan-barang.destroy', $pengadaanBarang) }}"
                                method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <x-input.confirm-button text="Data pengadaan barang ini akan dihapus!"
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
                @php
                    $perlengkapan = $pengadaanBarang->barang?->isPerlengkapan();
                    $susut = $perlengkapan ? null : $pengadaanBarang->penyusutan();
                    $baris = [
                        'Kode Inventaris' => $pengadaanBarang->kode_inventaris,
                        'Barang' => $pengadaanBarang->barang->nama_barang ?? '-',
                        'Jenis' => $perlengkapan ? 'Perlengkapan (stok)' : 'Peralatan (aset)',
                        'Kategori' => $pengadaanBarang->barang->kategori->nama_kategori_barang ?? '-',
                        'Lokasi' => ($pengadaanBarang->lokasi->lokasi->nama_lokasi ?? '-') . ' - ' . ($pengadaanBarang->lokasi->nama_sub_lokasi ?? '-'),
                        'Sumber' => $pengadaanBarang->sumber,
                        'Status' => $pengadaanBarang->status,
                        'Kondisi' => $pengadaanBarang->statusKondisi->nama_status ?? '-',
                        'Tanggal Pengadaan' => $pengadaanBarang->tanggal_pengadaan,
                        'Jumlah' => $pengadaanBarang->jumlah . ' ' . ($pengadaanBarang->satuan->nama_satuan ?? ''),
                        'Harga Satuan' => \App\Helpers\Format::rupiah($pengadaanBarang->harga_satuan),
                        'Total Harga' => \App\Helpers\Format::rupiah($pengadaanBarang->total_harga),
                        'Anggaran' => $pengadaanBarang->anggaran ? $pengadaanBarang->anggaran->label : 'Belum dikaitkan anggaran',
                    ];
                    if ($perlengkapan) {
                        $baris['Sudah Dipakai'] = $pengadaanBarang->stok_terpakai;
                        $baris['Sisa Stok'] = $pengadaanBarang->stok_tersedia;
                        $baris['Nilai Persediaan'] = \App\Helpers\Format::rupiah($pengadaanBarang->nilaiSaatIni());
                    } else {
                        $baris['Penyusutan'] = $susut['disusutkan']
                            ? 'Disusutkan: turun ' . \App\Helpers\Format::rupiah($susut['penyusutan_per_langkah']) . ' tiap ' . $susut['interval_tahun'] . ' tahun'
                                . ($pengadaanBarang->disusutkan_diatur_manual ? ' (diatur manual pada aset ini)' : '')
                            : 'Tidak disusutkan' . ($pengadaanBarang->disusutkan_diatur_manual ? ' (diatur manual pada aset ini)' : '');
                        $baris['Akumulasi Penyusutan'] = \App\Helpers\Format::rupiah($susut['akumulasi']);
                        $baris['Nilai Buku Saat Ini'] = \App\Helpers\Format::rupiah($susut['nilai_buku']);
                        if ($susut['tanggal_penyusutan_berikutnya']) {
                            $baris['Penurunan Nilai Berikutnya'] = $susut['tanggal_penyusutan_berikutnya']->format('d/m/Y');
                        }
                        $baris['Butuh Perawatan'] = $pengadaanBarang->barang?->butuh_perawatan ? 'Ya' : 'Tidak';
                    }
                    $baris['Keterangan'] = $pengadaanBarang->keterangan;
                @endphp
                <div class="row g-3">
                    @foreach ($baris as $label => $nilai)
                        <div class="col-md-4"><label>{{ $label }}</label></div>
                        <div class="col-md-8 form-group">: {{ $nilai }}</div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
