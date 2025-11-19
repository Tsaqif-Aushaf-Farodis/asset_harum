<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permohonan;
use App\Models\DetailPermohonan;
use App\Models\MasterBarang;
use App\Models\MasterSatuan;
use Illuminate\Support\Facades\DB;

class FormDetailPermohonan extends Component
{
    public $details = [];

    public $barangOptions = [];
    public $satuanOptions = [];

    public function mount()
    {
        $this->barangOptions = MasterBarang::pluck('nama_barang', 'id');
        $this->satuanOptions = MasterSatuan::all();
    }
    public function render ()
    {
       foreach ($this->details as $i => $detail) {
            $volume = (float) ($detail['volume'] ?? 0);
            $harga = (float) ($detail['harga'] ?? 0);
            $this->details[$i]['jumlah'] = $volume * $harga;
        }

        return view('livewire.form-detail-permohonan', [
            'barangOptions' => $this->barangOptions,
            'satuanOptions' => $this->satuanOptions,
        ]);
    }

    public function addDetail()
    {
        $this->details[] = [
            'barang_id' => null,
            'satuan' => null, // ubah dari 'satuan_id'
            'volume' => 0,
            'harga' => 0,
            'jumlah' => 0,
            'kode_ma' => '',
            'keterangan' => ''
        ];
    }


     public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }
   
}
