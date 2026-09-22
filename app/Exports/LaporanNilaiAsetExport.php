<?php

namespace App\Exports;

use App\Services\LaporanAsetService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanNilaiAsetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $filters;
    private int $index = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return LaporanAsetService::nilaiAset($this->filters)['rows'];
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Jenis',
            'Kategori',
            'Lokasi',
            'Jumlah',
            'Harga Satuan',
            'Nilai Perolehan',
            'Penyusutan / Terpakai',
            'Nilai Saat Ini',
        ];
    }

    public function map($row): array
    {
        $this->index++;
        $p = $row->pengadaan;

        return [
            $this->index,
            $p->kode_inventaris,
            $p->barang->nama_barang ?? '-',
            $row->jenis,
            $p->barang->kategori->nama_kategori_barang ?? '-',
            $p->lokasi->nama_sub_lokasi ?? '-',
            $p->jumlah ?? 1,
            $p->harga_satuan ?? 0,
            $row->nilai_perolehan,
            $row->pengurang,
            $row->nilai_saat_ini,
        ];
    }

    public function title(): string
    {
        return 'Laporan Nilai Aset';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $highestRow = $event->sheet->getDelegate()->getHighestRow();
                $total = $highestRow + 1;

                $event->sheet->getDelegate()->setCellValue('H' . $total, 'TOTAL:');
                foreach (['I', 'J', 'K'] as $col) {
                    $event->sheet->getDelegate()->setCellValue($col . $total, "=SUM({$col}2:{$col}{$highestRow})");
                }

                $event->sheet->getDelegate()->getStyle("H{$total}:K{$total}")->getFont()->setBold(true);
            },
        ];
    }
}
