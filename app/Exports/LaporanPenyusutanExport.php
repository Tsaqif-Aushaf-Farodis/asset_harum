<?php

namespace App\Exports;

use App\Services\LaporanAsetService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanPenyusutanExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    private int $index = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return LaporanAsetService::penyusutan($this->filters)['rows'];
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Tanggal Perolehan',
            'Nilai Perolehan',
            'Disusutkan',
            'Turun Setiap (tahun)',
            'Penyusutan per Langkah',
            'Langkah Berjalan',
            'Akumulasi Penyusutan',
            'Nilai Buku',
            'Penurunan Berikutnya',
        ];
    }

    public function map($row): array
    {
        $this->index++;
        $p = $row->pengadaan;
        $s = $row->susut;

        return [
            $this->index,
            $p->kode_inventaris,
            $p->barang->nama_barang ?? '-',
            $p->barang->kategori->nama_kategori_barang ?? '-',
            $p->lokasi->nama_sub_lokasi ?? '-',
            date('d/m/Y', strtotime($p->tanggal_pengadaan)),
            $s['nilai_perolehan'],
            $s['disusutkan'] ? 'Ya' : 'Tidak',
            $s['disusutkan'] ? $s['interval_tahun'] : '-',
            $s['penyusutan_per_langkah'],
            $s['disusutkan'] ? $s['langkah_berjalan'] . ' / ' . $s['jumlah_langkah'] : '-',
            $s['akumulasi'],
            $s['nilai_buku'],
            $s['tanggal_penyusutan_berikutnya']?->format('d/m/Y') ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Penyusutan';
    }
}
