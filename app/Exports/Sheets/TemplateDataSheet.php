<?php

namespace App\Exports\Sheets;

use App\Models\KategoriBarang;
use App\Models\MasterSatuan;
use App\Models\MasterStatus;
use App\Models\MasterSubLokasi;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class TemplateDataSheet implements FromArray, WithHeadings, WithTitle, WithColumnWidths, WithEvents
{
    /**
     * Number of rows (below the heading) to pre-arm with dropdown validation.
     */
    private const VALIDATION_ROWS = 50;

    protected MasterSubLokasi $subLokasi;

    public function __construct(MasterSubLokasi $subLokasi)
    {
        $this->subLokasi = $subLokasi;
    }

    /**
     * Heading row — these slugs become the import heading-row keys. Keep in sync
     * with PengadaanBarangController::validateImportRow().
     */
    public function headings(): array
    {
        return [
            'kode_barang',
            'nama_barang',
            'kode_kategori_barang',
            'merk_barang',
            'tipe_barang',
            'tahun_barang',
            'sumber',
            'status',
            'kode_kondisi',
            'tanggal_pengadaan',
            'jumlah',
            'kode_satuan',
            'harga_satuan',
            'keterangan',
            // Khusus barang BARU (kode_barang belum ada di master):
            'jenis_barang',
            'disusutkan',
            'masa_pemakaian',
            'interval_penyusutan',
            'butuh_perawatan',
        ];
    }

    /**
     * A few blank rows so it is obvious where to type.
     */
    public function array(): array
    {
        return array_fill(0, 5, array_fill(0, 19, ''));
    }

    public function title(): string
    {
        return 'Data Pengadaan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22, 'B' => 28, 'C' => 18, 'D' => 16, 'E' => 16,
            'F' => 12, 'G' => 18, 'H' => 12, 'I' => 14, 'J' => 18,
            'K' => 10, 'L' => 14, 'M' => 16, 'N' => 28,
            'O' => 16, 'P' => 12, 'Q' => 16, 'R' => 18, 'S' => 16,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $end = self::VALIDATION_ROWS + 1; // header is row 1

                $sheet->getStyle('A1:S1')->getFont()->setBold(true);
                $sheet->freezePane('A2');

                $kategoriRows = max(1, KategoriBarang::count());
                $satuanRows = max(1, MasterSatuan::where('is_active', 1)->count());
                $kondisiRows = max(1, MasterStatus::count());

                // status (enum) — literal list
                $this->applyList($sheet, 'H', $end, '"baru,bekas,hibah"');
                // kode_kategori_barang -> Referensi column C
                $this->applyList($sheet, 'C', $end, 'Referensi!$C$2:$C$' . (1 + $kategoriRows));
                // kode_kondisi -> Referensi column G
                $this->applyList($sheet, 'I', $end, 'Referensi!$G$2:$G$' . (1 + $kondisiRows));
                // kode_satuan -> Referensi column E
                $this->applyList($sheet, 'L', $end, 'Referensi!$E$2:$E$' . (1 + $satuanRows));
                // jenis_barang, disusutkan, butuh_perawatan (khusus barang baru) — literal list
                $this->applyList($sheet, 'O', $end, '"peralatan,perlengkapan"');
                $this->applyList($sheet, 'P', $end, '"ya,tidak"');
                $this->applyList($sheet, 'S', $end, '"ya,tidak"');
            },
        ];
    }

    private function applyList($sheet, string $col, int $endRow, string $formula): void
    {
        for ($row = 2; $row <= $endRow; $row++) {
            $validation = $sheet->getCell($col . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
            $validation->setAllowBlank(true);
            $validation->setShowInputMessage(true);
            $validation->setShowErrorMessage(true);
            $validation->setShowDropDown(true);
            $validation->setFormula1($formula);
        }
    }
}
