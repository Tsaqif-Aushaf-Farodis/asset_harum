<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanOpnameExport implements FromCollection, WithHeadings, WithMapping, WithTitle
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
            'Selisih',
            'Keterangan',
        ];
    }

    public function map($detail): array
    {
        static $index = 0;
        $index++;

        $selisih = '';
        if ($detail->status_keberadaan == 'tidak_sesuai') {
            $selisih = 'Tidak Sesuai';
        } elseif ($detail->status_keberadaan == 'hilang') {
            $selisih = 'Hilang';
        } elseif ($detail->status_keberadaan == 'rusak') {
            $selisih = 'Rusak';
        } elseif ($detail->status_keberadaan == 'baru') {
            $selisih = 'Baru Ditemukan';
        }

        return [
            $index,
            $detail->pengadaan->kode_inventaris ?? '-',
            $detail->pengadaan->nama_barang ?? '-',
            $detail->kondisi_sebelum ?? '-',
            $detail->status_keberadaan ?? '-',
            $detail->kondisi_sesudah ?? '-',
            $selisih,
            $detail->keterangan ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Opname';
    }
}
