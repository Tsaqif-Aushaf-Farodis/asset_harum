<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permohonan;
use App\Models\DetailPermohonan;
use App\Models\MasterBarang;
use App\Models\MasterSatuan;
use Illuminate\Support\Facades\DB;

class FormPermohonan extends Component
{
    public $tahun_anggaran = '';
    public $bidang = '';
    public $unit_kegiatan = '';
    public $keterangan = '';
    public $details = [];

    public $barangOptions = [];
    public $satuanOptions = [];

    public function mount()
    {
        $this->barangOptions = MasterBarang::pluck('nama_barang', 'id')->toArray();
        $this->satuanOptions = MasterSatuan::pluck('kode_satuan', 'id')->toArray();
        $this->details = [
            ['barang_id' => '', 'volume' => 1, 'satuan' => '', 'harga' => 0, 'jumlah' => 0, 'kode_ma' => '', 'keterangan' => '']
        ];
    }

    public function addDetail()
    {
        $this->details[] = ['barang_id' => '', 'volume' => 1, 'satuan' => '', 'harga' => 0, 'jumlah' => 0, 'kode_ma' => '', 'keterangan' => ''];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details);
    }

    public function updatedDetails($value, $name)
    {
        // $name format: details.0.harga
        $parts = explode('.', $name);
        if (count($parts) === 3) {
            [$parent, $index, $field] = $parts;
            $index = (int)$index;
            // Otomatis hitung jumlah
            if ($field === 'harga' || $field === 'volume') {
                $volume = $this->details[$index]['volume'] ?? 0;
                $harga = $this->details[$index]['harga'] ?? 0;
                $this->details[$index]['jumlah'] = (int)$volume * (int)$harga;
            }
            
        }
    }

    public function submit()
    {
        $this->validate([
            'tahun_anggaran' => 'required',
            'bidang' => 'required',
            'unit_kegiatan' => 'required',
            'details.*.barang_id' => 'required|exists:master_barang,id',
            'details.*.volume' => 'required|integer|min:1',
            'details.*.harga' => 'required|integer|min:0',
        ]);

        DB::transaction(function () {
            $permohonan = Permohonan::create([
                'tahun_anggaran' => $this->tahun_anggaran,
                'bidang' => $this->bidang,
                'unit_kegiatan' => $this->unit_kegiatan,
                'keterangan' => $this->keterangan,
            ]);
            foreach ($this->details as $detail) {
                $permohonan->details()->create($detail);
            }
        });

        session()->flash('success', 'Permohonan berhasil disimpan!');
        return redirect()->route('form-permohonan');
    }

    public function render()
    {
        return view('livewire.form-permohonan', [
            'barangOptions' => $this->barangOptions,
            'satuanOptions' => $this->satuanOptions,
        ]);
    }
}
