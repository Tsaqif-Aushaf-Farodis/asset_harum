<?php

namespace App\Exports;

use App\Models\PengadaanBarang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class LaporanNilaiAsetExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PengadaanBarang::aktif()->with(['lokasi', 'kategori']);

        if (isset($this->filters['lokasi_id'])) {
            $query->where('lokasi_id', $this->filters['lokasi_id']);
        }

        if (isset($this->filters['kategori_id'])) {
            $query->where('kategori_id', $this->filters['kategori_id']);
        }

        return $query->orderBy('kode_inventaris')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Jumlah',
            'Harga Satuan',
            'Total Nilai',
        ];
    }

    public function map($barang): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $barang->kode_inventaris,
            $barang->nama_barang,
            $barang->kategori->nama_kategori ?? '-',
            $barang->lokasi->nama_sub_lokasi ?? '-',
            $barang->jumlah ?? 1,
            $barang->harga_satuan ?? 0,
            ($barang->jumlah ?? 1) * ($barang->harga_satuan ?? 0),
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
                
                // Add total row
                $event->sheet->getDelegate()->setCellValue('G' . ($highestRow + 1), 'TOTAL:');
                $event->sheet->getDelegate()->setCellValue('H' . ($highestRow + 1), '=SUM(H2:H' . $highestRow . ')');
                
                // Style total row
                $event->sheet->getDelegate()->getStyle('G' . ($highestRow + 1) . ':H' . ($highestRow + 1))
                    ->getFont()->setBold(true);
            },
        ];
    }
}
