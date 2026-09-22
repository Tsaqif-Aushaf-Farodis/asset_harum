<?php

namespace App\Http\Controllers;

use App\Models\MasterSubLokasi;
use App\Services\PerlengkapanService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class StokPerlengkapanController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:perlengkapan view', only: ['index']),
        ];
    }

    public function index(Request $request): View
    {
        $batch = PerlengkapanService::batch($request->only(['lokasi_id', 'search', 'hanya_sisa']));
        $ringkasan = PerlengkapanService::ringkasan($batch);
        $lokasiList = MasterSubLokasi::with('lokasi')->get();

        $total = [
            'jenis_barang' => $ringkasan->count(),
            'sisa' => $ringkasan->sum('sisa'),
            'nilai_persediaan' => $ringkasan->sum('nilai_persediaan'),
        ];

        return view('stok-perlengkapan.index', compact('batch', 'ringkasan', 'lokasiList', 'total'));
    }
}
