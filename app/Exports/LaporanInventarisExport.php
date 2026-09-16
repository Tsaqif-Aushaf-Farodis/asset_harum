<?php

namespace App\Exports;

use App\Models\PengadaanBarang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanInventarisExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = PengadaanBarang::with(['lokasi', 'kategori', 'satuan']);

        if (!empty($this->filters['lokasi_id'])) {
            $query->where('lokasi_id', $this->filters['lokasi_id']);
        }

        if (!empty($this->filters['kategori_id'])) {
            $query->where('kategori_id', $this->filters['kategori_id']);
        }

        if (isset($this->filters['is_active']) && $this->filters['is_active'] !== '') {
            $query->where('is_active', $this->filters['is_active']);
        }

        if (!empty($this->filters['tahun_perolehan'])) {
            $query->whereYear('tanggal_pengadaan', $this->filters['tahun_perolehan']);
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
            'Satuan',
            'Harga Satuan',
            'Total Nilai',
            'Tanggal Perolehan',
            'Kondisi',
            'Status',
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
            $barang->satuan->nama_satuan ?? '-',
            $barang->harga_satuan ?? 0,
            ($barang->jumlah ?? 1) * ($barang->harga_satuan ?? 0),
            $barang->tanggal_pengadaan ? date('d/m/Y', strtotime($barang->tanggal_pengadaan)) : '-',
            $barang->kondisi ?? '-',
            $barang->is_active ? 'Aktif' : 'Tidak Aktif',
        ];
    }

    public function title(): string
    {
        return 'Laporan Inventaris';
    }
}
