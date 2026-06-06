<?php

namespace App\Exports\Sheets;

use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use App\Models\MasterSatuan;
use App\Models\MasterStatus;
use App\Models\MasterSubLokasi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TemplateReferensiSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    protected MasterSubLokasi $subLokasi;

    public function __construct(MasterSubLokasi $subLokasi)
    {
        $this->subLokasi = $subLokasi;
    }

    /**
     * Header row + reference lists laid out in fixed columns (A..H) so the
     * Data sheet dropdowns can reference exact ranges. Instruction text lives
     * in column J onward and does not affect those ranges.
     */
    public function array(): array
    {
        $barang = MasterBarang::approved()->with('kategori')->orderBy('kode_barang')->get();
        $kategori = KategoriBarang::orderBy('kode_kategori_barang')->get();
        $satuan = MasterSatuan::where('is_active', 1)->orderBy('kode_satuan')->get();
        $status = MasterStatus::orderBy('kode_status')->get();

        $instruksi = [
            'CARA PENGISIAN TEMPLATE',
            '1. Lokasi & Sub Lokasi sudah ditentukan saat download (tidak perlu diisi).',
            '2. BARANG SUDAH ADA: cukup isi kolom kode_barang sesuai daftar Kode Barang.',
            '3. BARANG BARU: isi kode_barang (baru) + nama_barang + kode_kategori_barang.',
            '4. merk_barang / tipe_barang / tahun_barang: opsional (khusus barang baru).',
            '5. sumber: opsional, isi kode mata anggaran secara manual.',
            '6. status: pilih baru / bekas / hibah.',
            '7. kondisi: isi sesuai daftar Kode Kondisi.',
            '8. tanggal_pengadaan: format YYYY-MM-DD, tidak boleh melebihi hari ini.',
            '9. jumlah: angka bulat minimal 1.',
            '10. kode_satuan: pilih dari daftar Kode Satuan.',
            '11. harga_satuan: angka tanpa pemisah ribuan. Total dihitung otomatis.',
        ];

        $rows = [];
        $rows[] = [
            'Kode Barang', 'Nama Barang',
            'Kode Kategori', 'Nama Kategori',
            'Kode Satuan', 'Nama Satuan',
            'Kode Kondisi', 'Nama Kondisi',
            '', 'PETUNJUK',
        ];

        $max = max($barang->count(), $kategori->count(), $satuan->count(), $status->count(), count($instruksi));

        for ($i = 0; $i < $max; $i++) {
            $rows[] = [
                $barang[$i]->kode_barang ?? '',
                $barang[$i]->nama_barang ?? '',
                $kategori[$i]->kode_kategori_barang ?? '',
                $kategori[$i]->nama_kategori_barang ?? '',
                $satuan[$i]->kode_satuan ?? '',
                $satuan[$i]->nama_satuan ?? '',
                $status[$i]->kode_status ?? '',
                $status[$i]->nama_status ?? '',
                '',
                $instruksi[$i] ?? '',
            ];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Referensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 30,
            'C' => 16, 'D' => 26,
            'E' => 14, 'F' => 20,
            'G' => 14, 'H' => 20,
            'I' => 3,  'J' => 70,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
