<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Read-only import used with Excel::toCollection(). The heading row (row 1 of
 * the "Data Pengadaan" sheet) becomes snake_case keys matching the template
 * headings. No persistence happens here — validation and saving are handled by
 * PengadaanBarangController so we can preview before committing.
 */
class PengadaanBarangImport implements WithHeadingRow
{
}
