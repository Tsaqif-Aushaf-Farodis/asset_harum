<?php

namespace App\Http\Controllers;

use App\Models\Permohonan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Woo\GridView\DataProviders\EloquentDataProvider;

class PermohonanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permohonan view', only: ['index', 'show']),
            new Middleware('permission:permohonan create', only: ['create', 'store']),
            new Middleware('permission:permohonan edit', only: ['edit', 'update']),
            new Middleware('permission:permohonan delete', only: ['destroy']),
            new Middleware('permission:permohonan edit', only: ['approve', 'reject']),
        ];
    }

    public function index(Request $request): View
    {
        $query = Permohonan::with(['details', 'approvedBy'])->orderBy('created_at', 'desc');

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tahun anggaran
        if ($request->filled('tahun_anggaran')) {
            $query->where('tahun_anggaran', $request->tahun_anggaran);
        }

        // Filter berdasarkan bidang
        if ($request->filled('bidang')) {
            $query->where('bidang', 'like', '%' . $request->bidang . '%');
        }

        // Search global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('bidang', 'like', "%$search%")
                  ->orWhere('unit_kegiatan', 'like', "%$search%")
                  ->orWhere('keterangan', 'like', "%$search%");
            });
        }
        
        $permohonan = $query->paginate(15);

        // Statistik untuk dashboard
        $statistics = [
            'total' => Permohonan::count(),
            'pending' => Permohonan::where('status', 'pending')->count(),
            'approved' => Permohonan::where('status', 'approved')->count(),
            'rejected' => Permohonan::where('status', 'rejected')->count(),
        ];

        $tahunAnggaranList = Permohonan::select('tahun_anggaran')
            ->distinct()
            ->orderBy('tahun_anggaran', 'desc')
            ->pluck('tahun_anggaran');

        if ($request->header('HX-Request')) {
            return view('permohonan.includes.index-table', compact('permohonan'));
        }

        return view('permohonan.index', compact('permohonan', 'statistics', 'tahunAnggaranList'));
    }

    public function create(): View
    {
        $permohonan = new Permohonan();

        return view('permohonan.create', compact('permohonan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'bidang' => 'required|string|max:255',
            'tahun_anggaran' => 'required|digits:4|min:2000|max:' . (date('Y') + 5),
            'unit_kegiatan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validatedData['status'] = 'pending';
        $validatedData['created_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $permohonan = Permohonan::create($validatedData);
            
            DB::commit();
            return redirect()->route('permohonan.index')
                ->with('success', 'Permohonan berhasil dibuat dengan nomor: ' . $permohonan->id);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data: ' . $e->getMessage());
        }
    }

    public function show(Permohonan $permohonan): View
    {
        $permohonan->load(['details.barang', 'approvedBy', 'createdBy']);
        return view('permohonan.show', compact('permohonan'));
    }

    public function edit(Permohonan $permohonan): View
    {
        return view('permohonan.edit', compact('permohonan'));
    }

    public function update(Request $request, Permohonan $permohonan): RedirectResponse
    {
        // Cek apakah sudah disetujui/ditolak
        if (in_array($permohonan->status, ['approved', 'rejected'])) {
            return redirect()->back()
                ->with('error', 'Permohonan yang sudah disetujui/ditolak tidak dapat diubah.');
        }

        $validatedData = $request->validate([
            'bidang' => 'required|string|max:255',
            'tahun_anggaran' => 'required|digits:4|min:2000|max:' . (date('Y') + 5),
            'unit_kegiatan' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $validatedData['updated_by'] = Auth::id();

        try {
            $permohonan->update($validatedData);
            return redirect()->route('permohonan.index')
                ->with('success', 'Permohonan berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(Permohonan $permohonan): RedirectResponse
    {
        try {
            $permohonan->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('permohonan.index')
                    ->with('error', 'Data permohonan ini sudah digunakan dan tidak dapat dihapus.');
            }
            return redirect()->route('permohonan.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('permohonan.index')
            ->with('success', 'Permohonan berhasil dihapus');
    }

    public function approve(Request $request, Permohonan $permohonan): RedirectResponse
    {
        $request->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Update status permohonan
            $permohonan->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_approval' => $request->catatan_approval,
            ]);

            // Update status_permohonan barang yang ada di detail permohonan menjadi approved
            $permohonan->load('details');
            foreach ($permohonan->details as $detail) {
                if ($detail->barang_id) {
                    \App\Models\MasterBarang::where('id', $detail->barang_id)
                        ->update(['status_permohonan' => 'approved']);
                }
            }

            DB::commit();
            return redirect()->route('permohonan.index')->with('success', 'Permohonan berhasil disetujui dan barang sudah dapat digunakan untuk inventarisasi');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Permohonan $permohonan): RedirectResponse
    {
        $request->validate([
            'catatan_approval' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            // Update status permohonan
            $permohonan->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_approval' => $request->catatan_approval,
            ]);

            // Update status_permohonan barang yang ada di detail permohonan menjadi rejected
            $permohonan->load('details');
            foreach ($permohonan->details as $detail) {
                if ($detail->barang_id) {
                    \App\Models\MasterBarang::where('id', $detail->barang_id)
                        ->update(['status_permohonan' => 'rejected']);
                }
            }

            DB::commit();
            return redirect()->route('permohonan.index')->with('success', 'Permohonan ditolak');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}