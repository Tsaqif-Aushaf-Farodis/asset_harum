<?php

namespace App\Exports;

use App\Services\PerlengkapanService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanStokPerlengkapanExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    private int $index = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return PerlengkapanService::batch($this->filters);
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Lokasi Penyimpanan',
            'Tanggal Terima',
            'Satuan',
            'Masuk',
            'Terpakai',
            'Sisa',
            'Harga Satuan',
            'Nilai Persediaan',
        ];
    }

    public function map($b): array
    {
        $this->index++;

        return [
            $this->index,
            $b->kode_inventaris,
            $b->barang->nama_barang ?? '-',
            ($b->lokasi->lokasi->nama_lokasi ?? '-') . ' - ' . ($b->lokasi->nama_sub_lokasi ?? '-'),
            date('d/m/Y', strtotime($b->tanggal_pengadaan)),
            $b->satuan->nama_satuan ?? '-',
            $b->jumlah,
            $b->stok_terpakai,
            $b->stok_tersedia,
            $b->harga_satuan,
            $b->nilaiSaatIni(),
        ];
    }

    public function title(): string
    {
        return 'Stok Perlengkapan';
    }
}
