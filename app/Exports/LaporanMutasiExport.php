<?php

namespace App\Exports;

use App\Models\MutasiAset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanMutasiExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = MutasiAset::with(['pengadaan', 'lokasiAsal', 'lokasiTujuan', 'createdBy', 'approvedBy']);

        if (isset($this->filters['jenis_mutasi'])) {
            $query->where('jenis_mutasi', $this->filters['jenis_mutasi']);
        }

        if (isset($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (isset($this->filters['tanggal_mulai']) && isset($this->filters['tanggal_akhir'])) {
            $query->whereBetween('tanggal_mutasi', [$this->filters['tanggal_mulai'], $this->filters['tanggal_akhir']]);
        }

        return $query->orderBy('tanggal_mutasi', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Mutasi',
            'Kode Inventaris',
            'Nama Barang',
            'Jenis Mutasi',
            'Lokasi Asal',
            'Lokasi Tujuan',
            'Pengguna Asal',
            'Pengguna Tujuan',
            'Alasan',
            'Status',
            'Dibuat Oleh',
            'Disetujui Oleh',
        ];
    }

    public function map($mutasi): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $mutasi->tanggal_mutasi ? date('d/m/Y', strtotime($mutasi->tanggal_mutasi)) : '-',
            $mutasi->pengadaan->kode_inventaris ?? '-',
            $mutasi->pengadaan->nama_barang ?? '-',
            ucwords(str_replace('_', ' ', $mutasi->jenis_mutasi)),
            $mutasi->lokasiAsal->nama_sub_lokasi ?? '-',
            $mutasi->lokasiTujuan->nama_sub_lokasi ?? '-',
            $mutasi->pengguna_asal ?? '-',
            $mutasi->pengguna_tujuan ?? '-',
            $mutasi->alasan,
            ucfirst($mutasi->status),
            $mutasi->createdBy->name ?? '-',
            $mutasi->approvedBy->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Mutasi';
    }
}
