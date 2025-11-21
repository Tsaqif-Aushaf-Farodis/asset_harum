<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class KertasKerjaOpnameExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $opname;

    public function __construct($opname)
    {
        $this->opname = $opname;
    }

    public function collection()
    {
        return $this->opname->details()->with('pengadaan')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Kondisi Sebelum',
            'Status Keberadaan',
            'Kondisi Sesudah',
            'Keterangan',
        ];
    }

    public function map($detail): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $detail->pengadaan->kode_inventaris ?? '-',
            $detail->pengadaan->nama_barang ?? '-',
            $detail->kondisi_sebelum ?? '-',
            $detail->status_keberadaan ?? '-',
            $detail->kondisi_sesudah ?? '-',
            $detail->keterangan ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Kertas Kerja Opname';
    }
}
