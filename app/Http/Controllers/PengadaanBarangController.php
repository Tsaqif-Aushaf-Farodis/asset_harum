<?php

namespace App\Http\Controllers;

use App\Models\PengadaanBarang;
use App\Models\MasterBarang;
use App\Models\MasterLokasi;
use App\Models\MasterSubLokasi;
use App\Models\MasterStatus;
use App\Models\MasterSatuan;
use App\Models\KategoriBarang;
use App\Models\User;
use App\Models\Anggaran;
use App\Models\PemakaianPerlengkapan;
use App\Helpers\Format;
use App\Services\KodeInventarisGenerator;
use App\Services\NilaiBarangService;
use Illuminate\Validation\ValidationException;
use App\Exports\TemplatePengadaanExport;
use App\Imports\PengadaanBarangImport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use \Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Woo\GridView\DataProviders\EloquentDataProvider;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class PengadaanBarangController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pengadaan-barang view', only: ['index', 'show']),
            new Middleware('permission:pengadaan-barang create', only: ['create', 'store']),
            new Middleware('permission:pengadaan-barang edit', only: ['edit', 'update']),
            new Middleware('permission:pengadaan-barang delete', only: ['destroy']),
            new Middleware('permission:pengadaan-barang import', only: ['importForm', 'getSubLokasi', 'downloadTemplate', 'previewImport', 'storeImport']),
            new Middleware('permission:pengadaan-barang view', only: ['downloadQrCode', 'downloadQrCodeBulk']),
        ];
    }

    public function index(Request $request): View
    {
        $jenis = $request->route('jenis') ?? $request->query('jenis');
        $jenis = in_array($jenis, [MasterBarang::JENIS_PERALATAN, MasterBarang::JENIS_PERLENGKAPAN], true) ? $jenis : null;
        $perawatan = (bool) ($request->route('perawatan') ?? $request->boolean('perawatan'));

        $query = PengadaanBarang::with(['barang.kategori', 'lokasi.lokasi', 'satuan', 'statusKondisi', 'createdBy', 'anggaran.lokasi'])
            ->withSum('pemakaian', 'jumlah')
            ->join('master_barang', 'master_barang.id', '=', 'pengadaan_barang.barang_id')
            ->select('pengadaan_barang.*')
            ->orderBy('master_barang.nama_barang');

        // Filter berdasarkan jenis barang (Peralatan / Perlengkapan)
        if ($jenis) {
            $query->where('master_barang.jenis_barang', $jenis);
        }

        // Hanya barang yang butuh perawatan
        if ($perawatan) {
            $query->where('master_barang.butuh_perawatan', true);
        }

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi_id')) {
            $query->where('pengadaan_barang.lokasi_id', $request->lokasi_id);
        }

        // Filter berdasarkan kategori (kolom ada di master_barang)
        if ($request->filled('kategori_id')) {
            $query->where('master_barang.kategori_barang_id', $request->kategori_id);
        }

        // Filter berdasarkan status kondisi
        if ($request->filled('status_id')) {
            $query->where('pengadaan_barang.status_id', $request->status_id);
        }

        // Filter berdasarkan status aktif
        if ($request->filled('is_active')) {
            $query->where('pengadaan_barang.is_active', $request->is_active);
        }

        // Filter berdasarkan tahun perolehan
        if ($request->filled('tahun')) {
            $query->whereYear('pengadaan_barang.tanggal_pengadaan', $request->tahun);
        }

        // Search global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pengadaan_barang.kode_inventaris', 'like', "%$search%")
                  ->orWhere('pengadaan_barang.keterangan', 'like', "%$search%")
                  ->orWhereHas('barang', function($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%$search%");
                  });
            });
        }

        $pengadaanBarang = $query->paginate(20);

        $statistics = $this->statistik($jenis);

        // Data untuk filter dropdown
        $lokasiList = MasterSubLokasi::with('lokasi')->get();
        $kategoriList = \App\Models\KategoriBarang::all();
        $statusList = MasterStatus::all();
        $tahunList = PengadaanBarang::selectRaw('YEAR(tanggal_pengadaan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        $indexRoute = $request->route()->getName();

        if ($request->header('HX-Request')) {
            return view('pengadaan-barang.includes.index-table', compact('pengadaanBarang'));
        }

        // Daftar barang untuk modal pilih-barang di fitur download QR Code (multiple).
        // QR hanya untuk aset (Peralatan); Perlengkapan tidak memakai label QR.
        $allPengadaanBarangForQr = PengadaanBarang::peralatan()->with('barang')
            ->orderBy('kode_inventaris')
            ->get(['id', 'barang_id', 'kode_inventaris']);

        return view('pengadaan-barang.index', compact(
            'pengadaanBarang',
            'statistics',
            'lokasiList',
            'kategoriList',
            'statusList',
            'tahunList',
            'allPengadaanBarangForQr',
            'jenis',
            'perawatan',
            'indexRoute'
        ));
    }

    /**
     * Ringkasan kartu statistik. Perlengkapan menampilkan stok/persediaan,
     * selain itu menampilkan aset Peralatan.
     */
    private function statistik(?string $jenis): array
    {
        if ($jenis === MasterBarang::JENIS_PERLENGKAPAN) {
            $batch = PengadaanBarang::perlengkapan()->aktif()->with('barang')->withSum('pemakaian', 'jumlah')->get();

            return [
                'mode' => 'perlengkapan',
                'total_batch' => $batch->count(),
                'nilai_persediaan' => $batch->sum(fn ($b) => $b->nilaiSaatIni()),
                'nilai_terpakai' => (float) PemakaianPerlengkapan::selectRaw('COALESCE(SUM(jumlah * harga_satuan), 0) as total')->value('total'),
                'total_nilai_perolehan' => (float) PengadaanBarang::perlengkapan()->sum('total_harga'),
            ];
        }

        $aktif = PengadaanBarang::peralatan()->aktif()->with('barang')->get();

        return [
            'mode' => 'peralatan',
            'total_aset' => PengadaanBarang::peralatan()->count(),
            'total_aktif' => $aktif->count(),
            'total_dipinjam' => PengadaanBarang::peralatan()->where('is_borrowed', true)->count(),
            'total_nilai' => (float) $aktif->sum('total_harga'),
            'total_nilai_buku' => (float) $aktif->sum(fn ($a) => $a->nilaiSaatIni()),
        ];
    }

    /** Data pendukung form create/edit. */
    private function formData(): array
    {
        $barangModels = MasterBarang::approved()->orderBy('nama_barang')->get();
        $barangList = $barangModels->mapWithKeys(fn ($b) => [
            $b->id => $b->nama_barang . ($b->isPerlengkapan() ? ' (Perlengkapan)' : ''),
        ])->toArray();
        $barangMeta = $barangModels->mapWithKeys(fn ($b) => [
            $b->id => [
                'jenis' => $b->jenis_barang,
                'disusutkan' => (bool) $b->disusutkan,
            ],
        ]);

        $lokasiModels = MasterSubLokasi::with('lokasi')->get();
        $lokasiList = $lokasiModels->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        $lokasiInduk = $lokasiModels->pluck('lokasi_id', 'id');

        $anggaranModels = Anggaran::aktif()->with('lokasi')->withRealisasi()->orderByDesc('tahun')->get();
        $anggaranList = $anggaranModels->mapWithKeys(fn ($a) => [
            $a->id => $a->tahun . ' – ' . ($a->lokasi->nama_lokasi ?? '-') . ' (sisa ' . Format::rupiah($a->sisa) . ')',
        ])->toArray();
        $anggaranMap = $anggaranModels->mapWithKeys(fn ($a) => [$a->tahun . '-' . $a->lokasi_id => $a->id]);

        return [
            'barangList' => $barangList,
            'barangMeta' => $barangMeta,
            'lokasiList' => $lokasiList,
            'lokasiInduk' => $lokasiInduk,
            'statusList' => MasterStatus::pluck('nama_status', 'id')->toArray(),
            'satuanList' => MasterSatuan::pluck('nama_satuan', 'id')->toArray(),
            'anggaranList' => $anggaranList,
            'anggaranMap' => $anggaranMap,
        ];
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();

        return view('pengadaan-barang.create', array_merge(['pengadaanBarang' => $pengadaanBarang], $this->formData()));
    }

    /** Aturan validasi dasar untuk store & update. */
    private function rules(): array
    {
        return [
            'barang_id' => 'required|exists:master_barang,id',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'nullable|exists:master_status,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1',
            'satuan_id' => 'required|exists:master_satuan,id',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string',
            'anggaran_id' => 'nullable|exists:anggaran,id',
            'disusutkan' => 'nullable|in:0,1',
        ];
    }

    /**
     * Sesuaikan data hasil validasi dengan jenis barang:
     * - total_harga selalu jumlah × harga_satuan
     * - Perlengkapan: kondisi default "Baik", tidak pernah disusutkan
     * - Peralatan: kondisi wajib; menyalakan penyusutan per aset butuh masa di Master Barang
     */
    private function normalisasi(array $data): array
    {
        $barang = MasterBarang::findOrFail($data['barang_id']);

        $data['total_harga'] = round($data['jumlah'] * (float) $data['harga_satuan'], 2);
        $data['anggaran_id'] = $data['anggaran_id'] ?? null;

        $disusutkan = $data['disusutkan'] ?? null;
        $data['disusutkan'] = ($disusutkan === null || $disusutkan === '') ? null : (bool) (int) $disusutkan;

        if ($barang->isPerlengkapan()) {
            $data['disusutkan'] = null;
            if (empty($data['status_id'])) {
                $data['status_id'] = MasterStatus::where('kode_status', 'BAIK')->value('id') ?? MasterStatus::orderBy('id')->value('id');
            }
        } else {
            if (empty($data['status_id'])) {
                throw ValidationException::withMessages(['status_id' => 'Kondisi wajib dipilih.']);
            }
            if ($data['disusutkan'] === true && ($barang->masa_pemakaian_bulan ?? 0) <= 12) {
                throw ValidationException::withMessages([
                    'disusutkan' => 'Barang ini belum memiliki masa pemakaian (lebih dari 1 tahun) di Master Barang, sehingga belum bisa disusutkan. Lengkapi dulu di Master Barang.',
                ]);
            }
        }

        return $data;
    }

    /** Peringatan (bukan blokir) terkait anggaran: tahun tidak sama atau melebihi pagu. */
    private function peringatanAnggaran(PengadaanBarang $pengadaan): ?string
    {
        if (!$pengadaan->anggaran_id) {
            return null;
        }

        $anggaran = Anggaran::with('lokasi')->withRealisasi()->find($pengadaan->anggaran_id);
        if (!$anggaran) {
            return null;
        }

        $pesan = [];
        if ((int) $anggaran->tahun !== (int) Carbon::parse($pengadaan->tanggal_pengadaan)->year) {
            $pesan[] = 'Tahun anggaran (' . $anggaran->tahun . ') berbeda dengan tahun pengadaan.';
        }
        if ($anggaran->realisasi > (float) $anggaran->pagu) {
            $pesan[] = 'Realisasi ' . $anggaran->label . ' (' . Format::rupiah($anggaran->realisasi)
                . ') melebihi pagu (' . Format::rupiah($anggaran->pagu) . ').';
        }

        return $pesan ? implode(' ', $pesan) : null;
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->normalisasi($request->validate($this->rules()));

        DB::beginTransaction();
        try {
            $barangData = MasterBarang::with('kategori')->findOrFail($validatedData['barang_id']);
            $lokasiData = MasterSubLokasi::findOrFail($validatedData['lokasi_id']);

            $kodeInventaris = KodeInventarisGenerator::buat($barangData, $lokasiData, $validatedData['tanggal_pengadaan']);

            $validatedData['kode_inventaris'] = $kodeInventaris;
            $validatedData['is_active'] = true;
            $validatedData['is_borrowed'] = false;
            $validatedData['created_by'] = Auth::id();

            $pengadaan = PengadaanBarang::create($validatedData);

            DB::commit();

            $redirect = redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil dibuat dengan kode: ' . $kodeInventaris);

            if ($peringatan = $this->peringatanAnggaran($pengadaan)) {
                $redirect->with('warning', $peringatan);
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data: ' . $e->getMessage());
        }
    }

    public function show(PengadaanBarang $pengadaanBarang): View
    {
        $pengadaanBarang->load(['barang.kategori', 'lokasi.lokasi', 'satuan', 'statusKondisi', 'anggaran.lokasi'])
            ->loadSum('pemakaian', 'jumlah');

        return view('pengadaan-barang.show', compact('pengadaanBarang'));
    }

    public function edit(PengadaanBarang $pengadaanBarang): View
    {
        return view('pengadaan-barang.edit', array_merge(['pengadaanBarang' => $pengadaanBarang], $this->formData()));
    }

    public function update(Request $request, PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        // Cek apakah barang sedang dipinjam
        if ($pengadaanBarang->is_borrowed) {
            return redirect()->back()
                ->with('error', 'Barang yang sedang dipinjam tidak dapat diubah.');
        }

        $rules = $this->rules();
        // kode_inventaris tidak ada di form; bila dikirim harus tetap unik.
        $rules['kode_inventaris'] = 'sometimes|required|string|max:255|unique:pengadaan_barang,kode_inventaris,' . $pengadaanBarang->id;
        $rules['is_active'] = 'nullable|boolean';

        $validatedData = $this->normalisasi($request->validate($rules));

        // Jenis barang aset tidak boleh berubah lewat penggantian barang.
        $barangBaru = MasterBarang::findOrFail($validatedData['barang_id']);
        $barangLama = $pengadaanBarang->barang;
        if ($barangLama && $barangLama->jenis_barang !== $barangBaru->jenis_barang) {
            return redirect()->back()->withInput($request->all())
                ->with('error', 'Barang pengganti harus berjenis sama (' . ucfirst($barangLama->jenis_barang) . ').');
        }

        // Perlengkapan yang sudah dipakai: batasi perubahan agar stok tetap konsisten.
        if ($barangBaru->isPerlengkapan() && $pengadaanBarang->pemakaian()->exists()) {
            $terpakai = (int) $pengadaanBarang->pemakaian()->sum('jumlah');
            $pakaiPertama = $pengadaanBarang->pemakaian()->min('tanggal_pemakaian');

            if ((int) $validatedData['barang_id'] !== (int) $pengadaanBarang->barang_id) {
                return redirect()->back()->withInput($request->all())
                    ->with('error', 'Barang tidak dapat diganti karena sudah ada pemakaian dari pengadaan ini.');
            }
            if ((int) $validatedData['jumlah'] < $terpakai) {
                return redirect()->back()->withInput($request->all())
                    ->with('error', "Jumlah tidak boleh kurang dari yang sudah dipakai ({$terpakai}).");
            }
            if ($pakaiPertama && Carbon::parse($validatedData['tanggal_pengadaan'])->gt(Carbon::parse($pakaiPertama))) {
                return redirect()->back()->withInput($request->all())
                    ->with('error', 'Tanggal pengadaan tidak boleh setelah tanggal pemakaian pertama (' . Carbon::parse($pakaiPertama)->format('d/m/Y') . ').');
            }
        }

        $validatedData['updated_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $pengadaanBarang->update($validatedData);

            DB::commit();

            $redirect = redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil diperbarui');

            if ($peringatan = $this->peringatanAnggaran($pengadaanBarang->fresh())) {
                $redirect->with('warning', $peringatan);
            }

            return $redirect;
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }

    public function destroy(PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        // Cek apakah barang sedang dipinjam
        if ($pengadaanBarang->is_borrowed) {
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Barang yang sedang dipinjam tidak dapat dihapus.');
        }

        // Cek apakah ada mutasi terkait
        if ($pengadaanBarang->mutasiAset()->exists()) {
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Data pengadaan barang ini memiliki riwayat mutasi dan tidak dapat dihapus.');
        }

        // Perlengkapan yang sudah pernah dipakai (termasuk yang dibatalkan) menyimpan riwayat.
        if ($pengadaanBarang->pemakaian()->withTrashed()->exists()) {
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Data pengadaan ini memiliki riwayat pemakaian perlengkapan dan tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $pengadaanBarang->delete();

            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pengadaan-barang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    public function downloadQrCode(PengadaanBarang $pengadaanBarang)
    {
        $items = collect([$pengadaanBarang->load('barang')]);

        return $this->buildQrCodePdf($items)->stream('qr-code-' . $pengadaanBarang->kode_inventaris . '.pdf');
    }

    public function downloadQrCodeBulk(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:pengadaan_barang,id',
        ]);

        $items = PengadaanBarang::with('barang')->whereIn('id', $request->ids)->get();

        return $this->buildQrCodePdf($items)->stream('qr-code-pengadaan-barang-' . now()->format('Ymd-His') . '.pdf');
    }

    private function buildQrCodePdf($items)
    {
        $logoPath = public_path('images/logo.png');
        $logoDataUri = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        foreach ($items as $item) {
            $item->qr_data_uri = Builder::create()
                ->writer(new PngWriter())
                ->data($item->kode_inventaris)
                ->size(300)
                ->margin(10)
                ->build()
                ->getDataUri();
        }

        return Pdf::loadView('pengadaan-barang.qr-code-pdf', compact('items', 'logoDataUri'))->setPaper('a4');
    }

    // ==================== IMPORT ====================

    /**
     * Halaman import: pilih lokasi & sub lokasi untuk download template,
     * lalu upload template yang sudah diisi untuk dipratinjau.
     */
    public function importForm(): View
    {
        $lokasiList = MasterLokasi::orderBy('nama_lokasi')
            ->pluck('nama_lokasi', 'id')
            ->toArray();

        $anggaranList = Anggaran::aktif()->with('lokasi')->orderByDesc('tahun')->get()
            ->mapWithKeys(fn ($a) => [$a->id => $a->tahun . ' – ' . ($a->lokasi->nama_lokasi ?? '-')])
            ->toArray();

        return view('pengadaan-barang.import', compact('lokasiList', 'anggaranList'));
    }

    /**
     * Endpoint untuk depdrop-select2: ambil sub lokasi berdasarkan lokasi_id.
     */
    public function getSubLokasi(Request $request): JsonResponse
    {
        $lokasiId = $request->input('q', '');

        $subLokasi = MasterSubLokasi::where('lokasi_id', $lokasiId)
            ->orderBy('nama_sub_lokasi')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'text' => $item->kode_sub_lokasi . ' - ' . $item->nama_sub_lokasi,
            ]);

        return response()->json($subLokasi);
    }

    /**
     * Download template Excel (multi-sheet) yang sudah di-scope ke sub lokasi terpilih.
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'sub_lokasi_id' => 'required|exists:master_sub_lokasi,id',
        ]);

        $subLokasi = MasterSubLokasi::with('lokasi')->findOrFail($request->sub_lokasi_id);

        $namaFile = 'template-pengadaan-' . Str::slug($subLokasi->kode_sub_lokasi) . '-' . date('Ymd') . '.xlsx';

        return Excel::download(new TemplatePengadaanExport($subLokasi), $namaFile);
    }

    /**
     * Upload template terisi -> simpan sementara -> validasi setiap baris ->
     * tampilkan pratinjau. Belum menyimpan apa pun ke database.
     */
    public function previewImport(Request $request): View
    {
        $request->validate([
            'sub_lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'file' => 'required|file|mimes:xlsx,xls',
            'anggaran_id' => 'nullable|exists:anggaran,id',
        ]);

        $subLokasi = MasterSubLokasi::with('lokasi')->findOrFail($request->sub_lokasi_id);
        $anggaran = $request->filled('anggaran_id') ? Anggaran::with('lokasi')->find($request->anggaran_id) : null;

        // Simpan file sementara agar bisa dibaca lagi saat konfirmasi simpan.
        $token = (string) Str::uuid();
        $request->file('file')->storeAs('imports', $token . '.xlsx');

        $rows = $this->parseImportFile($token);
        $results = $this->validateImportRows($rows);

        $validCount = collect($results)->where('valid', true)->count();
        $errorCount = collect($results)->where('valid', false)->count();
        $newBarangCount = collect($results)->where('valid', true)->where('barang_mode', 'new')->count();

        return view('pengadaan-barang.import-preview', [
            'subLokasi' => $subLokasi,
            'anggaran' => $anggaran,
            'results' => $results,
            'token' => $token,
            'validCount' => $validCount,
            'errorCount' => $errorCount,
            'newBarangCount' => $newBarangCount,
        ]);
    }

    /**
     * Konfirmasi simpan: baca ulang file sementara, validasi ulang, simpan baris valid.
     */
    public function storeImport(Request $request): RedirectResponse
    {
        $request->validate([
            'sub_lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'token' => 'required|string',
            'anggaran_id' => 'nullable|exists:anggaran,id',
        ]);

        $path = 'imports/' . basename($request->token) . '.xlsx';
        if (!Storage::exists($path)) {
            return redirect()->route('pengadaan-barang.import')
                ->with('error', 'File import tidak ditemukan atau sudah kedaluwarsa. Silakan upload ulang.');
        }

        $subLokasi = MasterSubLokasi::findOrFail($request->sub_lokasi_id);
        $anggaranId = $request->filled('anggaran_id') ? (int) $request->anggaran_id : null;

        $rows = $this->parseImportFile(basename($request->token));
        $results = $this->validateImportRows($rows);
        $validRows = collect($results)->where('valid', true)->values();

        if ($validRows->isEmpty()) {
            return redirect()->route('pengadaan-barang.import')
                ->with('error', 'Tidak ada baris valid untuk diimport.');
        }

        DB::beginTransaction();
        try {
            $barangCache = []; // kode_barang => MasterBarang (untuk barang baru dalam batch ini)
            $counterCache = []; // "MM-YYYY" => nomor urut terakhir
            $jumlahSimpan = 0;

            foreach ($validRows as $row) {
                // Resolusi / pembuatan barang
                if ($row['barang_mode'] === 'new') {
                    $kode = $row['kode_barang'];
                    if (isset($barangCache[$kode])) {
                        $barang = $barangCache[$kode];
                    } else {
                        $barang = MasterBarang::create([
                            'kode_barang' => $kode,
                            'nama_barang' => $row['_new_barang']['nama_barang'],
                            'merk_barang' => $row['_new_barang']['merk_barang'],
                            'tipe_barang' => $row['_new_barang']['tipe_barang'],
                            'tahun_barang' => $row['_new_barang']['tahun_barang'],
                            'kategori_barang_id' => $row['_new_barang']['kategori_barang_id'],
                            'jenis_barang' => $row['_new_barang']['jenis_barang'],
                            'disusutkan' => $row['_new_barang']['disusutkan'],
                            'masa_pemakaian_bulan' => $row['_new_barang']['masa_pemakaian_bulan'],
                            'interval_penyusutan_tahun' => $row['_new_barang']['interval_penyusutan_tahun'],
                            'butuh_perawatan' => $row['_new_barang']['butuh_perawatan'],
                            'status_permohonan' => 'approved',
                            'is_active' => true,
                            'created_by' => Auth::id(),
                        ]);
                        $barang->load('kategori');
                        $barangCache[$kode] = $barang;
                    }
                } else {
                    $barang = $row['_barang'];
                }

                // Generate kode inventaris (urut berjalan per bulan/tahun di dalam batch)
                $tanggal = $row['_tanggal'];
                $key = date('m-Y', strtotime($tanggal));
                $kodeInventaris = KodeInventarisGenerator::buat($barang, $subLokasi, $tanggal, $counterCache[$key]);

                PengadaanBarang::create([
                    'kode_inventaris' => $kodeInventaris,
                    'barang_id' => $barang->id,
                    'lokasi_id' => $subLokasi->id,
                    'sumber' => $row['sumber'] ?? '',
                    'status' => $row['status'],
                    'status_id' => $row['_status_id'],
                    'tanggal_pengadaan' => $tanggal,
                    'jumlah' => $row['jumlah'],
                    'satuan_id' => $row['_satuan_id'],
                    'harga_satuan' => $row['harga_satuan'],
                    'total_harga' => $row['total_harga'],
                    'keterangan' => $row['keterangan'] ?: null,
                    'anggaran_id' => $anggaranId,
                    'is_active' => true,
                    'is_borrowed' => false,
                    'created_by' => Auth::id(),
                ]);

                $jumlahSimpan++;
            }

            DB::commit();
            Storage::delete($path);

            return redirect()->route('pengadaan-barang.index')
                ->with('success', $jumlahSimpan . ' data pengadaan barang berhasil diimport.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('pengadaan-barang.import')
                ->with('error', 'Terjadi kesalahan saat menyimpan import: ' . $e->getMessage());
        }
    }

    /**
     * Baca sheet "Data Pengadaan" (sheet pertama) menjadi koleksi baris ber-key heading.
     */
    private function parseImportFile(string $token)
    {
        $path = Storage::path('imports/' . $token . '.xlsx');
        $sheets = Excel::toCollection(new PengadaanBarangImport, $path);

        return $sheets->first() ?? collect();
    }

    /**
     * Validasi seluruh baris. Mengembalikan array hasil per-baris (termasuk
     * data resolusi yang dipakai saat simpan).
     */
    private function validateImportRows($rows): array
    {
        // Preload lookup maps
        $barangMap = MasterBarang::all()->keyBy(fn ($b) => $this->norm($b->kode_barang));
        $kategoriMap = KategoriBarang::all()->keyBy(fn ($k) => $this->norm($k->kode_kategori_barang));
        $satuanMap = MasterSatuan::all()->keyBy(fn ($s) => $this->norm($s->kode_satuan));
        $kondisiMap = MasterStatus::all()->keyBy(fn ($s) => $this->norm($s->kode_status));

        $results = [];
        $rowNumber = 1; // baris 1 = heading

        foreach ($rows as $raw) {
            $rowNumber++;
            $row = collect($raw)->map(fn ($v) => is_string($v) ? trim($v) : $v);

            $kodeBarang = (string) $row->get('kode_barang', '');
            $namaBarang = (string) $row->get('nama_barang', '');
            $kodeKategori = (string) $row->get('kode_kategori_barang', '');
            $merk = (string) $row->get('merk_barang', '');
            $tipe = (string) $row->get('tipe_barang', '');
            $tahun = (string) $row->get('tahun_barang', '');
            $sumber = (string) $row->get('sumber', '');
            $status = strtolower((string) $row->get('status', ''));
            $kodeKondisi = (string) $row->get('kode_kondisi', '');
            $tanggalRaw = $row->get('tanggal_pengadaan', '');
            $jumlahRaw = $row->get('jumlah', '');
            $kodeSatuan = (string) $row->get('kode_satuan', '');
            $hargaRaw = $row->get('harga_satuan', '');
            $keterangan = (string) $row->get('keterangan', '');
            $jenisBarang = strtolower((string) $row->get('jenis_barang', ''));
            $disusutkanBaru = in_array(strtolower((string) $row->get('disusutkan', '')), ['ya', 'y', 'yes', '1', 'true'], true);
            $masaRaw = $row->get('masa_pemakaian', '');
            $intervalRaw = $row->get('interval_penyusutan', '');
            $butuhPerawatan = in_array(strtolower((string) $row->get('butuh_perawatan', '')), ['ya', 'y', 'yes', '1', 'true'], true);
            $pengaturanBaru = null;

            // Lewati baris yang benar-benar kosong
            $isiPenting = $kodeBarang . $namaBarang . $kodeKondisi . (string) $tanggalRaw . (string) $jumlahRaw . $kodeSatuan . (string) $hargaRaw;
            if (trim($isiPenting) === '') {
                continue;
            }

            $errors = [];
            $barangMode = null;
            $barang = null;
            $newBarang = null;
            $kategori = null;

            // --- Barang ---
            if ($kodeBarang === '') {
                $errors[] = 'kode_barang wajib diisi.';
            } else {
                $existing = $barangMap->get($this->norm($kodeBarang));
                if ($existing) {
                    if ($existing->status_permohonan === 'approved' && $existing->is_active) {
                        $barangMode = 'existing';
                        $barang = $existing->loadMissing('kategori');
                        $namaBarang = $existing->nama_barang;
                    } else {
                        $errors[] = "Barang '{$kodeBarang}' sudah ada tetapi belum approved/aktif.";
                    }
                } else {
                    $barangMode = 'new';
                    if ($namaBarang === '') {
                        $errors[] = 'nama_barang wajib diisi untuk barang baru.';
                    }
                    $kategori = $kodeKategori !== '' ? $kategoriMap->get($this->norm($kodeKategori)) : null;
                    if ($kodeKategori === '') {
                        $errors[] = 'kode_kategori_barang wajib diisi untuk barang baru.';
                    } elseif (!$kategori) {
                        $errors[] = "Kode kategori '{$kodeKategori}' tidak ditemukan.";
                    }

                    // Jenis & pengaturan penyusutan untuk barang baru
                    if (!in_array($jenisBarang, ['peralatan', 'perlengkapan'], true)) {
                        $errors[] = 'jenis_barang wajib diisi untuk barang baru: peralatan atau perlengkapan.';
                    } else {
                        $pengaturan = NilaiBarangService::validasiPengaturan($jenisBarang, $disusutkanBaru, $masaRaw, $intervalRaw);
                        foreach ($pengaturan['errors'] as $pesan) {
                            $errors[] = $pesan;
                        }
                        $pengaturanBaru = $pengaturan['data'];
                    }
                }
            }

            // --- Satuan ---
            $satuan = $kodeSatuan !== '' ? $satuanMap->get($this->norm($kodeSatuan)) : null;
            if ($kodeSatuan === '') {
                $errors[] = 'kode_satuan wajib diisi.';
            } elseif (!$satuan) {
                $errors[] = "Kode satuan '{$kodeSatuan}' tidak ditemukan.";
            }

            // --- Kondisi ---
            $kondisi = $kodeKondisi !== '' ? $kondisiMap->get($this->norm($kodeKondisi)) : null;
            if ($kodeKondisi === '') {
                $errors[] = 'kode_kondisi wajib diisi.';
            } elseif (!$kondisi) {
                $errors[] = "Kode kondisi '{$kodeKondisi}' tidak ditemukan.";
            }

            // --- Status (enum) ---
            if (!in_array($status, ['baru', 'bekas', 'hibah'], true)) {
                $errors[] = 'status harus salah satu dari: baru, bekas, hibah.';
            }

            // --- Tanggal ---
            $tanggal = $this->parseTanggal($tanggalRaw);
            if (!$tanggal) {
                $errors[] = 'tanggal_pengadaan tidak valid (format YYYY-MM-DD).';
            } elseif ($tanggal->isFuture() && !$tanggal->isToday()) {
                $errors[] = 'tanggal_pengadaan tidak boleh melebihi hari ini.';
            }

            // --- Jumlah ---
            $jumlah = is_numeric($jumlahRaw) ? (int) $jumlahRaw : 0;
            if (!is_numeric($jumlahRaw) || $jumlah < 1) {
                $errors[] = 'jumlah harus berupa angka minimal 1.';
            }

            // --- Harga ---
            $harga = is_numeric($hargaRaw) ? (float) $hargaRaw : -1;
            if (!is_numeric($hargaRaw) || $harga < 0) {
                $errors[] = 'harga_satuan harus berupa angka minimal 0.';
            }

            $total = ($jumlah > 0 && $harga >= 0) ? $jumlah * $harga : 0;

            $results[] = [
                'row_number' => $rowNumber,
                'valid' => empty($errors),
                'errors' => $errors,
                'barang_mode' => $barangMode,
                'kode_barang' => $kodeBarang,
                'nama_barang' => $namaBarang,
                'kategori' => $kategori->nama_kategori_barang ?? ($barang->kategori->nama_kategori_barang ?? '-'),
                'jenis_barang' => $barangMode === 'new' ? $jenisBarang : ($barang->jenis_barang ?? null),
                'satuan' => $satuan->nama_satuan ?? '-',
                'kondisi' => $kondisi->nama_status ?? '-',
                'status' => $status,
                'sumber' => $sumber,
                'tanggal_pengadaan' => $tanggal ? $tanggal->format('Y-m-d') : (string) $tanggalRaw,
                'jumlah' => $jumlah,
                'harga_satuan' => $harga >= 0 ? $harga : 0,
                'total_harga' => $total,
                'keterangan' => $keterangan,
                // payload resolusi untuk simpan
                '_barang' => $barang,
                '_new_barang' => $barangMode === 'new' ? [
                    'nama_barang' => $namaBarang,
                    'merk_barang' => $merk ?: null,
                    'tipe_barang' => $tipe ?: null,
                    'tahun_barang' => $tahun ?: null,
                    'kategori_barang_id' => $kategori->id ?? null,
                    'jenis_barang' => $jenisBarang,
                    'disusutkan' => $pengaturanBaru['disusutkan'] ?? false,
                    'masa_pemakaian_bulan' => $pengaturanBaru['masa_pemakaian_bulan'] ?? null,
                    'interval_penyusutan_tahun' => $pengaturanBaru['interval_penyusutan_tahun'] ?? 1,
                    'butuh_perawatan' => $butuhPerawatan,
                ] : null,
                '_satuan_id' => $satuan->id ?? null,
                '_status_id' => $kondisi->id ?? null,
                '_tanggal' => $tanggal ? $tanggal->format('Y-m-d') : null,
            ];
        }

        return $results;
    }

    private function norm($value): string
    {
        return strtoupper(trim((string) $value));
    }

    private function parseTanggal($value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Excel menyimpan tanggal sebagai serial number
            if (is_numeric($value)) {
                $dt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $value);
                return Carbon::instance($dt)->startOfDay();
            }

            return Carbon::parse($value)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }
}