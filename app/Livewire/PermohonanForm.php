<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Permohonan;
use App\Models\DetailPermohonan;
use App\Models\MasterBarang;
use App\Models\MasterSatuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermohonanForm extends Component
{
    public $bidang, $tahun_anggaran, $unit_kegiatan, $keterangan;
    public $details = [];

    protected $rules = [
        'bidang' => 'required|string|max:255',
        'tahun_anggaran' => 'required|digits:4',
        'unit_kegiatan' => 'required|string|max:255',
        'keterangan' => 'nullable|string',
        'details.*.barang_id' => 'required|exists:master_barang,id',
        'details.*.volume' => 'required|numeric|min:1',
        'details.*.satuan' => 'required|string',
        'details.*.harga' => 'required|numeric|min:0',
        'details.*.kode_ma' => 'nullable|string|max:50',
        'details.*.keterangan' => 'nullable|string',
    ];

    protected $messages = [
        'bidang.required' => 'Bidang harus diisi',
        'tahun_anggaran.required' => 'Tahun anggaran harus diisi',
        'unit_kegiatan.required' => 'Unit kegiatan harus diisi',
        'details.*.barang_id.required' => 'Barang harus dipilih',
        'details.*.volume.required' => 'Volume harus diisi',
        'details.*.satuan.required' => 'Satuan harus dipilih',
        'details.*.harga.required' => 'Harga harus diisi',
    ];

    public function mount()
    {
        // Set default tahun anggaran
        $this->tahun_anggaran = date('Y');
        
        // Tambah 1 baris default
        $this->addDetail();
    }

    public function addDetail()
    {
        $this->details[] = [
            'barang_id' => '',
            'volume' => 1,
            'satuan' => '',
            'harga' => 0,
            'jumlah' => 0,
            'kode_ma' => '',
            'keterangan' => '',
        ];
    }

    public function removeDetail($index)
    {
        if (count($this->details) > 1) {
            unset($this->details[$index]);
            $this->details = array_values($this->details);
        } else {
            session()->flash('error', 'Minimal harus ada 1 detail barang');
        }
    }

    public function updated($propertyName)
    {
        // Auto-calculate jumlah when volume or harga changes
        if (str_contains($propertyName, 'details.') && 
            (str_contains($propertyName, '.volume') || str_contains($propertyName, '.harga'))) {
            
            $parts = explode('.', $propertyName);
            $index = $parts[1];
            
            $volume = (float) ($this->details[$index]['volume'] ?? 0);
            $harga = (float) ($this->details[$index]['harga'] ?? 0);
            
            $this->details[$index]['jumlah'] = $volume * $harga;
        }
    }

    public function save()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $permohonan = Permohonan::create([
                'bidang' => $this->bidang,
                'tahun_anggaran' => $this->tahun_anggaran,
                'unit_kegiatan' => $this->unit_kegiatan,
                'keterangan' => $this->keterangan,
                'status' => 'pending',
                'created_by' => Auth::id(),
            ]);

            foreach ($this->details as $detail) {
                DetailPermohonan::create([
                    'permohonan_id' => $permohonan->id,
                    'barang_id' => $detail['barang_id'],
                    'volume' => $detail['volume'],
                    'satuan' => $detail['satuan'],
                    'harga' => $detail['harga'],
                    'jumlah' => $detail['jumlah'],
                    'kode_ma' => $detail['kode_ma'] ?? null,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();
            session()->flash('success', 'Permohonan berhasil disimpan!');
            return redirect()->route('permohonan.index');
            
        } catch (\Exception $e) {
            DB::rollback();
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.permohonan-form', [
            'barangOptions' => MasterBarang::where('is_active', true)
                ->where('status_permohonan', 'pending')
                ->orderBy('nama_barang')
                ->get(),
            'satuanOptions' => MasterSatuan::orderBy('nama_satuan')->get(),
        ]);
    }
}
