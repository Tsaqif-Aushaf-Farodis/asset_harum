<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permohonan;
use App\Models\DetailPermohonan;
use App\Models\MasterBarang;
use App\Models\MasterSatuan;

class PermohonanForm extends Component
{
    public $bidang, $tahun_anggaran, $unit_kegiatan, $keterangan;

    public $details = [];

    public function mount()
    {
        $this->details[] = [
            'barang_id' => '',
            'volume' => 0,
            'satuan' => '',
            'harga' => 0,
            'jumlah' => 0,
            'kode_ma' => '',
            'keterangan' => '',
        ]; // Mulai dengan 1 baris
    }

    public function addDetail()
    {
        $this->details[] = [
            'barang_id' => '',
            'volume' => 0,
            'satuan' => '',
            'harga' => 0,
            'jumlah' => 0,
            'kode_ma' => '',
            'keterangan' => '',
        ];
    }

    public function removeDetail($index)
    {
        unset($this->details[$index]);
        $this->details = array_values($this->details); // reset index
    }

    public function updatedDetails($value, $key)
    {
        [$index, $field] = explode('.', $key);

        $volume = (int) ($this->details[$index]['volume'] ?? 0);
        $harga = (int) str_replace(['.', ','], '', $this->details[$index]['harga'] ?? 0); // amanin titik

        $this->details[$index]['jumlah'] = $volume * $harga;
    }

    public function save()
    {
        $this->validate([
            'bidang' => 'required',
            'tahun_anggaran' => 'required',
            'unit_kegiatan' => 'required',
            'details.*.barang_id' => 'required|exists:master_barang,id',
            'details.*.volume' => 'required|numeric',
            'details.*.satuan' => 'required',
            'details.*.harga' => 'required|numeric',
            'details.*.jumlah' => 'required|numeric',
            'details.*.kode_ma' => 'nullable|string',
        ]);

        $permohonan = Permohonan::create([
            'bidang' => $this->bidang,
            'tahun_anggaran' => $this->tahun_anggaran,
            'unit_kegiatan' => $this->unit_kegiatan,
            'keterangan' => $this->keterangan,
            'status' => 'draft',
        ]);

        foreach ($this->details as $detail) {
            $detail['permohonan_id'] = $permohonan->id;
            DetailPermohonan::create($detail);
        }

        session()->flash('message', 'Permohonan berhasil disimpan!');
        return redirect()->route('permohonan.index'); // sesuaikan route
    }

    public function render()
    {
        return view('livewire.permohonan-form', [
            'barangOptions' => MasterBarang::all(),
            'satuanOptions' => MasterSatuan::all(),

        ]);
    }
}
