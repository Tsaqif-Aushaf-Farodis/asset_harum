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
        $query = PengadaanBarang::with(['barang', 'lokasi', 'kategori', 'satuan', 'statusKondisi', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan lokasi
        if ($request->filled('lokasi_id')) {
            $query->where('lokasi_id', $request->lokasi_id);
        }

        // Filter berdasarkan kategori (via relasi barang)
        if ($request->filled('kategori_id')) {
            $query->whereHas('barang', function($q) use ($request) {
                $q->where('kategori_id', $request->kategori_id);
            });
        }

        // Filter berdasarkan status kondisi
        if ($request->filled('status_id')) {
            $query->where('status_id', $request->status_id);
        }

        // Filter berdasarkan status aktif
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Filter berdasarkan tahun perolehan
        if ($request->filled('tahun')) {
            $query->whereYear('tanggal_pengadaan', $request->tahun);
        }

        // Search global
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_inventaris', 'like', "%$search%")
                  ->orWhere('keterangan', 'like', "%$search%")
                  ->orWhereHas('barang', function($sq) use ($search) {
                      $sq->where('nama_barang', 'like', "%$search%");
                  });
            });
        }
        
        $pengadaanBarang = $query->paginate(20);

        // Statistik untuk dashboard
        $statistics = [
            'total_aset' => PengadaanBarang::count(),
            'total_aktif' => PengadaanBarang::where('is_active', true)->count(),
            'total_dipinjam' => PengadaanBarang::where('is_borrowed', true)->count(),
            'total_nilai' => PengadaanBarang::sum('total_harga'),
        ];

        // Data untuk filter dropdown
        $lokasiList = MasterSubLokasi::with('lokasi')->get();
        $kategoriList = \App\Models\KategoriBarang::all();
        $statusList = MasterStatus::all();
        $tahunList = PengadaanBarang::selectRaw('YEAR(tanggal_pengadaan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun');

        if ($request->header('HX-Request')) {
            return view('pengadaan-barang.includes.index-table', compact('pengadaanBarang'));
        }

        // Daftar barang untuk modal pilih-barang di fitur download QR Code (multiple)
        $allPengadaanBarangForQr = PengadaanBarang::with('barang')
            ->orderBy('kode_inventaris')
            ->get(['id', 'barang_id', 'kode_inventaris']);

        return view('pengadaan-barang.index', compact(
            'pengadaanBarang',
            'statistics',
            'lokasiList',
            'kategoriList',
            'statusList',
            'tahunList',
            'allPengadaanBarangForQr'
        ));
    }

    public function create(): View
    {
        $pengadaanBarang = new PengadaanBarang();
        $barangList = MasterBarang::approved()
            ->pluck('nama_barang', 'id')
            ->toArray();
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();


        return view('pengadaan-barang.create', compact('pengadaanBarang', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'barang_id' => 'required|exists:master_barang,id',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'required|exists:master_status,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1',
            'satuan_id' => 'required|exists:master_satuan,id',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'permohonan_id' => 'nullable|exists:permohonan,id',
        ]);

        DB::beginTransaction();
        try {
            $barangData = MasterBarang::with('kategori')->findOrFail($validatedData['barang_id']);
            $lokasiData = MasterSubLokasi::findOrFail($validatedData['lokasi_id']);
            
            $tanggalPengadaan = $validatedData['tanggal_pengadaan'];
            $month = date('m', strtotime($tanggalPengadaan));
            $year = date('Y', strtotime($tanggalPengadaan));
            
            // Generate nomor urut
            $nomorUrut = PengadaanBarang::whereMonth('tanggal_pengadaan', $month)
                ->whereYear('tanggal_pengadaan', $year)
                ->count() + 1;

            // Format: KODE_BARANG-KODE_KATEGORI-KODE_LOKASI-MM-YYYY-URUT
            $kodeInventaris = sprintf(
                '%s-%s-%s-%s-%s-%04d',
                $barangData->kode_barang,
                $barangData->kategori->kode_kategori_barang,
                $lokasiData->kode_sub_lokasi,
                $month,
                $year,
                $nomorUrut
            );

            $validatedData['kode_inventaris'] = $kodeInventaris;
            $validatedData['is_active'] = true;
            $validatedData['is_borrowed'] = false;
            $validatedData['created_by'] = Auth::id();

            $pengadaan = PengadaanBarang::create($validatedData);
            
            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil dibuat dengan kode: ' . $kodeInventaris);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput($request->all())
                ->with('error', 'Terjadi kesalahan saat membuat data: ' . $e->getMessage());
        }
    }

    public function show(PengadaanBarang $pengadaanBarang): View
    {
        return view('pengadaan-barang.show', compact('pengadaanBarang'));
    }

    public function edit(PengadaanBarang $pengadaanBarang): View
    {
        $barangList = MasterBarang::approved()
            ->pluck('nama_barang', 'id')
            ->toArray();
        $lokasiList = MasterSubLokasi::with('lokasi')->get()->mapWithKeys(function ($item) {
            $label = $item->lokasi->nama_lokasi . ' - ' . $item->nama_sub_lokasi;
            return [$item->id => $label];
        })->toArray();
        $statusList = MasterStatus::pluck('nama_status', 'id')->toArray();
        $satuanList = MasterSatuan::pluck('nama_satuan', 'id')->toArray();

        return view('pengadaan-barang.edit', compact('pengadaanBarang', 'barangList', 'lokasiList', 'statusList', 'satuanList'));
    }

    public function update(Request $request, PengadaanBarang $pengadaanBarang): RedirectResponse
    {
        // Cek apakah barang sedang dipinjam
        if ($pengadaanBarang->is_borrowed) {
            return redirect()->back()
                ->with('error', 'Barang yang sedang dipinjam tidak dapat diubah.');
        }

        $validatedData = $request->validate([
            'kode_inventaris' => 'required|string|max:255|unique:pengadaan_barang,kode_inventaris,' . $pengadaanBarang->id,
            'barang_id' => 'required|exists:master_barang,id',
            'lokasi_id' => 'required|exists:master_sub_lokasi,id',
            'sumber' => 'required|string|max:100',
            'status' => 'required|in:baru,bekas,hibah',
            'status_id' => 'required|exists:master_status,id',
            'tanggal_pengadaan' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|integer|min:1',
            'satuan_id' => 'required|exists:master_satuan,id',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validatedData['updated_by'] = Auth::id();

        DB::beginTransaction();
        try {
            $pengadaanBarang->update($validatedData);
            
            DB::commit();
            return redirect()->route('pengadaan-barang.index')
                ->with('success', 'Pengadaan Barang berhasil diperbarui');
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
        $logoPath = public_path('images/logo_da_old.png');
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

        return view('pengadaan-barang.import', compact('lokasiList'));
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
        ]);

        $subLokasi = MasterSubLokasi::with('lokasi')->findOrFail($request->sub_lokasi_id);

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
        ]);

        $path = 'imports/' . basename($request->token) . '.xlsx';
        if (!Storage::exists($path)) {
            return redirect()->route('pengadaan-barang.import')
                ->with('error', 'File import tidak ditemukan atau sudah kedaluwarsa. Silakan upload ulang.');
        }

        $subLokasi = MasterSubLokasi::findOrFail($request->sub_lokasi_id);

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
                $month = date('m', strtotime($tanggal));
                $year = date('Y', strtotime($tanggal));
                $key = $month . '-' . $year;
                if (!isset($counterCache[$key])) {
                    $counterCache[$key] = PengadaanBarang::whereMonth('tanggal_pengadaan', $month)
                        ->whereYear('tanggal_pengadaan', $year)
                        ->count();
                }
                $counterCache[$key]++;

                $kodeInventaris = sprintf(
                    '%s-%s-%s-%s-%s-%04d',
                    $barang->kode_barang,
                    $barang->kategori->kode_kategori_barang,
                    $subLokasi->kode_sub_lokasi,
                    $month,
                    $year,
                    $counterCache[$key]
                );

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