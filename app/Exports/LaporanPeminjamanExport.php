<?php

namespace App\Exports;

use App\Models\Peminjaman;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanPeminjamanExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Peminjaman::with(['pengadaan', 'peminjam', 'approvedBy']);

        if (isset($this->filters['status_peminjaman'])) {
            $query->where('status_peminjaman', $this->filters['status_peminjaman']);
        }

        if (isset($this->filters['tanggal_mulai']) && isset($this->filters['tanggal_akhir'])) {
            $query->whereBetween('tanggal_pinjam', [$this->filters['tanggal_mulai'], $this->filters['tanggal_akhir']]);
        }

        return $query->orderBy('tanggal_pinjam', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode Inventaris',
            'Nama Barang',
            'Nama Peminjam',
            'Kontak',
            'Tanggal Pinjam',
            'Rencana Kembali',
            'Tanggal Kembali',
            'Keperluan',
            'Status',
            'Kondisi Kembali',
            'Disetujui Oleh',
        ];
    }

    public function map($peminjaman): array
    {
        static $index = 0;
        $index++;

        return [
            $index,
            $peminjaman->pengadaan->kode_inventaris ?? '-',
            $peminjaman->pengadaan->nama_barang ?? '-',
            $peminjaman->nama_peminjam,
            $peminjaman->kontak_peminjam,
            $peminjaman->tanggal_pinjam ? date('d/m/Y', strtotime($peminjaman->tanggal_pinjam)) : '-',
            $peminjaman->tanggal_rencana_kembali ? date('d/m/Y', strtotime($peminjaman->tanggal_rencana_kembali)) : '-',
            $peminjaman->tanggal_kembali_aktual ? date('d/m/Y', strtotime($peminjaman->tanggal_kembali_aktual)) : '-',
            $peminjaman->keperluan,
            ucfirst($peminjaman->status_peminjaman),
            $peminjaman->kondisi_kembali ?? '-',
            $peminjaman->approvedBy->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}
