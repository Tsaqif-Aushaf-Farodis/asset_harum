<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterSubLokasi;
use App\Models\PemakaianPerlengkapan;
use App\Services\PerlengkapanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class PemakaianPerlengkapanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pemakaian view', only: ['index']),
            new Middleware('permission:pemakaian create', only: ['create', 'store']),
            new Middleware('permission:pemakaian delete', only: ['destroy']),
        ];
    }

    /** Riwayat pemakaian dengan filter periode/barang/lokasi/pemakai. */
    public function index(Request $request): View
    {
        $query = self::filterQuery(PemakaianPerlengkapan::with(['pengadaan.barang', 'pengadaan.satuan', 'lokasi.lokasi', 'createdBy']), $request->all())
            ->orderByDesc('tanggal_pemakaian')
            ->orderByDesc('id');

        $ringkasan = [
            'jumlah_transaksi' => (clone $query)->count(),
            'nilai_terpakai' => (float) (clone $query)->selectRaw('COALESCE(SUM(jumlah * harga_satuan), 0) as total')->reorder()->value('total'),
        ];

        $pemakaian = $query->paginate(20);

        $barangList = MasterBarang::perlengkapan()->orderBy('nama_barang')->pluck('nama_barang', 'id');
        $lokasiList = MasterSubLokasi::with('lokasi')->get();

        return view('pemakaian-perlengkapan.index', compact('pemakaian', 'ringkasan', 'barangList', 'lokasiList'));
    }

    public function create(): View
    {
        $sisa = PerlengkapanService::sisaPerBarang();

        $barangList = MasterBarang::approved()->perlengkapan()->orderBy('nama_barang')->get()
            ->filter(fn ($b) => ($sisa[$b->id] ?? 0) > 0)
            ->mapWithKeys(fn ($b) => [$b->id => $b->nama_barang . ' (sisa ' . $sisa[$b->id] . ')'])
            ->toArray();

        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(
            fn ($item) => [$item->id => $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi]
        )->toArray();

        return view('pemakaian-perlengkapan.create', compact('barangList', 'lokasiList'));
    }

    public function store(Request $request, PerlengkapanService $service): RedirectResponse
    {
        $data = $request->validate([
            'barang_id' => 'required|exists:master_barang,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_pemakaian' => 'required|date|before_or_equal:today',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'pemakai' => 'required|string|max:150',
            'keperluan' => 'nullable|string',
        ]);

        $hasil = $service->catat(
            (int) $data['barang_id'],
            (int) $data['jumlah'],
            $data['tanggal_pemakaian'],
            (int) $data['lokasi_id'],
            $data['pemakai'],
            $data['keperluan'] ?? null,
            auth()->id(),
        );

        $pesan = 'Pemakaian ' . $data['jumlah'] . ' unit berhasil dicatat';
        if ($hasil->count() > 1) {
            $pesan .= ' (diambil dari ' . $hasil->count() . ' pengadaan, yang paling lama lebih dulu)';
        }

        return redirect()->route('pemakaian-perlengkapan.index')->with('success', $pesan . '.');
    }

    /** Batal pemakaian: soft delete, stok otomatis kembali. */
    public function destroy(PemakaianPerlengkapan $pemakaian_perlengkapan): RedirectResponse
    {
        $pemakaian_perlengkapan->delete();

        return redirect()->route('pemakaian-perlengkapan.index')
            ->with('success', 'Pemakaian dibatalkan, stok dikembalikan.');
    }

    /**
     * Filter bersama untuk halaman riwayat & laporan pemakaian.
     *
     * @param  array{tanggal_mulai?: mixed, tanggal_akhir?: mixed, barang_id?: mixed, lokasi_id?: mixed, pemakai?: mixed}  $f
     */
    public static function filterQuery($query, array $f)
    {
        if (!empty($f['tanggal_mulai'])) {
            $query->whereDate('tanggal_pemakaian', '>=', $f['tanggal_mulai']);
        }

        if (!empty($f['tanggal_akhir'])) {
            $query->whereDate('tanggal_pemakaian', '<=', $f['tanggal_akhir']);
        }

        if (!empty($f['barang_id'])) {
            $query->whereHas('pengadaan', fn ($q) => $q->where('barang_id', $f['barang_id']));
        }

        if (!empty($f['lokasi_id'])) {
            $query->where('lokasi_id', $f['lokasi_id']);
        }

        if (!empty($f['pemakai'])) {
            $query->where('pemakai', 'like', '%' . $f['pemakai'] . '%');
        }

        return $query;
    }
}
