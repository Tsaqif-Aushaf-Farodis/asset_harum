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
        $query = Peminjaman::with(['pengadaan.barang', 'peminjam', 'kondisiKembali', 'approvedBy']);

        if (!empty($this->filters['status_peminjaman'])) {
            $query->where('status_peminjaman', $this->filters['status_peminjaman']);
        }

        if (!empty($this->filters['tanggal_mulai']) && !empty($this->filters['tanggal_akhir'])) {
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
            $peminjaman->pengadaan->barang->nama_barang ?? '-',
            $peminjaman->peminjam_nama,
            $peminjaman->peminjam_telepon ?? '-',
            $peminjaman->tanggal_pinjam ? date('d/m/Y', strtotime($peminjaman->tanggal_pinjam)) : '-',
            $peminjaman->tanggal_rencana_kembali ? date('d/m/Y', strtotime($peminjaman->tanggal_rencana_kembali)) : '-',
            $peminjaman->tanggal_kembali_aktual ? date('d/m/Y', strtotime($peminjaman->tanggal_kembali_aktual)) : '-',
            $peminjaman->keperluan,
            ucfirst($peminjaman->status_peminjaman),
            $peminjaman->kondisiKembali->nama_status ?? '-',
            $peminjaman->approvedBy->name ?? '-',
        ];
    }

    public function title(): string
    {
        return 'Laporan Peminjaman';
    }
}
