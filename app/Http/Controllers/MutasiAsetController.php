<?php

namespace App\Http\Controllers;

use App\Models\MutasiAset;
use App\Models\PengadaanBarang;
use App\Models\MasterSubLokasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MutasiAsetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:mutasi-aset list|mutasi-aset-create|mutasi-aset-edit|mutasi-aset-delete', only: ['index','show']),
            new Middleware('permission:mutasi-aset create', only: ['create','store']),
            new Middleware('permission:mutasi-aset edit', only: ['edit','update']),
            new Middleware('permission:mutasi-aset delete', only: ['destroy']),
            new Middleware('permission:mutasi-aset approve', only: ['approve','reject']),
        ];
    }

    public function index(Request $request)
    {
        $query = MutasiAset::with(['pengadaan', 'lokasiAsal', 'lokasiTujuan', 'createdBy', 'approvedBy']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pengadaan', function($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                  ->orWhere('nama_barang', 'like', "%$search%");
            });
        }

        if ($request->has('jenis_mutasi')) {
            $query->where('jenis_mutasi', $request->jenis_mutasi);
        }

        if ($request->has('status')) {
            $query->where('status_mutasi', $request->status);
        }

        $mutasi = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('mutasi-aset.index', compact('mutasi'));
    }

    public function create()
    {
        $pengadaanBarang = PengadaanBarang::aktif()->get();
        $lokasi = MasterSubLokasi::all();
        $users = User::all();

        return view('mutasi-aset.create', compact('pengadaanBarang', 'lokasi', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengadaan_barang_id' => 'required|exists:pengadaan_barang,id',
            'jenis_mutasi' => 'required|in:pindah_lokasi,ubah_pengguna,non_aktif,barang_keluar,penghapusan,peminjaman,pengembalian',
            'tanggal_mutasi' => 'required|date',
            'lokasi_asal_id' => 'nullable|exists:master_sub_lokasi,id',
            'lokasi_tujuan_id' => 'nullable|exists:master_sub_lokasi,id',
            'pengguna_asal' => 'nullable|string',
            'pengguna_tujuan' => 'nullable|string',
            'alasan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['pengadaan_id'] = $request->pengadaan_barang_id; // Map pengadaan_barang_id to pengadaan_id
        $data['created_by'] = Auth::id();
        $data['status_mutasi'] = 'pending';
        
        // Remove pengadaan_barang_id karena tidak ada di tabel
        unset($data['pengadaan_barang_id']);

        MutasiAset::create($data);

        return redirect()->route('mutasi-aset.index')->with('success', 'Mutasi aset berhasil dibuat');
    }

    public function show(MutasiAset $mutasiAset)
    {
        $mutasiAset->load(['pengadaan', 'lokasiAsal', 'lokasiTujuan', 'createdBy', 'approvedBy']);
        return view('mutasi-aset.show', compact('mutasiAset'));
    }

    public function edit(MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status_mutasi != 'pending') {
            return redirect()->route('mutasi-aset.index')->with('error', 'Hanya mutasi dengan status pending yang dapat diedit');
        }

        $pengadaanBarang = PengadaanBarang::aktif()->get();
        $lokasi = MasterSubLokasi::all();
        $users = User::all();

        return view('mutasi-aset.edit', compact('mutasiAset', 'pengadaanBarang', 'lokasi', 'users'));
    }

    public function update(Request $request, MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status_mutasi != 'pending') {
            return redirect()->route('mutasi-aset.index')->with('error', 'Hanya mutasi dengan status pending yang dapat diedit');
        }

        $request->validate([
            'pengadaan_barang_id' => 'required|exists:pengadaan_barang,id',
            'jenis_mutasi' => 'required|in:pindah_lokasi,ubah_pengguna,non_aktif,barang_keluar,penghapusan,peminjaman,pengembalian',
            'tanggal_mutasi' => 'required|date',
            'lokasi_asal_id' => 'nullable|exists:master_sub_lokasi,id',
            'lokasi_tujuan_id' => 'nullable|exists:master_sub_lokasi,id',
            'pengguna_asal' => 'nullable|string',
            'pengguna_tujuan' => 'nullable|string',
            'alasan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['pengadaan_id'] = $request->pengadaan_barang_id;
        unset($data['pengadaan_barang_id']);

        $mutasiAset->update($data);

        return redirect()->route('mutasi-aset.index')->with('success', 'Mutasi aset berhasil diupdate');
    }

    public function destroy(MutasiAset $mutasiAset)
    {
        if ($mutasiAset->status_mutasi != 'pending') {
            return redirect()->route('mutasi-aset.index')->with('error', 'Hanya mutasi dengan status pending yang dapat dihapus');
        }

        $mutasiAset->delete();

        return redirect()->route('mutasi-aset.index')->with('success', 'Mutasi aset berhasil dihapus');
    }

    public function approve(Request $request, MutasiAset $mutasiAset)
    {
        $request->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $mutasiAset->update([
                'status_mutasi' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_approval' => $request->catatan_approval,
            ]);

            // Update pengadaan_barang based on jenis_mutasi
            $pengadaan = $mutasiAset->pengadaan;

            switch ($mutasiAset->jenis_mutasi) {
                case 'pindah_lokasi':
                    $pengadaan->update(['lokasi_id' => $mutasiAset->lokasi_tujuan_id]);
                    break;
                case 'ubah_pengguna':
                    $pengadaan->update(['current_user' => $mutasiAset->pengguna_tujuan]);
                    break;
                case 'non_aktif':
                case 'barang_keluar':
                case 'penghapusan':
                    $pengadaan->update(['is_active' => false]);
                    break;
            }

            DB::commit();
            return redirect()->route('mutasi-aset.index')->with('success', 'Mutasi aset berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, MutasiAset $mutasiAset)
    {
        $request->validate([
            'catatan_approval' => 'required|string',
        ]);

        $mutasiAset->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->route('mutasi-aset.index')->with('success', 'Mutasi aset ditolak');
    }
}
