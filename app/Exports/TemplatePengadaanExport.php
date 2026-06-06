<?php

namespace App\Exports;

use App\Exports\Sheets\TemplateDataSheet;
use App\Exports\Sheets\TemplateReferensiSheet;
use App\Models\MasterSubLokasi;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TemplatePengadaanExport implements WithMultipleSheets
{
    protected MasterSubLokasi $subLokasi;

    public function __construct(MasterSubLokasi $subLokasi)
    {
        $this->subLokasi = $subLokasi;
    }

    public function sheets(): array
    {
        return [
            new TemplateDataSheet($this->subLokasi),
            new TemplateReferensiSheet($this->subLokasi),
        ];
    }
}
