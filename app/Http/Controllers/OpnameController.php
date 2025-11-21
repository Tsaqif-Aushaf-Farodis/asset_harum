<?php

namespace App\Http\Controllers;

use App\Models\OpnameSession;
use App\Models\OpnameDetail;
use App\Models\PengadaanBarang;
use App\Models\MasterSubLokasi;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KertasKerjaOpnameExport;
use App\Exports\LaporanOpnameExport;

class OpnameController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:opname-list|opname-create|opname-edit|opname-delete', only: ['index','show']),
            new Middleware('permission:opname-create', only: ['create','store']),
            new Middleware('permission:opname-edit', only: ['edit','update','inputHasil']),
            new Middleware('permission:opname-complete', only: ['complete']),
        ];
    }

    public function index(Request $request)
    {
        $query = OpnameSession::with(['lokasi', 'createdBy']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('nama_opname', 'like', "%$search%")
                  ->orWhere('kode_opname', 'like', "%$search%");
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('opname.index', compact('sessions'));
    }

    public function create()
    {
        $lokasi = MasterSubLokasi::all();
        return view('opname.create', compact('lokasi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_opname' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'lokasi_id' => 'nullable|exists:master_sub_lokasi,id',
            'keterangan' => 'nullable|string',
        ]);

        $session = OpnameSession::create([
            'nama_opname' => $request->nama_opname,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lokasi_id' => $request->lokasi_id,
            'status' => 'draft',
            'keterangan' => $request->keterangan,
            'created_by' => Auth::id(),
        ]);

        // Generate opname details for all active assets
        $query = PengadaanBarang::aktif();
        
        if ($request->lokasi_id) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        $pengadaanBarang = $query->get();

        foreach ($pengadaanBarang as $barang) {
            OpnameDetail::create([
                'opname_session_id' => $session->id,
                'pengadaan_id' => $barang->id,
                'kondisi_sistem' => $barang->statusKondisi->nama_status ?? 'Baik',
                'status_keberadaan' => 'sesuai',
            ]);
        }

        return redirect()->route('opname.show', $session)->with('success', 'Sesi opname berhasil dibuat');
    }

    public function show(OpnameSession $opname)
    {
        $opname->load(['lokasi', 'createdBy', 'details.pengadaan']);
        
        $summary = [
            'total' => $opname->details->count(),
            'sesuai' => $opname->details->where('status_keberadaan', 'sesuai')->count(),
            'tidak_sesuai' => $opname->details->where('status_keberadaan', 'tidak_sesuai')->count(),
            'hilang' => $opname->details->where('status_keberadaan', 'hilang')->count(),
            'rusak' => $opname->details->where('status_keberadaan', 'rusak')->count(),
            'baru' => $opname->details->where('status_keberadaan', 'baru')->count(),
        ];

        return view('opname.show', compact('opname', 'summary'));
    }

    public function edit(OpnameSession $opname)
    {
        if (!in_array($opname->status, ['draft', 'ongoing'])) {
            return redirect()->route('opname.index')->with('error', 'Hanya opname draft/ongoing yang dapat diedit');
        }

        $lokasi = MasterSubLokasi::all();
        return view('opname.edit', compact('opname', 'lokasi'));
    }

    public function update(Request $request, OpnameSession $opname)
    {
        if (!in_array($opname->status, ['draft', 'ongoing'])) {
            return redirect()->route('opname.index')->with('error', 'Hanya opname draft/ongoing yang dapat diedit');
        }

        $request->validate([
            'nama_opname' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'lokasi_id' => 'nullable|exists:master_sub_lokasi,id',
            'keterangan' => 'nullable|string',
        ]);

        $opname->update($request->all());

        return redirect()->route('opname.show', $opname)->with('success', 'Sesi opname berhasil diupdate');
    }

    public function destroy(OpnameSession $opname)
    {
        if ($opname->status != 'draft') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname draft yang dapat dihapus');
        }

        $opname->delete();

        return redirect()->route('opname.index')->with('success', 'Sesi opname berhasil dihapus');
    }

    public function start(OpnameSession $opname)
    {
        if ($opname->status != 'draft') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname draft yang dapat dimulai');
        }

        $opname->update(['status' => 'ongoing']);

        return redirect()->route('opname.show', $opname)->with('success', 'Opname dimulai');
    }

    public function inputHasil(Request $request, OpnameSession $opname)
    {
        if ($opname->status != 'ongoing') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname ongoing yang dapat diinput hasilnya');
        }

        $opname->load(['details.pengadaan']);
        return view('opname.input-hasil', compact('opname'));
    }

    public function saveHasil(Request $request, OpnameSession $opname)
    {
        if ($opname->status != 'ongoing') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname ongoing yang dapat diinput hasilnya');
        }

        $request->validate([
            'details' => 'required|array',
            'details.*.id' => 'required|exists:opname_detail,id',
            'details.*.status_keberadaan' => 'required|in:sesuai,tidak_sesuai,hilang,rusak,baru',
            'details.*.kondisi_sesudah' => 'nullable|string',
            'details.*.keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->details as $detail) {
                OpnameDetail::find($detail['id'])->update([
                    'status_keberadaan' => $detail['status_keberadaan'],
                    'kondisi_sesudah' => $detail['kondisi_sesudah'] ?? null,
                    'keterangan' => $detail['keterangan'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('opname.show', $opname)->with('success', 'Hasil opname berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function complete(OpnameSession $opname)
    {
        if ($opname->status != 'ongoing') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname ongoing yang dapat diselesaikan');
        }

        $opname->update([
            'status' => 'completed',
            'tanggal_selesai' => now(),
        ]);

        return redirect()->route('opname.show', $opname)->with('success', 'Opname selesai');
    }

    public function exportKertasKerja(OpnameSession $opname)
    {
        return Excel::download(new KertasKerjaOpnameExport($opname), 'kertas-kerja-' . $opname->kode_opname . '.xlsx');
    }

    public function exportLaporan(OpnameSession $opname)
    {
        if ($opname->status != 'completed') {
            return redirect()->route('opname.index')->with('error', 'Hanya opname completed yang dapat diekspor laporannya');
        }

        return Excel::download(new LaporanOpnameExport($opname), 'laporan-opname-' . $opname->kode_opname . '.xlsx');
    }
}
