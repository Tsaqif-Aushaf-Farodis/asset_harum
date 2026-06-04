<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\PengadaanBarang;
use App\Models\User;
use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:peminjaman-list|peminjaman-create|peminjaman-edit|peminjaman-delete', only: ['index','show']),
            new Middleware('permission:peminjaman-create', only: ['create','store']),
            new Middleware('permission:peminjaman-edit', only: ['edit','update']),
            new Middleware('permission:peminjaman-delete', only: ['destroy']),
            new Middleware('permission:peminjaman-approve', only: ['approve','reject']),
            new Middleware('permission:peminjaman-return', only: ['pengembalian','storePengembalian']),
        ];
    }

    public function index(Request $request)
    {
        $query = Peminjaman::with(['pengadaan', 'peminjam', 'approvedBy']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pengadaan', function($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                  ->orWhere('nama_barang', 'like', "%$search%");
            })->orWhereHas('peminjam', function($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })->orWhere('nama_peminjam', 'like', "%$search%");
        }

        if ($request->has('status_peminjaman')) {
            $query->where('status_peminjaman', $request->status_peminjaman);
        }

        $peminjaman = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('peminjaman.index', compact('peminjaman'));
    }

    public function create()
    {
        $pengadaanBarang = PengadaanBarang::aktif()->where('is_borrowed', false)->get();
        $users = User::all();

        return view('peminjaman.create', compact('pengadaanBarang', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengadaan_barang_id' => 'required|exists:pengadaan_barang,id',
            'peminjam_id' => 'nullable|exists:users,id',
            'nama_peminjam' => 'required|string',
            'kontak_peminjam' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_pinjam',
            'keperluan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        // Check if barang is already borrowed
        $pengadaan = PengadaanBarang::find($request->pengadaan_barang_id);
        if ($pengadaan->is_borrowed) {
            return redirect()->back()->with('error', 'Barang sedang dipinjam');
        }

        Peminjaman::create([
            'pengadaan_id' => $request->pengadaan_barang_id,
            'peminjam_id' => $request->peminjam_id,
            'peminjam_nama' => $request->nama_peminjam ?? $request->peminjam_nama,
            'peminjam_nip' => $request->peminjam_nip,
            'peminjam_instansi' => $request->peminjam_instansi,
            'peminjam_telepon' => $request->kontak_peminjam ?? $request->peminjam_telepon,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,
            'keperluan' => $request->keperluan,
            'kondisi_pinjam_id' => $request->kondisi_pinjam_id ?? 1, // Default ke status 'Baik'
            'catatan' => $request->keterangan ?? $request->catatan,
            'status_peminjaman' => 'pending',
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dibuat');
    }

    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['pengadaan.barang', 'peminjam', 'kondisiPinjam', 'kondisiKembali', 'createdBy', 'approvedBy']);
        return view('peminjaman.show', compact('peminjaman'));
    }

    public function edit(Peminjaman $peminjaman)
    {
        if ($peminjaman->status_peminjaman != 'pending') {
            return redirect()->route('peminjaman.index')->with('error', 'Hanya peminjaman dengan status pending yang dapat diedit');
        }

        $pengadaanBarang = PengadaanBarang::aktif()->where('is_borrowed', false)->get();
        $users = User::all();

        return view('peminjaman.edit', compact('peminjaman', 'pengadaanBarang', 'users'));
    }

    public function update(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status_peminjaman != 'pending') {
            return redirect()->route('peminjaman.index')->with('error', 'Hanya peminjaman dengan status pending yang dapat diedit');
        }

        $request->validate([
            'pengadaan_barang_id' => 'required|exists:pengadaan_barang,id',
            'peminjam_id' => 'nullable|exists:users,id',
            'nama_peminjam' => 'required|string',
            'kontak_peminjam' => 'required|string',
            'tanggal_pinjam' => 'required|date',
            'tanggal_rencana_kembali' => 'required|date|after:tanggal_pinjam',
            'keperluan' => 'required|string',
            'keterangan' => 'nullable|string',
        ]);

        $data = [
            'pengadaan_id' => $request->pengadaan_barang_id,
            'peminjam_id' => $request->peminjam_id,
            'peminjam_nama' => $request->nama_peminjam ?? $request->peminjam_nama,
            'peminjam_nip' => $request->peminjam_nip,
            'peminjam_instansi' => $request->peminjam_instansi,
            'peminjam_telepon' => $request->kontak_peminjam ?? $request->peminjam_telepon,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_rencana_kembali' => $request->tanggal_rencana_kembali,
            'keperluan' => $request->keperluan,
            'kondisi_pinjam_id' => $request->kondisi_pinjam_id,
            'catatan' => $request->keterangan ?? $request->catatan,
        ];

        $peminjaman->update($data);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil diupdate');
    }

    public function destroy(Peminjaman $peminjaman)
    {
        if ($peminjaman->status_peminjaman != 'pending') {
            return redirect()->route('peminjaman.index')->with('error', 'Hanya peminjaman dengan status pending yang dapat dihapus');
        }

        $peminjaman->delete();

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus');
    }

    public function approve(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'catatan_approval' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman->update([
                'status_peminjaman' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'catatan_approval' => $request->catatan_approval,
            ]);

            // Update status to borrowed
            $peminjaman->update(['status_peminjaman' => 'borrowed']);

            // Update pengadaan_barang
            $peminjaman->pengadaan->update(['is_borrowed' => true]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil disetujui');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Peminjaman $peminjaman)
    {
        $request->validate([
            'catatan_approval' => 'required|string',
        ]);

        $peminjaman->update([
            'status_peminjaman' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'catatan_approval' => $request->catatan_approval,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman ditolak');
    }

    public function pengembalian(Peminjaman $peminjaman)
    {
        $statuses = MasterStatus::all();

        if ($peminjaman->status_peminjaman != 'borrowed') {
            return redirect()->route('peminjaman.index')->with('error', 'Hanya peminjaman yang dipinjam yang dapat dikembalikan');
        }

        return view('peminjaman.pengembalian', compact('peminjaman', 'statuses'));
    }

    public function storePengembalian(Request $request, Peminjaman $peminjaman)
    {
        if ($peminjaman->status_peminjaman != 'borrowed') {
            return redirect()->route('peminjaman.index')->with('error', 'Hanya peminjaman yang dipinjam yang dapat dikembalikan');
        }

        $request->validate([
            'tanggal_kembali' => 'required|date',
            'kondisi_kembali' => 'required|exists:master_status,id',
            'keterangan_pengembalian' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // $peminjaman->update([
            //     'status_peminjaman' => 'returned',
            //     'tanggal_kembali' => $request->tanggal_kembali,
            //     'kondisi_kembali' => $request->kondisi_kembali,
            //     'keterangan_pengembalian' => $request->keterangan_pengembalian,
            // ]);

            $peminjaman->update([
                'status_peminjaman'      => 'returned',
                'tanggal_kembali_aktual' => $request->tanggal_kembali,
                'kondisi_kembali_id'     => $request->kondisi_kembali,
                'catatan'                => $request->keterangan_pengembalian,
            ]);

            // Update pengadaan_barang
            $peminjaman->pengadaan->update(['is_borrowed' => false]);

            DB::commit();
            return redirect()->route('peminjaman.index')->with('success', 'Pengembalian berhasil dicatat');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function riwayat(Request $request)
    {
        $query = Peminjaman::with(['pengadaan', 'peminjam', 'approvedBy', 'kondisiKembali'])
                          ->whereIn('status_peminjaman', ['returned', 'overdue', 'lost']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('pengadaan', function($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                  ->orWhere('nama_barang', 'like', "%$search%");
            });
        }

        $riwayat = $query->orderBy('tanggal_kembali_aktual', 'desc')->paginate(10);

        return view('peminjaman.riwayat', compact('riwayat'));
    }
}
