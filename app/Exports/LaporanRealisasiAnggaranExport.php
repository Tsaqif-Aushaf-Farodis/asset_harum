<?php

namespace App\Exports;

use App\Services\LaporanAsetService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanRealisasiAnggaranExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    private int $index = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $data = LaporanAsetService::realisasiAnggaran($this->filters);

        $rows = $data['rows']->map(fn ($a) => [
            'lokasi' => $a->lokasi->nama_lokasi ?? '-',
            'tahun' => $a->tahun,
            'pagu' => (float) $a->pagu,
            'realisasi' => $a->realisasi,
            'sisa' => $a->sisa,
            'persen' => $a->persen_realisasi,
        ]);

        if ($data['tanpa_anggaran']['jumlah'] > 0) {
            $rows->push([
                'lokasi' => 'Belum dikaitkan anggaran (' . $data['tanpa_anggaran']['jumlah'] . ' pengadaan)',
                'tahun' => $data['tahun'],
                'pagu' => 0,
                'realisasi' => $data['tanpa_anggaran']['nilai'],
                'sisa' => '-',
                'persen' => '-',
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Tahun', 'Lokasi (Unit)', 'Pagu', 'Realisasi', 'Sisa', '% Realisasi'];
    }

    public function map($row): array
    {
        $this->index++;

        return [$this->index, $row['tahun'], $row['lokasi'], $row['pagu'], $row['realisasi'], $row['sisa'], $row['persen']];
    }

    public function title(): string
    {
        return 'Realisasi Anggaran';
    }
}
