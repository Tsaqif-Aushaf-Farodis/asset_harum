<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use App\Models\MasterLokasi;
use App\Models\PengadaanBarang;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnggaranController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:anggaran view', only: ['index']),
            new Middleware('permission:anggaran create', only: ['create', 'store']),
            new Middleware('permission:anggaran edit', only: ['edit', 'update']),
            new Middleware('permission:anggaran delete', only: ['destroy']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Anggaran::with('lokasi')->withRealisasi()->orderByDesc('tahun')->orderBy('lokasi_id');

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        $anggaran = $query->paginate(15);

        $tahunList = Anggaran::select('tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');
        $lokasiList = MasterLokasi::orderBy('nama_lokasi')->pluck('nama_lokasi', 'id');

        // Pengadaan yang belum dikaitkan ke anggaran (data lama / belum diisi)
        $tanpaAnggaran = [
            'jumlah' => PengadaanBarang::whereNull('anggaran_id')->count(),
            'nilai' => (float) PengadaanBarang::whereNull('anggaran_id')->where('status', '!=', 'hibah')->sum('total_harga'),
        ];

        return view('anggaran.index', compact('anggaran', 'tahunList', 'lokasiList', 'tanpaAnggaran'));
    }

    public function create(): View
    {
        $anggaran = new Anggaran(['tahun' => date('Y'), 'is_active' => true]);
        $lokasiList = MasterLokasi::orderBy('nama_lokasi')->pluck('nama_lokasi', 'id')->toArray();

        return view('anggaran.create', compact('anggaran', 'lokasiList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules($request));
        $data['is_active'] = $request->boolean('is_active');
        $data['created_by'] = auth()->id();

        Anggaran::create($data);

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil dibuat');
    }

    public function edit(Anggaran $anggaran): View
    {
        $lokasiList = MasterLokasi::orderBy('nama_lokasi')->pluck('nama_lokasi', 'id')->toArray();

        return view('anggaran.edit', compact('anggaran', 'lokasiList'));
    }

    public function update(Request $request, Anggaran $anggaran): RedirectResponse
    {
        $data = $request->validate($this->rules($request, $anggaran));
        $data['is_active'] = $request->boolean('is_active');
        $data['updated_by'] = auth()->id();

        $anggaran->update($data);

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil diperbarui');
    }

    public function destroy(Anggaran $anggaran): RedirectResponse
    {
        if ($anggaran->pengadaan()->exists()) {
            return redirect()->route('anggaran.index')
                ->with('error', 'Anggaran ini sudah dipakai oleh data pengadaan dan tidak dapat dihapus. Nonaktifkan saja bila tidak dipakai lagi.');
        }

        $anggaran->delete();

        return redirect()->route('anggaran.index')->with('success', 'Anggaran berhasil dihapus');
    }

    private function rules(Request $request, ?Anggaran $anggaran = null): array
    {
        return [
            'tahun' => 'required|digits:4|integer|min:2000|max:' . (date('Y') + 5),
            'lokasi_id' => [
                'required',
                'exists:master_lokasi,id',
                Rule::unique('anggaran')
                    ->where('tahun', $request->input('tahun'))
                    ->ignore($anggaran?->id),
            ],
            'pagu' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ];
    }
}
