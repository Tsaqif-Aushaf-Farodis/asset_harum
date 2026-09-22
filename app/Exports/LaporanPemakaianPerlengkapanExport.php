<?php

namespace App\Exports;

use App\Http\Controllers\PemakaianPerlengkapanController;
use App\Models\PemakaianPerlengkapan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanPemakaianPerlengkapanExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;
    private int $index = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        return PemakaianPerlengkapanController::filterQuery(
            PemakaianPerlengkapan::with(['pengadaan.barang', 'pengadaan.satuan', 'lokasi.lokasi']),
            $this->filters
        )->orderBy('tanggal_pemakaian')->orderBy('id')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pemakaian',
            'Nama Barang',
            'Jumlah',
            'Satuan',
            'Lokasi',
            'Pemakai',
            'Keperluan',
            'Harga Satuan',
            'Nilai Terpakai',
            'Kode Pengadaan',
        ];
    }

    public function map($row): array
    {
        $this->index++;

        return [
            $this->index,
            $row->tanggal_pemakaian->format('d/m/Y'),
            $row->pengadaan->barang->nama_barang ?? '-',
            $row->jumlah,
            $row->pengadaan->satuan->nama_satuan ?? '-',
            ($row->lokasi->lokasi->nama_lokasi ?? '-') . ' - ' . ($row->lokasi->nama_sub_lokasi ?? '-'),
            $row->pemakai,
            $row->keperluan,
            $row->harga_satuan,
            $row->nilai_terpakai,
            $row->pengadaan->kode_inventaris ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Pemakaian Perlengkapan';
    }
}
