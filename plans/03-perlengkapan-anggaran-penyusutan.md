# Plan: Peralatan vs Perlengkapan, Penyusutan, Stok/Pemakaian, dan Anggaran per Lokasi

> Status: **DIIMPLEMENTASIKAN (Tahap 1–7)** — 2026-09-21. Semua pertanyaan client sudah terjawab (usulan umur pakai per kategori dipakai sementara, lihat §7).
> Riwayat perawatan/maintenance **tidak** termasuk plan ini (akan dibuat di plan terpisah, lihat §0).

## Status Implementasi

Semua tahap di §6 sudah dikerjakan. Verifikasi: 38 test lulus (unit `NilaiBarangServiceTest`, feature `PerlengkapanAnggaranTest` termasuk import Excel), dan 32 halaman utama diuji terhadap data asli DB dev (semua 200 OK).

**Belum dijalankan (sengaja, menunggu keputusan pengguna):**
- `php artisan simaset:terapkan-umur-pakai --apply` — mengisi jenis/penyusutan master barang lama berdasarkan tabel usulan §6 Tahap 7. Tanpa `--apply` hanya dry-run. Sebelum dijalankan semua master lama berstatus "Tidak disusutkan" sehingga nilai buku = harga perolehan.
- Role non-admin belum diberi permission baru (`anggaran *`, `perlengkapan view`, `pemakaian *`); admin sudah otomatis (seeder dijalankan).

**Penyimpangan kecil dari rencana:**
- Menu "Riwayat Pemakaian" digabung ke satu halaman **Pemakaian & Riwayat** (form + daftar berfilter).
- Permission `penyusutan view` tidak dibuat: daftar aset & nilai buku memakai `pengadaan-barang view`, laporan memakai `laporan view`.
- Dropdown anggaran pada form import menampilkan semua anggaran aktif (tidak difilter per lokasi/tahun); pada form pengadaan terisi otomatis dari tahun + lokasi.
- Generator kode inventaris dibagi (`KodeInventarisGenerator`) untuk Pengadaan (store) dan Import. Tanah/Bangunan/Kendaraan **belum** dipindah karena format kodenya berbeda dari Pengadaan.
- Perbaikan bug existing yang ikut dikerjakan: dropdown kategori di edit Master Barang, filter kategori di Pengadaan, nilai awal tanggal di form pengadaan, label kolom Status/Kondisi yang tertukar, validasi `kode_inventaris` wajib pada update padahal tidak ada di form, serta angka nilai aset yang tidak konsisten (kini memakai `total_harga`).
- Test memakai database MySQL terpisah `simaduasset_test` (diatur di `phpunit.xml`) agar `RefreshDatabase` tidak menghapus data dev.

## 0. Ringkasan Keputusan Desain

1. **Master Barang tetap satu tabel** (`master_barang`), ditambah 5 kolom: `jenis_barang`, `disusutkan`, `masa_pemakaian_bulan`, `interval_penyusutan_tahun`, `butuh_perawatan`. Semua data lama otomatis menjadi **Peralatan** (default) → tidak ada data yang rusak.
2. **`pengadaan_barang` tetap satu tabel** dan dipakai untuk kedua jenis. Untuk Peralatan = record aset (seperti sekarang). Untuk Perlengkapan = **batch stok masuk** (penerimaan). Ditambah `anggaran_id` (nullable).
3. **Tabel baru hanya 2**: `anggaran` (pagu per **lokasi** per tahun) dan `pemakaian_perlengkapan`. Tidak ada tabel penyusutan, tidak ada tabel stok.
4. **Penyusutan Peralatan: admin memilih sendiri disusutkan atau tidak** (saklar Ya/Tidak per barang di Master Barang, bisa ditimpa per aset di Pengadaan). Jika disusutkan: garis lurus dengan interval yang dipilih per barang (tiap 1 tahun, 2 tahun, 3 tahun, dst.), turun pada **tanggal ulang tahun pembelian** masing-masing aset. Dihitung on-the-fly oleh service class, bukan disimpan. Stok = `jumlah masuk − Σ pemakaian` (dihitung, bukan kolom `sisa`), agar tidak bisa drift.
5. **Menu operasional dipisah** (Peralatan vs Perlengkapan), tapi **Pengadaan tetap satu form/satu tabel**.
6. Perlengkapan **tidak** dijadikan aset tetap: tidak ikut mutasi, opname, peminjaman, QR label, dan tidak ikut total nilai aset tetap. **Nilai Perlengkapan langsung 0 saat dipakai** (tanpa penyusutan bertahap).
7. **Perawatan:** plan ini hanya menyediakan penanda `butuh_perawatan` (Ya/Tidak). **Pencatatan riwayat perawatan/maintenance dikerjakan di plan terpisah** (`plans/04-…`, dibuat setelah plan ini disetujui).

### Keputusan client (dari jawaban)
| # | Topik | Keputusan | Dampak di plan |
|---|---|---|---|
| 1 | Penyusutan | Bisa dipilih **per 1 tahun, 2 tahun, 3 tahun, dst.**; besar penurunan **berbeda untuk tiap barang**; nilai turun pada **tanggal ulang tahun pembelian** barang tersebut | §5.2: kolom `interval_penyusutan_tahun` + `masa_pemakaian_bulan` diatur per barang di Master Barang; turun tiap N tahun sejak `tanggal_pengadaan` |
| 2 | Barang tidak disusutkan | Semula: masa boleh dikosongkan. Lalu diminta: admin bisa **memilih sendiri** disusutkan/tidak per barang/aset | Saklar eksplisit `disusutkan` (Ya/Tidak) di Master Barang + penimpa per aset di Pengadaan (§4, §5.2). Masa kosong tidak lagi dipakai sebagai penanda |
| 3 | Perlengkapan | **Semua** barang habis pakai (termasuk sapu, lap, dll.) **nilai langsung 0 saat dipakai** | §5.5 disederhanakan: tidak ada penurunan bertahap, tidak perlu snapshot masa di tabel pemakaian; masa pemakaian Perlengkapan hanya informasi |
| 4 | Pemakaian | Dikaitkan ke **lokasi** dan **nama pemakai**; alokasi batch diserahkan ke kami | Lokasi & pemakai **wajib**; alokasi FIFO otomatis |
| 5 | Anggaran | Dipecah per **lokasi = unit besar** (Master Lokasi, bukan sub lokasi/kelas) | `anggaran.lokasi_id` → `master_lokasi`, unik per tahun+lokasi |
| 6 | Perawatan | Perlu riwayat maintenance, **plan terpisah** | Di luar plan ini; hanya flag `butuh_perawatan` |
| 7 | Umur pakai data lama | Kami yang mengusulkan; **dipakai sementara sesuai usulan** (Bangunan 20 th, Olahraga 2 th, Kitab tidak disusutkan) | Tabel usulan di §6 Tahap 7; bisa diubah per barang kapan saja |

---

## 1. Existing System (hasil audit)

**Stack:** Laravel 11, Blade + Bootstrap 5 (Sneat-like), Livewire 3 (hanya untuk form permohonan), maatwebsite/excel, dompdf, spatie/laravel-permission. **Bukan Filament.** Pola: resource controller + `HasMiddleware` (`permission:<grup> <aksi>`), navigasi di [config/navigation.php](../config/navigation.php), permission di [config/permission.php](../config/permission.php) (di-seed via `PermissionSeeder`, admin otomatis dapat semua). Test: Pest terpasang tapi hanya berisi `ExampleTest`.

**Inti data:**

| Tabel / Model | Peran saat ini |
|---|---|
| `kategori_barang` | Kategori (Elektronik, Furniture, Kendaraan, Bangunan, Tanah, ATK, Komputer, Olahraga, Kitab). Dipakai juga sebagai pembeda Tanah/Bangunan/Kendaraan via `LIKE '%tanah%'` dst. |
| `master_barang` | `kode_barang`, `nama_barang`, merk, tipe, tahun, `kategori_barang_id`, `is_active`, `status_permohonan` (pending/approved/rejected). Scope `approved()` menentukan barang yang boleh dipengadaankan. |
| `pengadaan_barang` | **Sekaligus tabel aset.** 1 baris = 1 record inventaris: `kode_inventaris` (unik), `barang_id`, `lokasi_id` (sub lokasi), `sumber` (string bebas), `status` (baru/bekas/hibah), `status_id` (kondisi), `tanggal_pengadaan`, `jumlah`, `satuan_id`, `harga_satuan`, `total_harga`, `is_active`, `is_borrowed`, `permohonan_id`, `detail_permohonan_id`. |
| `master_lokasi` → `master_sub_lokasi` | Lokasi = unit (MTs Putra, MA Putri, Keuangan, Sarana Prasarana, …); Sub lokasi = ruangan/kelas di dalamnya. Pengadaan menunjuk **sub lokasi**. |
| `tanah_details`, `bangunan_details`, `kendaraan_details` | Ekstensi 1:1 ke `pengadaan_barang` (`pengadaan_id`). |
| `permohonan` + `detail_permohonan` | Usulan pengadaan; punya `tahun_anggaran`, `bidang`, `kode_ma` (teks bebas). Approve → set `master_barang.status_permohonan='approved'`. **Tidak ada nilai anggaran/pagu.** |
| `mutasi_aset`, `peminjaman`, `opname_*` | Semua FK ke `pengadaan_barang.id` — model "aset individual". |

**Yang BELUM ada sama sekali** (dicek via grep `penyusutan|susut|nilai_buku|anggaran|stok|perawatan`): penyusutan/nilai buku, stok/persediaan, pemakaian, perawatan, master anggaran/pagu/realisasi. Satu-satunya jejak "anggaran" adalah kolom teks `permohonan.tahun_anggaran` dan `detail_permohonan.kode_ma`.

**Fitur yang menyentuh `PengadaanBarang` (harus dicek dampaknya):** `PengadaanBarangController` (CRUD, QR, **import Excel** yang juga membuat `master_barang` baru on-the-fly), `LaporanController` + `LaporanInventarisExport` + `LaporanNilaiAsetExport`, `DashboardController`, `MutasiAsetController` (`aktif()->get()`), `OpnameController` (generate detail dari `aktif()`), `PeminjamanController` (`aktif()->where('is_borrowed', false)`), `Tanah/Bangunan/KendaraanController`.

**Temuan existing yang relevan (bukan bagian scope, tapi kena sentuh):**
- `MasterBarangController::edit` mengirim `KategoriBarang::pluck('nama_kategori_barang')` **tanpa key id** → dropdown kategori di form edit salah petakan (index, bukan id). Form yang sama akan kita ubah, jadi perbaiki sekalian.
- `PengadaanBarangController::index` filter `kategori_id` memakai kolom `kategori_id` di `master_barang` yang **tidak ada** (kolomnya `kategori_barang_id`) → error saat filter kategori dipakai.
- `pengadaan-barang/includes/form.blade.php`: `value1` tanggal_pengadaan diisi dari `$pengadaanBarang?->sumber` (salah kolom).
- Generator `kode_inventaris` berbasis `count()+1` per bulan, **diduplikasi di 5 tempat** (Pengadaan store, import, Tanah, Bangunan, Kendaraan). Rawan bentrok setelah ada data yang dihapus. Perlengkapan akan menambah banyak baris → rekomendasi diekstrak jadi satu helper dan pakai suffix maksimum, bukan `count()`.
- `laporan/nilai-aset` menjumlah `harga_satuan` (mengabaikan `jumlah`), sementara export-nya `jumlah × harga_satuan`, dan dashboard memakai `total_harga`. Tiga angka nilai yang tidak konsisten → dijadikan satu (`total_harga`) saat nilai buku diperkenalkan.
- MySQL-spesifik (`YEAR()`, `MONTH()` di dashboard) → test DB harus MySQL, bukan SQLite in-memory.

---

## 2. Gap Analysis

| Kebutuhan | Existing | Perubahan |
|---|---|---|
| Jenis barang Peralatan/Perlengkapan | Tidak ada; hanya kategori | Kolom `master_barang.jenis_barang` (default `peralatan`) + validasi form |
| Masa pemakaian | Tidak ada | `master_barang.masa_pemakaian_bulan` (nullable). Peralatan: > 1 tahun (input dalam **tahun**), wajib diisi bila barang disusutkan. Perlengkapan: kosong atau 1–12 bulan, **hanya informasi** |
| Bisa/tidak disusutkan (dipilih admin) | Tidak ada | `master_barang.disusutkan` (bool) sebagai default + `pengadaan_barang.disusutkan` (nullable; NULL = ikut master) untuk menimpa per aset |
| Interval penyusutan | Tidak ada | `master_barang.interval_penyusutan_tahun` (1, 2, 3, …; default 1), dipilih per barang |
| Butuh perawatan Ya/Tidak | Tidak ada | `master_barang.butuh_perawatan` (bool, default false) + badge/filter. Riwayat perawatan → plan terpisah |
| Penyusutan & nilai buku Peralatan | Tidak ada | `NilaiBarangService` (garis lurus, per interval N tahun, on-the-fly) + kolom di daftar aset + Laporan Penyusutan |
| Perlengkapan sebagai stok masuk | Semua barang dianggap aset | Pengadaan jenis perlengkapan = batch stok masuk; tanpa QR/mutasi/opname/peminjaman |
| Transaksi pemakaian & histori | Tidak ada | Tabel `pemakaian_perlengkapan` (lokasi + pemakai wajib) + menu Stok/Pemakaian/Riwayat |
| Perlengkapan dihitung sejak digunakan | Tidak ada | Tanggal pemakaian dicatat; nilai barang yang sudah dipakai = 0, dilaporkan sebagai "nilai terpakai" |
| Nilai barang saat ini | Hanya `total_harga` (nilai perolehan) | `nilaiSaatIni($asOf)` per jenis; laporan & dashboard memakainya |
| Anggaran tahunan (pagu) | Hanya `tahun_anggaran` teks di permohonan | Tabel `anggaran` (tahun, **lokasi**, pagu) |
| Pengadaan dikaitkan ke anggaran | `sumber` teks bebas | `pengadaan_barang.anggaran_id` (nullable) + dropdown di form & import |
| Pagu, realisasi, sisa, % realisasi | Tidak ada | Dihitung: realisasi = Σ `total_harga` pengadaan ter-link; laporan + kartu dashboard |
| Aset-only feature tidak boleh kemasukan perlengkapan | Semua `aktif()` diambil | Scope `peralatan()` dipasang di Mutasi, Opname, Peminjaman, QR bulk, laporan aset, dashboard |
| Import Excel pengadaan | Membuat master barang baru tanpa jenis | Tambah kolom template (`jenis_barang`, `disusutkan`, `masa_pemakaian`, `interval_penyusutan`, `butuh_perawatan` untuk barang baru) + pilih anggaran di form import |

---

## 3. Proposed Structure

```text
Master Barang (1 tabel, +jenis_barang, +disusutkan, +masa_pemakaian_bulan, +interval_penyusutan_tahun, +butuh_perawatan)
├── Peralatan     (jenis_barang = peralatan; saklar disusutkan Ya/Tidak; bila Ya: masa > 1 tahun + interval 1/2/3… tahun)
└── Perlengkapan  (jenis_barang = perlengkapan, nilai 0 saat dipakai)

Pengadaan (1 tabel pengadaan_barang, +anggaran_id)  ── Anggaran Tahunan per Lokasi (tabel anggaran)
├── barang Peralatan     → record aset (kode_inventaris, QR, kondisi, dipinjam/mutasi/opname)
└── barang Perlengkapan  → batch stok masuk (jumlah, harga_satuan, tanggal terima)

Peralatan
├── Aset        = pengadaan_barang WHERE jenis = peralatan   (tidak ada tabel baru)
├── Penyusutan  = kalkulasi per interval N tahun (NilaiBarangService) + laporan   (tidak ada tabel baru)
└── Perawatan   = flag butuh_perawatan + filter                (riwayat maintenance → PLAN TERPISAH)

Perlengkapan
├── Stok        = batch pengadaan − Σ pemakaian              (tidak ada tabel baru)
└── Pemakaian   = tabel pemakaian_perlengkapan (histori: lokasi + pemakai)
```

**Menu operasional: dipisah, data tidak dipisah.** Alasan: kata kerja user berbeda (Peralatan → mutasi/opname/pinjam/QR/kondisi; Perlengkapan → terima/pakai/sisa). Yang dipisah hanya menu dan tampilan; tabel tetap satu supaya anggaran, import, laporan pengadaan, dan permohonan tidak perlu diduplikasi.

Usulan `config/navigation.php`:

```text
Barang                       (master-barang, satu menu, ada filter/badge jenis)
Permohonan
Inventaris
├── Pengadaan Barang         (satu form; tab/filter jenis: Semua | Peralatan | Perlengkapan)
├── Tanah / Bangunan / Kendaraan   (tetap; otomatis Peralatan)
Peralatan   [baru]
├── Daftar Aset & Nilai Buku       (= pengadaan-barang.index?jenis=peralatan + kolom nilai buku)
└── Perlu Perawatan                (filter butuh_perawatan=1)   [cukup filter]
Perlengkapan [baru]
├── Stok Perlengkapan
├── Pemakaian                      (form + daftar)
└── Riwayat Pemakaian
Anggaran    [baru]
├── Anggaran Tahunan               (CRUD pagu per lokasi)
└── Realisasi Anggaran             (laporan)
Mutasi Aset / Opname / Peminjaman  (tetap, hanya Peralatan)
Laporan  (+ Penyusutan, Stok, Pemakaian, Realisasi Anggaran)
```

Untuk mengurangi controller baru: "Daftar Aset" tidak butuh controller sendiri (reuse `PengadaanBarangController@index` dengan parameter `jenis`).

---

## 4. Database Changes

Semua migration **additive** (kolom nullable/ber-default). Tidak ada `drop`/`rename` kolom existing. Backup DB sebelum `migrate` di production.

### 4.1 Tabel existing

**`master_barang`** (+5 kolom)
| Kolom | Tipe | Default | Catatan |
|---|---|---|---|
| `jenis_barang` | enum(`peralatan`,`perlengkapan`) | `peralatan` | Data lama otomatis peralatan |
| `masa_pemakaian_bulan` | unsigned smallint, nullable | NULL | Disimpan dalam **bulan**; form Peralatan menerima **tahun** (×12), form Perlengkapan menerima bulan. Peralatan: wajib diisi bila `disusutkan` = Ya, boleh kosong bila Tidak. Perlengkapan: hanya informasi (tidak memengaruhi nilai) |
| `disusutkan` | boolean | false | Pilihan admin: barang ini disusutkan atau tidak. Hanya untuk Peralatan (disembunyikan untuk Perlengkapan). Default false → data lama tidak berubah sampai diatur |
| `interval_penyusutan_tahun` | unsigned tinyint | 1 | Nilai turun tiap N tahun. Hanya berlaku bila `disusutkan` = Ya. **Masa (tahun) harus kelipatan interval** (divalidasi di form) |
| `butuh_perawatan` | boolean | false | |

**`pengadaan_barang`** (+2 kolom)
| Kolom | Tipe | Catatan |
|---|---|---|
| `anggaran_id` | FK nullable → `anggaran.id`, `restrictOnDelete`, indexed | Data lama = NULL (tampil "Belum dikaitkan anggaran") |
| `disusutkan` | boolean nullable | **NULL = ikut Master Barang** (kasus umum, semua data lama). `true`/`false` = admin menimpa khusus aset ini. Dibaca lewat `disusutkan_efektif = pengadaan.disusutkan ?? master_barang.disusutkan` |

Tidak ada kolom `tanggal_mulai_penyusutan`: mulai susut = `tanggal_pengadaan` sesuai kebutuhan client.

### 4.2 Tabel baru

**`anggaran`**
`id`, `tahun` (year), `lokasi_id` (FK → `master_lokasi`, restrict — unit besar), `pagu` (decimal 15,2), `keterangan` (text null), `is_active` (bool, default true), `created_by`, `updated_by`, timestamps. **Unique (`tahun`, `lokasi_id`)**. Label tampil dibentuk otomatis, mis. "Anggaran 2026 – MTs Putra" (tidak perlu kolom kode/nama).

**`pemakaian_perlengkapan`**
`id`, `pengadaan_id` (FK → `pengadaan_barang`, restrict, indexed), `jumlah` (unsigned int), `tanggal_pemakaian` (date), `lokasi_id` (FK → `master_sub_lokasi`, **wajib**, dipakai di mana), `pemakai` (string, **wajib**), `keperluan` (text null), **snapshot** `harga_satuan` (decimal 15,2 — agar "nilai terpakai" pada histori tidak berubah bila harga pengadaan diedit belakangan), `created_by`, timestamps, **`softDeletes`** (histori tetap ada; batal pemakaian = soft delete + stok otomatis kembali).

### 4.3 Relationship baru
- `MasterBarang`: konstanta jenis, scope `peralatan()` / `perlengkapan()`, cast boolean.
- `PengadaanBarang`: `anggaran()` belongsTo; `pemakaian()` hasMany; scope `peralatan()` / `perlengkapan()` (via `whereHas('barang')`); accessor `nilai_saat_ini`, `nilai_buku`, `stok_tersedia` (delegasi ke service).
- `Anggaran`: `lokasi()` belongsTo `MasterLokasi`; `pengadaan()` hasMany; accessor `realisasi`, `sisa`, `persen_realisasi`.
- `PemakaianPerlengkapan`: `pengadaan()`, `lokasi()`, `createdBy()`.
- Permission baru (config/permission.php + rerun `PermissionSeeder`): `anggaran view/create/edit/delete`, `perlengkapan view` (stok), `pemakaian view/create/delete`, `penyusutan view`. Laporan baru memakai `laporan view/export` yang sudah ada. Catatan: role non-admin harus diberi permission baru secara manual.

**Rule integritas:** jenis barang **tidak boleh diubah** bila sudah punya pengadaan; barang perlengkapan yang sudah ada pemakaian: `barang_id` tidak boleh diganti dan `jumlah` tidak boleh < total terpakai; pengadaan yang sudah punya pemakaian tidak bisa dihapus (FK restrict + pesan yang jelas, mengikuti pola pesan di `destroy` existing); anggaran yang sudah dipakai pengadaan tidak bisa dihapus.

---

## 5. Business Logic

### 5.1 Klasifikasi
| | Peralatan | Perlengkapan |
|---|---|---|
| Disusutkan? | **Pilihan admin** (saklar Ya/Tidak; default dari Master Barang, bisa ditimpa per aset) | Tidak berlaku |
| Masa pemakaian | > 1 tahun (diisi dalam tahun); wajib bila disusutkan | Kosong atau 1–12 bulan (informasi saja) |
| Sifat | Aset individual (QR, kondisi, mutasi, opname, pinjam) | Persediaan (stok, pemakaian) |
| Mulai dihitung | `tanggal_pengadaan` (pembelian/penerimaan) | `tanggal_pemakaian` (saat dipakai) |
| Dasar nilai | `total_harga` | `harga_satuan` × jumlah |
| Nilai setelah dipakai | — | **0** |

Klasifikasi diambil dari `master_barang.jenis_barang` (sumber tunggal), divalidasi terhadap `masa_pemakaian_bulan` dan `interval_penyusutan_tahun` di Store/Update Master Barang.

### 5.2 Penyusutan & nilai buku Peralatan (garis lurus, interval N tahun)
Tiap barang punya **umur pakai** (`masa_pemakaian_bulan`) dan **interval** (`interval_penyusutan_tahun`) sendiri di Master Barang, sehingga besar penurunannya berbeda antar barang. Nilai turun pada **ulang tahun tanggal pembelian** tiap N tahun.

```
disusutkan_efektif      = pengadaan.disusutkan ?? master_barang.disusutkan       # NULL di aset = ikut master
                          jika Tidak → nilai_buku = nilai_perolehan, akumulasi 0, selesai
masa_tahun              = masa_pemakaian_bulan / 12
interval                = interval_penyusutan_tahun                              # 1, 2, 3, …  (masa_tahun kelipatan interval)
jumlah_langkah          = masa_tahun / interval                                  # berapa kali nilai turun sampai habis
nilai_perolehan         = pengadaan.total_harga
penyusutan_per_langkah  = (nilai_perolehan − nilai_residu) / jumlah_langkah      # residu = 0
tahun_penuh             = jumlah tahun penuh sejak tanggal_pengadaan s/d tanggal_hitung
langkah_berjalan        = min(jumlah_langkah, floor(tahun_penuh / interval))
akumulasi_penyusutan    = penyusutan_per_langkah × langkah_berjalan
nilai_buku              = nilai_perolehan − akumulasi_penyusutan                 # tidak pernah < 0
```
Contoh: laptop dibeli **15 Mar 2026**, Rp 12.000.000, umur pakai 4 tahun:
- Interval **1 tahun** → turun Rp 3.000.000 tiap 15 Maret: 9 jt (2027) → 6 jt (2028) → 3 jt (2029) → 0 (2030).
- Interval **2 tahun** → turun Rp 6.000.000 tiap 2 tahun: 12 jt sampai 14 Mar 2028 → 6 jt (15 Mar 2028) → 0 (15 Mar 2030).

Catatan:
- **Tidak disusutkan** (mis. Tanah): nilai buku = nilai perolehan, akumulasi 0, ditampilkan "Tidak disusutkan"; tetap ikut dijumlahkan di total nilai.
- Menyalakan penyusutan (di Master Barang maupun di aset) mensyaratkan masa dan interval sudah terisi; bila belum, form menolak dengan pesan yang jelas.
- Perhitungan selalu dari `tanggal_pengadaan` (**retroaktif**): bila saklar diubah dari Tidak ke Ya belakangan, akumulasi langsung menyesuaikan sampai hari ini. Form menampilkan peringatan saat saklar diubah.
- Saklar Master Barang diubah lewat permission `master-barang edit`; penimpa per aset lewat `pengadaan-barang edit`. Aset yang ditimpa diberi badge "Diatur manual" di daftar.
- Tanggal hitung default = hari ini; laporan bisa memilih "per tanggal".
- Dua aset dari barang yang sama tetapi dibeli di tanggal berbeda turun di tanggal berbeda (mengikuti tanggal pembelian masing-masing).
- Rumus berupa fungsi murni (input: harga, tanggal beli, masa, interval, as-of) → mudah di-unit-test.

### 5.3 Stok Perlengkapan
```
stok_masuk (batch)   = pengadaan_barang.jumlah        (barang jenis perlengkapan)
terpakai (batch)     = Σ pemakaian_perlengkapan.jumlah (non-soft-deleted)
sisa (batch)         = stok_masuk − terpakai
stok barang          = Σ sisa semua batch barang tsb (per lokasi penyimpanan bila difilter)
```
Diambil dengan `withSum('pemakaian','jumlah')`, tanpa kolom `sisa` tersimpan.

### 5.4 Pemakaian Perlengkapan
- Input: barang, jumlah, tanggal, **lokasi (wajib)**, **nama pemakai (wajib)**, keperluan (opsional).
- **Alokasi FIFO otomatis**: sistem mengambil dari batch tertua yang masih punya sisa; bila jumlah melewati satu batch, dibuat beberapa baris pemakaian (satu per batch) dalam satu transaksi.
- Cek stok dilakukan **di dalam transaksi dengan `lockForUpdate()`** pada batch terkait → mencegah stok negatif akibat dua input bersamaan. Jumlah > total sisa → ditolak.
- Pemakaian menyimpan snapshot `harga_satuan` (untuk nilai terpakai).
- Koreksi = soft delete (dengan permission `pemakaian delete`); tidak ada edit jumlah agar histori bersih.

### 5.5 Nilai barang saat ini
- **Peralatan** = `nilai_buku` (§5.2).
- **Perlengkapan** = **nilai persediaan** saja = Σ `sisa × harga_satuan` (belum dipakai; tidak menyusut). Begitu dipakai, nilainya menjadi **0** dan berpindah ke **"nilai terpakai"** = Σ `jumlah × harga_snapshot` per pemakaian, yang ditampilkan di Laporan Pemakaian (per periode/lokasi/pemakai).
- Satu method `PengadaanBarang::nilaiSaatIni($asOf)` mengembalikan angka yang sesuai jenisnya, dipakai laporan & dashboard.

> ✅ **Catatan akuntansi:** pilihan client "nilai langsung 0 saat dipakai" sudah sesuai praktik akuntansi persediaan (barang habis pakai dibebankan saat dipakai, bukan disusutkan). Hanya *Aset Tetap* (Peralatan) yang mengenal *penyusutan*. Di UI/laporan Perlengkapan gunakan label **"Nilai Persediaan"** dan **"Nilai Terpakai"**; label **"Penyusutan / Nilai Buku"** hanya untuk Peralatan. Perlengkapan tidak dijadikan Aset Tetap.

### 5.6 Anggaran & realisasi (per lokasi)
```
pagu               = anggaran.pagu                       (satu baris per tahun + lokasi/unit)
realisasi          = Σ pengadaan_barang.total_harga WHERE anggaran_id = anggaran.id
                     AND status <> 'hibah'               (Peralatan + Perlengkapan)
sisa               = pagu − realisasi
persen_realisasi   = pagu > 0 ? realisasi / pagu × 100 : 0
```
- Realisasi bertambah saat pengadaan dibuat/diimpor; berubah bila total pengadaan diedit; berkurang bila dihapus (dihitung ulang, tak ada angka tersimpan).
- Form pengadaan: dropdown anggaran diisi otomatis dari **tahun** `tanggal_pengadaan` + **lokasi** (induk dari sub lokasi yang dipilih), namun tetap bisa diganti manual — karena Perlengkapan sering disimpan di gudang lokasi lain dari unit yang membayarnya.
- Realisasi + pengadaan baru > pagu → **peringatan saja** (tidak memblokir).
- Pengadaan **hibah** tidak dihitung ke realisasi (tidak mengeluarkan uang).
- Pengadaan lama tanpa `anggaran_id` tampil sebagai baris "Belum dikaitkan" di laporan realisasi, tidak merusak angka.
- *(Asumsi tentang hibah, peringatan, dan tidak ada backfill otomatis dicatat di §7 bagian "Asumsi".)*

---

## 6. Implementation Plan

Urutan dirancang agar tiap tahap bisa dirilis sendiri dan tidak merusak fitur berjalan.

### Tahap 1 — Database & Model
- Migration: `add_jenis_disusutkan_masa_interval_perawatan_to_master_barang_table`, `create_anggaran_table`, `add_anggaran_id_and_disusutkan_to_pengadaan_barang_table`, `create_pemakaian_perlengkapan_table`.
- Model: ubah [MasterBarang.php](../app/Models/MasterBarang.php) (fillable, cast, scope), [PengadaanBarang.php](../app/Models/PengadaanBarang.php) (relasi, scope); baru `Anggaran.php`, `PemakaianPerlengkapan.php`.
- Baru `app/Services/NilaiBarangService.php` (penyusutan per interval, nilai persediaan, stok) — fungsi murni, tanpa query di dalam rumus.
- Tujuan: fondasi; app tetap berjalan identik karena semua default = peralatan.

### Tahap 2 — Master Barang
- [MasterBarangController.php](../app/Http/Controllers/MasterBarangController.php), `resources/views/master-barang/includes/form.blade.php`, `index-table.blade.php`, `show.blade.php`: field jenis, masa pemakaian (Peralatan: input **tahun**, boleh kosong; Perlengkapan: input **bulan**, boleh kosong, hanya informasi), **saklar Disusutkan (Ya/Tidak)** dan **interval penyusutan** (dropdown 1, 2, 3, … tahun) — saklar tampil untuk Peralatan; masa & interval hanya tampil bila saklar = Ya, butuh perawatan; kolom badge jenis; filter jenis di index. Form menampilkan pratinjau kecil: "Turun Rp X setiap N tahun" bila contoh harga diisi (opsional).
- Validasi: Peralatan disusutkan → masa wajib, > 1 tahun, dan kelipatan interval; Peralatan tidak disusutkan → masa boleh kosong; Perlengkapan → masa kosong atau 1–12 bulan. Guard ubah jenis bila sudah dipakai pengadaan.
- Perbaiki bug `edit()` (`pluck` tanpa key) di file yang sama.
- Catatan: `index()` memakai `getFillable()` sebagai kolom pencarian; kolom baru otomatis ikut, pastikan pencarian pada kolom enum/boolean tidak error.

### Tahap 3 — Anggaran & Pengadaan
- Baru: `AnggaranController` (resource), views `anggaran/*`, route + permission + menu. Halaman index: tahun, lokasi (unit), pagu/realisasi/sisa/% (progress bar); filter tahun & lokasi.
- [PengadaanBarangController.php](../app/Http/Controllers/PengadaanBarangController.php) + `pengadaan-barang/includes/form.blade.php`: dropdown `anggaran_id` (auto-pilih dari tahun + lokasi, bisa diganti; peringatan bila melebihi pagu); pilihan **Disusutkan** per aset khusus Peralatan (*Ikut Master Barang* / *Ya* / *Tidak*); filter `jenis`; untuk perlengkapan `status_id` diisi default "Baik" otomatis dan QR disembunyikan; guard update/destroy (lihat §4.3); perbaiki bug filter `kategori_id` & `value1` tanggal.
- Import ([PengadaanBarangImport.php](../app/Imports/PengadaanBarangImport.php), `TemplateDataSheet`, `TemplateReferensiSheet`, `importForm/storeImport`): pilih anggaran per batch di form import (difilter mengikuti lokasi + tahun); kolom `jenis_barang`, `disusutkan`, `masa_pemakaian`, `interval_penyusutan`, `butuh_perawatan` wajib **hanya bila `kode_barang` baru** (agar barang on-the-fly tidak salah jenis).
- Ekstrak generator `kode_inventaris` ke satu helper dan pakai di Pengadaan store, import, Tanah, Bangunan, Kendaraan (rekomendasi; bisa dipisah jadi PR kecil).

### Tahap 4 — Peralatan & Penyusutan
- `PengadaanBarangController@index` + `index-table.blade.php` + `show.blade.php`: kolom Akumulasi Penyusutan, Nilai Buku, status Disusutkan/Tidak (badge "Diatur manual" bila ditimpa per aset), tanggal penurunan nilai berikutnya, badge "Perlu Perawatan" (saat filter jenis=peralatan).
- Pasang `->peralatan()` pada sumber data aset: `MutasiAsetController` (2 tempat), `OpnameController@store`, `PeminjamanController` (create/edit), daftar QR bulk di `PengadaanBarangController@index`.
- Baru: `PenyusutanController@index` (+ export) atau method di `LaporanController`; menu "Peralatan".

### Tahap 5 — Perlengkapan & Pemakaian
- Baru: `StokPerlengkapanController@index` (stok per barang/batch, filter lokasi), `PemakaianPerlengkapanController` (index/create/store/destroy), views `perlengkapan/*`, route (didefinisikan sebelum wildcard, sesuai konvensi `routes/web.php`), permission, menu.
- Form pemakaian: barang, jumlah (dengan info sisa stok), tanggal, lokasi (wajib), nama pemakai (wajib), keperluan.
- Logic FIFO + `lockForUpdate` di dalam service/controller (dibungkus `DB::transaction`, gaya `beginTransaction` existing).
- Riwayat pemakaian: filter periode/barang/lokasi/pemakai, menampilkan nilai terpakai.

### Tahap 6 — Laporan & Dashboard
- [LaporanController.php](../app/Http/Controllers/LaporanController.php), `app/Exports/*`, `resources/views/laporan/*` (termasuk kartu di `laporan/index.blade.php`):
  - update **Laporan Nilai Aset** (pakai `total_harga`, tambah kolom akumulasi/nilai buku, filter jenis & per tanggal);
  - update **Laporan Inventaris** (kolom/filter jenis; default tampilan peralatan);
  - baru: Laporan Penyusutan, Laporan Stok Perlengkapan, Laporan Pemakaian, Laporan Realisasi Anggaran per lokasi (+ export Excel mengikuti pola `FromCollection/WithHeadings/WithMapping`).
- [DashboardController.php](../app/Http/Controllers/DashboardController.php) + `dashboard.blade.php`: total aset/nilai hanya Peralatan; kartu baru: nilai buku peralatan, nilai persediaan perlengkapan, jumlah butuh perawatan, pagu/realisasi/sisa/% tahun berjalan.

### Tahap 7 — Testing & Migrasi Data Lama
- Pest unit test `NilaiBarangService`: saklar tidak disusutkan (nilai buku = nilai perolehan), penimpa aset vs master, penyusutan interval 1/2/3 tahun (baru dibeli, sehari sebelum & tepat pada ulang tahun, lewat masa, masa NULL), tanggal beli 29 Februari, stok/sisa.
- Feature test: FIFO lintas batch, tolak stok kurang, soft-delete mengembalikan stok, guard ubah jenis, validasi masa kelipatan interval, realisasi anggaran (hibah dikecualikan), scope peralatan di Mutasi/Opname/Peminjaman. Test DB harus MySQL.
- Data lama: (a) `migrate` non-destruktif, semua master → peralatan, `disusutkan` = Tidak, masa kosong, interval 1 (belum ada yang disusutkan sampai tabel usulan dijalankan); (b) command/seeder sekali jalan mengisi jenis/`disusutkan`/masa/interval per **kategori** memakai tabel usulan di bawah (**dipakai sementara**) (per barang tetap bisa diubah manual di Master Barang); (c) master yang berpindah ke Perlengkapan didahului laporan dry-run yang mendaftar pengadaan/mutasi/peminjaman/opname yang terdampak — baris lama akan menjadi batch stok penuh (0 pemakaian) dan hilang dari daftar aset, riwayat lamanya tetap utuh; (d) pengadaan lama dibiarkan tanpa anggaran (bisa dikaitkan manual belakangan).
- Backup DB + uji di salinan data production sebelum rilis; siapkan `down()` di setiap migration.

**Usulan awal umur pakai per kategori** — angka berdasarkan kelaziman umum, **bukan** aturan resmi. **Dipakai sementara** atas persetujuan client; tiap barang/aset tetap bisa diubah kapan saja (termasuk saklar Disusutkan).

| Kategori | Jenis | Disusutkan | Umur pakai | Interval turun | Catatan |
|---|---|---|---|---|---|
| Elektronik | Peralatan | Ya | 4 tahun | 1 tahun | |
| Komputer & IT | Peralatan | Ya | 4 tahun | 1 tahun | |
| Furniture | Peralatan | Ya | 5 tahun | 1 tahun | |
| Kendaraan | Peralatan | Ya | 8 tahun | 1 tahun | |
| Bangunan | Peralatan | Ya | 20 tahun | 1 tahun | dapat ditinjau ulang |
| Tanah | Peralatan | **Tidak** | – | – | |
| Olahraga | Peralatan | Ya | 2 tahun | 1 tahun | bila ada yang habis pakai, ubah jenisnya per barang |
| Kitab & Buku | Peralatan | **Tidak** | – | – | |
| ATK | **Perlengkapan** | – | – | – | habis pakai |

---

## 7. Hal yang Perlu Dikonfirmasi Client

**Tidak ada pertanyaan tersisa.** Semua jawaban client sudah dimasukkan ke §0.

Tabel usulan umur pakai (akhir §6 Tahap 7) dipakai **sementara** sesuai persetujuan client. Bila kelak ada yang tidak cocok (mis. Bangunan, Olahraga, Kitab & Buku), admin cukup mengubahnya per barang di Master Barang (jenis, saklar Disusutkan, umur pakai, interval) tanpa perubahan program.

### Asumsi kami (cukup dikabari bila keliru)
- Penyusutan **berhenti / barang tidak dihitung** di total bila statusnya non-aktif, hilang, atau rusak berat.
- Barang **hibah (gratis)** tidak dihitung memakai anggaran.
- Bila pembelian melebihi sisa anggaran, sistem hanya **memberi peringatan**, tidak menolak.
- Pengadaan lama **tidak** otomatis dikaitkan ke anggaran; bisa diatur belakangan.
- Nilai sisa (residu) barang **Rp 0** di akhir umur pakai.
- Mengubah saklar **Disusutkan** berlaku **retroaktif**: nilai dihitung dari tanggal pembelian, bukan mulai tanggal saklar diubah.
- Saklar Disusutkan ada di dua tingkat: **Master Barang** (default untuk semua pembelian barang itu) dan **per aset** di Pengadaan (penimpa opsional, kosong = ikut Master Barang).
- Umur pakai (dalam tahun) harus **kelipatan interval** (mis. umur 4 tahun boleh interval 1, 2, atau 4 tahun; tidak boleh 3 tahun).
- Saat memakai perlengkapan, sistem otomatis mengambil dari **pembelian yang paling lama** lebih dulu (FIFO).

### Di luar scope plan ini (sengaja ditunda)
- **Riwayat perawatan/maintenance** (tanggal, jenis, biaya, jadwal) → plan terpisah `plans/04-perawatan-aset.md`.
- Opname/stock-take perlengkapan, batas stok minimum & notifikasi, jurnal akuntansi.
