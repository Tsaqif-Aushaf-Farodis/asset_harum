<?php

use App\Models\Anggaran;
use App\Models\KategoriBarang;
use App\Models\MasterBarang;
use App\Models\MasterLokasi;
use App\Models\MasterSatuan;
use App\Models\MasterStatus;
use App\Models\MasterSubLokasi;
use App\Models\PemakaianPerlengkapan;
use App\Models\PengadaanBarang;
use App\Models\User;
use App\Services\PerlengkapanService;
use Carbon\Carbon;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

uses(RefreshDatabase::class);

/** Data dasar + user dengan semua permission. */
function siapkan(): object
{
    test()->seed(PermissionSeeder::class);

    $user = User::forceCreate([
        'name' => 'Admin Uji',
        'username' => 'adminuji',
        'email' => 'admin@uji.test',
        'password' => bcrypt('password'),
    ]);
    $user->givePermissionTo(Permission::all());

    $kategori = KategoriBarang::create([
        'kode_kategori_barang' => 'ELK', 'nama_kategori_barang' => 'Elektronik', 'status_kategori_barang' => 'aktif',
    ]);
    $lokasi = MasterLokasi::create(['kode_lokasi' => 'MTSPA', 'nama_lokasi' => 'MTs Putra']);
    $sub = MasterSubLokasi::create(['lokasi_id' => $lokasi->id, 'kode_sub_lokasi' => 'KLS7A', 'nama_sub_lokasi' => 'Kelas 7A']);
    $status = MasterStatus::create(['kode_status' => 'BAIK', 'nama_status' => 'Baik']);
    $satuan = MasterSatuan::create(['kode_satuan' => 'PCS', 'nama_satuan' => 'Pcs', 'is_active' => true]);

    return (object) compact('user', 'kategori', 'lokasi', 'sub', 'status', 'satuan');
}

function barangUji(object $d, array $attr = []): MasterBarang
{
    return MasterBarang::create(array_merge([
        'kode_barang' => 'BRG' . MasterBarang::count(),
        'nama_barang' => 'Barang ' . MasterBarang::count(),
        'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'peralatan',
        'status_permohonan' => 'approved',
        'is_active' => true,
    ], $attr));
}

function pengadaanUji(object $d, MasterBarang $barang, array $attr = []): PengadaanBarang
{
    $jumlah = $attr['jumlah'] ?? 10;
    $harga = $attr['harga_satuan'] ?? 1000;
    static $n = 0;
    $n++;

    return PengadaanBarang::create(array_merge([
        'kode_inventaris' => 'KODE-' . $n,
        'barang_id' => $barang->id,
        'lokasi_id' => $d->sub->id,
        'sumber' => 'APBS',
        'status' => 'baru',
        'status_id' => $d->status->id,
        'tanggal_pengadaan' => '2026-01-10',
        'jumlah' => $jumlah,
        'satuan_id' => $d->satuan->id,
        'harga_satuan' => $harga,
        'total_harga' => $jumlah * $harga,
        'is_active' => true,
        'is_borrowed' => false,
        'created_by' => $d->user->id,
    ], $attr));
}

// ---------------------------------------------------------------- stok & pemakaian

it('mengalokasikan pemakaian FIFO lintas batch dan menyimpan snapshot harga', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    $lama = pengadaanUji($d, $barang, ['jumlah' => 10, 'harga_satuan' => 1000, 'tanggal_pengadaan' => '2026-01-01']);
    $baru = pengadaanUji($d, $barang, ['jumlah' => 10, 'harga_satuan' => 2000, 'tanggal_pengadaan' => '2026-02-01']);

    $hasil = app(PerlengkapanService::class)->catat($barang->id, 15, '2026-03-01', $d->sub->id, 'Bu Ani', null, $d->user->id);

    expect($hasil)->toHaveCount(2)
        ->and($hasil[0]->pengadaan_id)->toBe($lama->id)
        ->and($hasil[0]->jumlah)->toBe(10)
        ->and((float) $hasil[0]->harga_satuan)->toBe(1000.0)
        ->and($hasil[1]->pengadaan_id)->toBe($baru->id)
        ->and($hasil[1]->jumlah)->toBe(5)
        ->and((float) $hasil[1]->harga_satuan)->toBe(2000.0);

    expect($lama->fresh()->stok_tersedia)->toBe(0)
        ->and($baru->fresh()->stok_tersedia)->toBe(5)
        // sisa 5 × 2000 = 10.000; yang sudah dipakai bernilai 0
        ->and($baru->fresh()->nilaiSaatIni())->toBe(10000.0)
        ->and($lama->fresh()->nilaiSaatIni())->toBe(0.0);
});

it('menolak pemakaian melebihi stok tanpa mengubah data', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    pengadaanUji($d, $barang, ['jumlah' => 5]);

    expect(fn () => app(PerlengkapanService::class)->catat($barang->id, 6, '2026-03-01', $d->sub->id, 'Pak Budi', null, $d->user->id))
        ->toThrow(ValidationException::class);

    expect(PemakaianPerlengkapan::count())->toBe(0);
});

it('tidak mengambil stok dari pengadaan yang tanggalnya setelah tanggal pemakaian', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    pengadaanUji($d, $barang, ['jumlah' => 5, 'tanggal_pengadaan' => '2026-05-01']);

    expect(fn () => app(PerlengkapanService::class)->catat($barang->id, 1, '2026-04-01', $d->sub->id, 'Pak Budi', null, $d->user->id))
        ->toThrow(ValidationException::class);
});

it('menolak pemakaian barang yang bukan perlengkapan', function () {
    $d = siapkan();
    $barang = barangUji($d); // peralatan
    pengadaanUji($d, $barang);

    expect(fn () => app(PerlengkapanService::class)->catat($barang->id, 1, '2026-03-01', $d->sub->id, 'X', null, $d->user->id))
        ->toThrow(ValidationException::class);
});

it('mengembalikan stok saat pemakaian dibatalkan (soft delete) dan riwayat tetap ada', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    $batch = pengadaanUji($d, $barang, ['jumlah' => 10]);

    $pemakaian = app(PerlengkapanService::class)->catat($barang->id, 4, '2026-03-01', $d->sub->id, 'Bu Ani', null, $d->user->id)->first();
    expect($batch->fresh()->stok_tersedia)->toBe(6);

    $this->actingAs($d->user)->delete(route('pemakaian-perlengkapan.destroy', $pemakaian))->assertRedirect();

    expect($batch->fresh()->stok_tersedia)->toBe(10)
        ->and(PemakaianPerlengkapan::withTrashed()->count())->toBe(1);
});

it('mencatat pemakaian lewat form dan mewajibkan lokasi serta nama pemakai', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    pengadaanUji($d, $barang, ['jumlah' => 10]);

    $this->actingAs($d->user)
        ->post(route('pemakaian-perlengkapan.store'), ['barang_id' => $barang->id, 'jumlah' => 2, 'tanggal_pemakaian' => '2026-03-01'])
        ->assertSessionHasErrors(['lokasi_id', 'pemakai']);

    $this->actingAs($d->user)
        ->post(route('pemakaian-perlengkapan.store'), [
            'barang_id' => $barang->id, 'jumlah' => 2, 'tanggal_pemakaian' => '2026-03-01',
            'lokasi_id' => $d->sub->id, 'pemakai' => 'Bu Ani',
        ])->assertRedirect(route('pemakaian-perlengkapan.index'));

    expect(PemakaianPerlengkapan::first())
        ->pemakai->toBe('Bu Ani')
        ->jumlah->toBe(2);
});

// ---------------------------------------------------------------- master barang

it('menyimpan Peralatan disusutkan dengan masa dalam tahun dan interval', function () {
    $d = siapkan();

    $this->actingAs($d->user)->post(route('master-barang.store'), [
        'kode_barang' => 'LPT01', 'nama_barang' => 'Laptop', 'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'peralatan', 'disusutkan' => 1, 'masa_pemakaian' => 4, 'interval_penyusutan_tahun' => 2,
        'butuh_perawatan' => 1,
    ])->assertRedirect(route('master-barang.index'));

    $b = MasterBarang::where('kode_barang', 'LPT01')->first();
    expect($b->masa_pemakaian_bulan)->toBe(48)
        ->and($b->interval_penyusutan_tahun)->toBe(2)
        ->and($b->disusutkan)->toBeTrue()
        ->and($b->butuh_perawatan)->toBeTrue();
});

it('menolak Peralatan disusutkan tanpa masa atau masa bukan kelipatan interval', function () {
    $d = siapkan();
    $dasar = ['kode_barang' => 'X1', 'nama_barang' => 'X', 'kategori_barang_id' => $d->kategori->id, 'jenis_barang' => 'peralatan', 'disusutkan' => 1];

    $this->actingAs($d->user)->post(route('master-barang.store'), $dasar)
        ->assertSessionHasErrors('masa_pemakaian');

    $this->actingAs($d->user)->post(route('master-barang.store'), $dasar + ['masa_pemakaian' => 4, 'interval_penyusutan_tahun' => 3])
        ->assertSessionHasErrors('interval_penyusutan_tahun');

    expect(MasterBarang::count())->toBe(0);
});

it('mengizinkan Peralatan tidak disusutkan tanpa masa (mis. tanah)', function () {
    $d = siapkan();

    $this->actingAs($d->user)->post(route('master-barang.store'), [
        'kode_barang' => 'TNH01', 'nama_barang' => 'Tanah', 'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'peralatan', 'disusutkan' => 0,
    ])->assertRedirect(route('master-barang.index'));

    expect(MasterBarang::first()->disusutkan)->toBeFalse();
});

it('menyimpan Perlengkapan dan mengabaikan pengaturan penyusutan', function () {
    $d = siapkan();

    $this->actingAs($d->user)->post(route('master-barang.store'), [
        'kode_barang' => 'ATK01', 'nama_barang' => 'Spidol', 'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'perlengkapan', 'disusutkan' => 1, 'masa_pemakaian' => 6, 'interval_penyusutan_tahun' => 3,
    ])->assertRedirect(route('master-barang.index'));

    $b = MasterBarang::first();
    expect($b->jenis_barang)->toBe('perlengkapan')
        ->and($b->disusutkan)->toBeFalse()
        ->and($b->masa_pemakaian_bulan)->toBe(6)
        ->and($b->interval_penyusutan_tahun)->toBe(1);

    $this->actingAs($d->user)->post(route('master-barang.store'), [
        'kode_barang' => 'ATK02', 'nama_barang' => 'Kertas', 'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'perlengkapan', 'masa_pemakaian' => 24,
    ])->assertSessionHasErrors('masa_pemakaian');
});

it('tidak mengizinkan jenis barang diubah setelah punya pengadaan', function () {
    $d = siapkan();
    $barang = barangUji($d);
    pengadaanUji($d, $barang);

    $this->actingAs($d->user)->put(route('master-barang.update', $barang), [
        'kode_barang' => $barang->kode_barang, 'nama_barang' => $barang->nama_barang, 'kategori_barang_id' => $d->kategori->id,
        'jenis_barang' => 'perlengkapan',
    ])->assertSessionHasErrors('jenis_barang');

    expect($barang->fresh()->jenis_barang)->toBe('peralatan');
});

// ---------------------------------------------------------------- pengadaan, aset & nilai buku

it('menghitung nilai buku aset per interval dan menghormati penimpa per aset', function () {
    $d = siapkan();
    $barang = barangUji($d, ['disusutkan' => true, 'masa_pemakaian_bulan' => 48, 'interval_penyusutan_tahun' => 2]);
    $aset = pengadaanUji($d, $barang, ['jumlah' => 1, 'harga_satuan' => 12_000_000, 'tanggal_pengadaan' => '2026-03-15']);

    expect($aset->penyusutan(Carbon::parse('2028-03-14'))['nilai_buku'])->toBe(12_000_000.0)
        ->and($aset->penyusutan(Carbon::parse('2028-03-15'))['nilai_buku'])->toBe(6_000_000.0)
        ->and($aset->nilaiSaatIni(Carbon::parse('2030-03-15')))->toBe(0.0);

    // penimpa: aset ini tidak disusutkan walaupun master menyala
    $aset->update(['disusutkan' => false]);
    expect($aset->fresh()->nilaiSaatIni(Carbon::parse('2030-03-15')))->toBe(12_000_000.0)
        ->and($aset->fresh()->disusutkan_diatur_manual)->toBeTrue();

    // NULL = ikut master lagi
    $aset->update(['disusutkan' => null]);
    expect($aset->fresh()->disusutkan_efektif)->toBeTrue();
});

it('membuat pengadaan Perlengkapan tanpa kondisi (default Baik) dan tidak disusutkan', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);

    $this->actingAs($d->user)->post(route('pengadaan-barang.store'), [
        'barang_id' => $barang->id, 'lokasi_id' => $d->sub->id, 'sumber' => 'APBS', 'status' => 'baru',
        'tanggal_pengadaan' => '2026-01-10', 'jumlah' => 20, 'satuan_id' => $d->satuan->id, 'harga_satuan' => 1500,
        'disusutkan' => 1,
    ])->assertRedirect(route('pengadaan-barang.index'));

    $p = PengadaanBarang::first();
    expect($p->status_id)->toBe($d->status->id)
        ->and($p->disusutkan)->toBeNull()
        ->and((float) $p->total_harga)->toBe(30000.0)
        ->and($p->stok_tersedia)->toBe(20);
});

it('mewajibkan kondisi untuk Peralatan dan menolak penyusutan aset bila master belum punya masa', function () {
    $d = siapkan();
    $barang = barangUji($d); // masa kosong
    $dasar = [
        'barang_id' => $barang->id, 'lokasi_id' => $d->sub->id, 'sumber' => 'APBS', 'status' => 'baru',
        'tanggal_pengadaan' => '2026-01-10', 'jumlah' => 1, 'satuan_id' => $d->satuan->id, 'harga_satuan' => 1000,
    ];

    $this->actingAs($d->user)->post(route('pengadaan-barang.store'), $dasar)->assertSessionHasErrors('status_id');

    $this->actingAs($d->user)->post(route('pengadaan-barang.store'), $dasar + ['status_id' => $d->status->id, 'disusutkan' => 1])
        ->assertSessionHasErrors('disusutkan');

    expect(PengadaanBarang::count())->toBe(0);
});

it('menghitung total_harga di server dan membuat kode inventaris yang tidak bentrok setelah ada yang dihapus', function () {
    $d = siapkan();
    $barang = barangUji($d, ['kode_barang' => 'LPT']);
    $dasar = [
        'barang_id' => $barang->id, 'lokasi_id' => $d->sub->id, 'sumber' => 'APBS', 'status' => 'baru', 'status_id' => $d->status->id,
        'tanggal_pengadaan' => '2026-01-10', 'jumlah' => 2, 'satuan_id' => $d->satuan->id, 'harga_satuan' => 500, 'total_harga' => 1,
    ];

    foreach (range(1, 3) as $i) {
        $this->actingAs($d->user)->post(route('pengadaan-barang.store'), $dasar)->assertRedirect();
    }
    // hapus yang tengah, lalu buat lagi → tidak boleh menghasilkan kode yang sudah ada
    PengadaanBarang::orderBy('id')->skip(1)->first()->delete();
    $this->actingAs($d->user)->post(route('pengadaan-barang.store'), $dasar)->assertRedirect();

    $kode = PengadaanBarang::pluck('kode_inventaris');
    expect($kode)->toHaveCount(3)->and($kode->unique())->toHaveCount(3)
        ->and((float) PengadaanBarang::first()->total_harga)->toBe(1000.0);
});

it('menjaga integritas perlengkapan yang sudah dipakai', function () {
    $d = siapkan();
    $barang = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    $batch = pengadaanUji($d, $barang, ['jumlah' => 10]);
    app(PerlengkapanService::class)->catat($barang->id, 6, '2026-03-01', $d->sub->id, 'Bu Ani', null, $d->user->id);

    $payload = [
        'barang_id' => $barang->id, 'lokasi_id' => $d->sub->id, 'sumber' => 'APBS', 'status' => 'baru',
        'tanggal_pengadaan' => '2026-01-10', 'jumlah' => 5, 'satuan_id' => $d->satuan->id, 'harga_satuan' => 1000,
    ];

    // jumlah < terpakai
    $this->actingAs($d->user)->put(route('pengadaan-barang.update', $batch), $payload)->assertSessionHas('error');
    expect($batch->fresh()->jumlah)->toBe(10);

    // jumlah >= terpakai boleh
    $this->actingAs($d->user)->put(route('pengadaan-barang.update', $batch), array_merge($payload, ['jumlah' => 8]))
        ->assertRedirect(route('pengadaan-barang.index'));
    expect($batch->fresh()->jumlah)->toBe(8);

    // tidak bisa dihapus
    $this->actingAs($d->user)->delete(route('pengadaan-barang.destroy', $batch))->assertSessionHas('error');
    expect(PengadaanBarang::count())->toBe(1);
});

it('mengeluarkan Perlengkapan dari daftar mutasi, peminjaman, dan opname', function () {
    $d = siapkan();
    $aset = pengadaanUji($d, barangUji($d));
    $habisPakai = pengadaanUji($d, barangUji($d, ['jenis_barang' => 'perlengkapan']));

    $mutasi = $this->actingAs($d->user)->get(route('mutasi-aset.create'))->viewData('pengadaanBarang');
    $pinjam = $this->actingAs($d->user)->get(route('peminjaman.create'))->viewData('pengadaanBarang');

    expect($mutasi->pluck('id')->all())->toBe([$aset->id])
        ->and($pinjam->pluck('id')->all())->toBe([$aset->id]);

    $this->actingAs($d->user)->post(route('opname.store'), [
        'nama_opname' => 'Opname uji', 'tanggal_mulai' => '2026-03-01', 'lokasi_id' => $d->sub->id,
    ])->assertSessionHasNoErrors();
    $opname = \App\Models\OpnameDetail::pluck('pengadaan_id')->all();
    expect($opname)->toBe([$aset->id])->and($opname)->not->toContain($habisPakai->id);
});

// ---------------------------------------------------------------- anggaran

it('menghitung pagu, realisasi, sisa, dan persen dengan mengecualikan hibah', function () {
    $d = siapkan();
    $anggaran = Anggaran::create(['tahun' => 2026, 'lokasi_id' => $d->lokasi->id, 'pagu' => 10_000_000, 'is_active' => true]);
    $barang = barangUji($d);
    pengadaanUji($d, $barang, ['jumlah' => 1, 'harga_satuan' => 2_000_000, 'anggaran_id' => $anggaran->id]);
    pengadaanUji($d, $barang, ['jumlah' => 3, 'harga_satuan' => 1_000_000, 'anggaran_id' => $anggaran->id]);
    pengadaanUji($d, $barang, ['jumlah' => 1, 'harga_satuan' => 9_000_000, 'anggaran_id' => $anggaran->id, 'status' => 'hibah']);

    $a = Anggaran::withRealisasi()->find($anggaran->id);

    expect($a->realisasi)->toBe(5_000_000.0)
        ->and($a->sisa)->toBe(5_000_000.0)
        ->and($a->persen_realisasi)->toBe(50.0)
        // tanpa withRealisasi() hasilnya sama
        ->and(Anggaran::find($anggaran->id)->realisasi)->toBe(5_000_000.0);
});

it('menjaga satu anggaran per tahun per lokasi dan mencegah hapus anggaran yang dipakai', function () {
    $d = siapkan();

    $this->actingAs($d->user)->post(route('anggaran.store'), ['tahun' => 2026, 'lokasi_id' => $d->lokasi->id, 'pagu' => 5000000])
        ->assertRedirect(route('anggaran.index'));
    $this->actingAs($d->user)->post(route('anggaran.store'), ['tahun' => 2026, 'lokasi_id' => $d->lokasi->id, 'pagu' => 1])
        ->assertSessionHasErrors('lokasi_id');

    $anggaran = Anggaran::first();
    pengadaanUji($d, barangUji($d), ['anggaran_id' => $anggaran->id]);

    $this->actingAs($d->user)->delete(route('anggaran.destroy', $anggaran))->assertSessionHas('error');
    expect(Anggaran::count())->toBe(1);
});

it('memberi peringatan saat realisasi melebihi pagu tanpa memblokir', function () {
    $d = siapkan();
    $anggaran = Anggaran::create(['tahun' => 2026, 'lokasi_id' => $d->lokasi->id, 'pagu' => 1000, 'is_active' => true]);
    $barang = barangUji($d);

    $this->actingAs($d->user)->post(route('pengadaan-barang.store'), [
        'barang_id' => $barang->id, 'lokasi_id' => $d->sub->id, 'sumber' => 'APBS', 'status' => 'baru', 'status_id' => $d->status->id,
        'tanggal_pengadaan' => '2026-01-10', 'jumlah' => 1, 'satuan_id' => $d->satuan->id, 'harga_satuan' => 5000,
        'anggaran_id' => $anggaran->id,
    ])->assertRedirect()->assertSessionHas('warning');

    expect(PengadaanBarang::count())->toBe(1);
});

// ---------------------------------------------------------------- halaman & export (smoke)

it('menampilkan semua halaman baru tanpa error', function () {
    $d = siapkan();
    $anggaran = Anggaran::create(['tahun' => (int) date('Y'), 'lokasi_id' => $d->lokasi->id, 'pagu' => 9_000_000, 'is_active' => true]);
    $peralatan = barangUji($d, ['disusutkan' => true, 'masa_pemakaian_bulan' => 48, 'butuh_perawatan' => true]);
    $habisPakai = barangUji($d, ['jenis_barang' => 'perlengkapan', 'masa_pemakaian_bulan' => 6]);
    pengadaanUji($d, $peralatan, ['jumlah' => 1, 'harga_satuan' => 4_000_000, 'anggaran_id' => $anggaran->id, 'tanggal_pengadaan' => '2023-01-01']);
    pengadaanUji($d, $habisPakai, ['jumlah' => 10, 'anggaran_id' => $anggaran->id]);
    app(PerlengkapanService::class)->catat($habisPakai->id, 3, '2026-03-01', $d->sub->id, 'Bu Ani', 'ujian', $d->user->id);

    $this->actingAs($d->user);

    $halaman = [
        route('dashboard'),
        route('master-barang.index'), route('master-barang.index', ['jenis' => 'perlengkapan']),
        route('master-barang.create'), route('master-barang.edit', $peralatan), route('master-barang.show', $habisPakai),
        route('pengadaan-barang.index'), route('pengadaan-barang.index', ['jenis' => 'perlengkapan']),
        route('peralatan.index'), route('peralatan.perawatan'),
        route('pengadaan-barang.create'), route('pengadaan-barang.import'),
        route('pengadaan-barang.show', PengadaanBarang::first()), route('pengadaan-barang.edit', PengadaanBarang::first()),
        route('anggaran.index'), route('anggaran.create'), route('anggaran.edit', $anggaran),
        route('stok-perlengkapan.index'), route('pemakaian-perlengkapan.index'), route('pemakaian-perlengkapan.create'),
        route('laporan.index'), route('laporan.inventaris'), route('laporan.inventaris', ['jenis' => '']),
        route('laporan.nilai-aset'), route('laporan.penyusutan'), route('laporan.penyusutan', ['semua' => 1]),
        route('laporan.stok-perlengkapan'), route('laporan.pemakaian-perlengkapan'), route('laporan.realisasi-anggaran'),
    ];

    foreach ($halaman as $url) {
        $this->get($url)->assertOk();
    }

    foreach ([
        'laporan.inventaris.export', 'laporan.nilai-aset.export', 'laporan.penyusutan.export',
        'laporan.stok-perlengkapan.export', 'laporan.pemakaian-perlengkapan.export', 'laporan.realisasi-anggaran.export',
    ] as $nama) {
        $this->get(route($nama))->assertOk();
    }
});

it('menampilkan nilai perlengkapan dan peralatan yang benar di laporan nilai aset', function () {
    $d = siapkan();
    $peralatan = barangUji($d, ['disusutkan' => true, 'masa_pemakaian_bulan' => 48, 'interval_penyusutan_tahun' => 1]);
    $habisPakai = barangUji($d, ['jenis_barang' => 'perlengkapan']);
    pengadaanUji($d, $peralatan, ['jumlah' => 1, 'harga_satuan' => 12_000_000, 'tanggal_pengadaan' => '2026-03-15']);
    pengadaanUji($d, $habisPakai, ['jumlah' => 10, 'harga_satuan' => 1000, 'tanggal_pengadaan' => '2026-03-15']);
    app(PerlengkapanService::class)->catat($habisPakai->id, 4, '2026-04-01', $d->sub->id, 'Bu Ani', null, $d->user->id);

    $data = $this->actingAs($d->user)->get(route('laporan.nilai-aset', ['per_tanggal' => '2028-03-15']))->assertOk()->viewData('summary');

    // peralatan: 12jt − 2 tahun × 3jt = 6jt ; perlengkapan: sisa 6 × 1000 = 6.000
    expect($data['total_perolehan'])->toBe(12_010_000.0)
        ->and($data['total_nilai'])->toBe(6_006_000.0);
});

// ---------------------------------------------------------------- import Excel

/** Buat file xlsx template sederhana (heading sama dengan TemplateDataSheet). */
function buatXlsxImport(array $baris): \Illuminate\Http\UploadedFile
{
    $judul = [
        'kode_barang', 'nama_barang', 'kode_kategori_barang', 'merk_barang', 'tipe_barang', 'tahun_barang', 'sumber',
        'status', 'kode_kondisi', 'tanggal_pengadaan', 'jumlah', 'kode_satuan', 'harga_satuan', 'keterangan',
        'jenis_barang', 'disusutkan', 'masa_pemakaian', 'interval_penyusutan', 'butuh_perawatan',
    ];

    $book = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $book->getActiveSheet()->setTitle('Data Pengadaan')->fromArray(array_merge([$judul], $baris));

    $path = tempnam(sys_get_temp_dir(), 'imp') . '.xlsx';
    (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save($path);

    return new \Illuminate\Http\UploadedFile($path, 'template.xlsx', null, null, true);
}

it('mengimpor barang baru berjenis Peralatan dan Perlengkapan beserta anggaran', function () {
    \Illuminate\Support\Facades\Storage::fake('local');
    $d = siapkan();
    $anggaran = Anggaran::create(['tahun' => 2026, 'lokasi_id' => $d->lokasi->id, 'pagu' => 10_000_000, 'is_active' => true]);

    $file = buatXlsxImport([
        // barang baru: peralatan disusutkan 4 tahun, interval 2
        ['LPT-N', 'Laptop Baru', 'ELK', '', '', '', 'APBS', 'baru', 'BAIK', '2026-01-10', 2, 'PCS', 1_000_000, '', 'peralatan', 'ya', 4, 2, 'ya'],
        // barang baru: perlengkapan
        ['SPD-N', 'Spidol', 'ELK', '', '', '', 'APBS', 'baru', 'BAIK', '2026-01-11', 50, 'PCS', 5_000, '', 'perlengkapan', '', 6, '', ''],
        // barang baru tanpa jenis → error
        ['XXX-N', 'Tanpa Jenis', 'ELK', '', '', '', 'APBS', 'baru', 'BAIK', '2026-01-12', 1, 'PCS', 100, '', '', '', '', '', ''],
        // peralatan disusutkan tanpa masa → error
        ['YYY-N', 'Tanpa Masa', 'ELK', '', '', '', 'APBS', 'baru', 'BAIK', '2026-01-12', 1, 'PCS', 100, '', 'peralatan', 'ya', '', '', ''],
    ]);

    $preview = $this->actingAs($d->user)->post(route('pengadaan-barang.import.preview'), [
        'sub_lokasi_id' => $d->sub->id, 'anggaran_id' => $anggaran->id, 'file' => $file,
    ])->assertOk();

    expect($preview->viewData('validCount'))->toBe(2)
        ->and($preview->viewData('errorCount'))->toBe(2)
        ->and($preview->viewData('anggaran')->id)->toBe($anggaran->id);

    $this->actingAs($d->user)->post(route('pengadaan-barang.import.store'), [
        'sub_lokasi_id' => $d->sub->id, 'anggaran_id' => $anggaran->id, 'token' => $preview->viewData('token'),
    ])->assertRedirect(route('pengadaan-barang.index'));

    $laptop = MasterBarang::where('kode_barang', 'LPT-N')->first();
    $spidol = MasterBarang::where('kode_barang', 'SPD-N')->first();

    expect($laptop->jenis_barang)->toBe('peralatan')
        ->and($laptop->disusutkan)->toBeTrue()
        ->and($laptop->masa_pemakaian_bulan)->toBe(48)
        ->and($laptop->interval_penyusutan_tahun)->toBe(2)
        ->and($laptop->butuh_perawatan)->toBeTrue()
        ->and($spidol->jenis_barang)->toBe('perlengkapan')
        ->and($spidol->disusutkan)->toBeFalse()
        ->and($spidol->masa_pemakaian_bulan)->toBe(6)
        ->and(MasterBarang::where('kode_barang', 'XXX-N')->exists())->toBeFalse();

    $pengadaan = PengadaanBarang::orderBy('id')->get();
    expect($pengadaan)->toHaveCount(2)
        ->and($pengadaan->pluck('anggaran_id')->unique()->all())->toBe([$anggaran->id])
        ->and($pengadaan->pluck('kode_inventaris')->unique())->toHaveCount(2);

    // realisasi: 2 × 1.000.000 + 50 × 5.000
    expect(Anggaran::withRealisasi()->find($anggaran->id)->realisasi)->toBe(2_250_000.0);
});

it('mengunduh template import dengan kolom baru', function () {
    $d = siapkan();

    $this->actingAs($d->user)
        ->get(route('pengadaan-barang.import.template', ['sub_lokasi_id' => $d->sub->id]))
        ->assertOk();

    $this->actingAs($d->user)->get(route('pengadaan-barang.import'))->assertOk();
});
