# Plan: Import Pengadaan Barang via Excel Template (with scoped template download + preview-before-save)

## Goal

Add an **Import** feature to the Pengadaan Barang module so an operator can:

1. Open a form, pick a **Lokasi** + **Sub Lokasi**, and **download an `.xlsx` template** already scoped to that location.
2. The template has a **main entry sheet** (rows to fill = future `pengadaan_barang` / inventaris records) plus a **second "Referensi" sheet** listing valid master data (barang, satuan, kondisi) and fill-in instructions. The barang/satuan/kondisi columns on the main sheet use **dropdowns** sourced from the Referensi sheet.
3. Upload the filled template and see a **validated preview table** (per-row OK / error) **before** anything is written to the database.
4. Confirm to **save all valid rows** transactionally — each becomes a `pengadaan_barang` row with an auto-generated `kode_inventaris`, exactly like the existing single-create flow.

## Confirmed decisions (from kickoff)
1. **Barang may be created on the fly.** If a row's `kode_barang` is **not** an existing `approved()` MasterBarang, the import creates a new `master_barang` (needs `nama_barang` + kategori on the row), set `status_permohonan='approved'`, `is_active=true`, `created_by`. Within one batch a new `kode_barang` is created **once** and reused for later rows with the same code.
2. **Preview persistence = temp file + uuid** in `storage/app/imports/{uuid}.xlsx`.
3. **`sumber` = dropdown of fixed values** (list TBD — see Open questions; placeholder used in design: `APBD, APBN, Hibah, Pembelian, Bantuan, Lainnya`).

---

## Phase 0 — Documentation Discovery (DONE; verified against the codebase)

### Stack (from [composer.json](../composer.json))
- Laravel `^11.31`, Livewire `^3.6`, **maatwebsite/excel `^3.1`**, spatie/laravel-permission `^6.10`, simple-qrcode, pharaonic/livewire-select2.
- Front end: Bootstrap 5 + Boxicons (`bx ...`), Blade `x-layout.app` / `x-breadcrumb` components.

### Allowed APIs — maatwebsite/excel 3.1 concerns (use ONLY these; all exist in 3.1)
**Export side:**
- `Maatwebsite\Excel\Concerns\WithMultipleSheets` → `public function sheets(): array` — one workbook, multiple sheet classes. *(needed for main + Referensi sheets)*
- `FromArray` (`array()`), `FromCollection` (`collection()`) — sheet body source.
- `WithHeadings` (`headings(): array`), `WithTitle` (`title(): string`) — already used in [LaporanInventarisExport.php](../app/Exports/LaporanInventarisExport.php).
- `WithColumnWidths` (`columnWidths(): array`), `WithStyles` (`styles(Worksheet $sheet)`) — header styling/column sizing.
- `WithEvents` + `Maatwebsite\Excel\Events\AfterSheet` — to attach **PhpSpreadsheet `DataValidation`** dropdowns that point at the Referensi sheet ranges. This is the documented way to add list dropdowns.

**Import side:**
- `Maatwebsite\Excel\Concerns\ToCollection` (`collection(Collection $rows)`) **+** `WithHeadingRow` — read filled rows as keyed arrays. *(preferred for preview: we control validation, not `ToModel`)*
- `WithStartRow` / `WithHeadingRow` — skip instruction rows.
- `Excel::download(new Export, 'file.xlsx')` and `Excel::toCollection(new Import, $file)` / `Excel::import(...)` from `Maatwebsite\Excel\Facades\Excel` (already imported in [LaporanController.php:13](../app/Http/Controllers/LaporanController.php#L13)).

**Anti-patterns / do NOT do:**
- Do **not** use `ToModel` for the import path — it auto-persists row-by-row and defeats "preview before save."
- Do **not** invent a `WithDropdownColumn`/`WithValidation`-for-dropdown concern; dropdowns come from `WithEvents`+`DataValidation` only.
- Do **not** call `$query->where('kategori_id', ...)` on `PengadaanBarang` — there is **no** `kategori_id` column (kategori is reached via the `barang` relation). The legacy [LaporanInventarisExport.php:29](../app/Exports/LaporanInventarisExport.php#L29) does this and it silently does nothing; do not copy that filter.

### Verified data model (migrations + models)
`pengadaan_barang` columns ([create migration](../database/migrations/2025_05_31_120000_create_pengadaan_barang_table.php), [add-fields migration](../database/migrations/2025_11_22_035743_add_fields_to_pengadaan_barang_table.php)):
`kode_inventaris`(unique) · `barang_id`→`master_barang` · `lokasi_id`→`master_sub_lokasi` · `sumber`(string100) · `status` enum(`baru`,`bekas`,`hibah`) · `status_id`→`master_status` · `tanggal_pengadaan`(date) · `jumlah`(int) · `satuan_id`→`master_satuan` · `harga_satuan`(decimal 15,2) · `total_harga`(decimal 15,2) · `keterangan`(nullable) · `permohonan_id`/`detail_permohonan_id`(nullable) · `is_active` · `is_borrowed` · `current_user` · `created_by` · `updated_by`.

Reference models & exact column names:
- `MasterBarang` ([model](../app/Models/MasterBarang.php)): `kode_barang`, `nama_barang`, `merk_barang`, `tipe_barang`, `tahun_barang`, `deskripsi_barang`, `kategori_barang_id`→`KategoriBarang`, `status_permohonan`, `is_active`. Scope `approved()` = `status_permohonan='approved' AND is_active=1`.
  - **Create-barang reference** ([MasterBarangController::store](../app/Http/Controllers/MasterBarangController.php#L65-L89)): required = `kode_barang`,`nama_barang`,`kategori_barang_id`; optional = `merk_barang`,`tipe_barang`,`tahun_barang`,`deskripsi_barang`; sets `created_by`. ⚠️ It does **not** set `status_permohonan`, so a barang created exactly like this would **fail `approved()`** and be unusable in pengadaan. For import-created barang we must explicitly set `status_permohonan='approved'` + `is_active=true` so the new barang is immediately usable in the same import batch.
- `KategoriBarang` ([model](../app/Models/KategoriBarang.php)): `kode_kategori_barang`, `nama_kategori_barang`.
- `MasterSatuan`: `kode_satuan`, `nama_satuan`, `is_active`.
- `MasterStatus`: `kode_status`, `nama_status` (route key = `kode_status`).
- `MasterLokasi`: `kode_lokasi`, `nama_lokasi`. `MasterSubLokasi`: `lokasi_id`, `kode_sub_lokasi`, `nama_sub_lokasi`, `belongsTo lokasi`.

### Verified `kode_inventaris` generation (COPY this exactly from [PengadaanBarangController::store](../app/Http/Controllers/PengadaanBarangController.php#L150-L170))
Format: `KODE_BARANG-KODE_KATEGORI-KODE_SUBLOKASI-MM-YYYY-URUT(4 digits)` where the running counter is
`PengadaanBarang::whereMonth('tanggal_pengadaan',$m)->whereYear(...,$y)->count() + 1`.
⚠️ During a batch import the counter must **increment per inserted row inside the loop**, not be recomputed from `count()` each time (otherwise all rows in the same month collide). See Phase 3.

### Conventions to match
- Controller permission middleware via `HasMiddleware::middleware()` returning `new Middleware('permission:pengadaan-barang <action>', only: [...])` — see [controller top](../app/Http/Controllers/PengadaanBarangController.php#L24-L32).
- Routes registered in [routes/web.php](../routes/web.php) inside the `auth` group; non-resource actions added as explicit `Route::get/post(...)->name('pengadaan-barang.<x>')` near the resource line (mirror the Laporan export routes [web.php:87-90](../routes/web.php#L87-L90)).
- Flash messages: `->with('success'| 'error', ...)`; transactional writes wrapped in `DB::beginTransaction()/commit()/rollback()` (see store).
- Blade: `@can('pengadaan-barang create')`, Bootstrap cards, Boxicons. Buttons live in the index card-header next to "Tambah Aset" ([index.blade.php:75-81](../resources/views/pengadaan-barang/index.blade.php#L75-L81)).

### New permission needed
A new ability `pengadaan-barang import`. Confirm how abilities are seeded (search the permission seeder) and add it there + grant to the same roles that have `pengadaan-barang create`.

---

## Phase 1 — Scoped template download (multi-sheet export + form)

**What to implement**

1. **`app/Exports/TemplatePengadaanExport.php`** implementing `WithMultipleSheets`. Constructor takes `MasterSubLokasi $subLokasi` (resolved from the form). `sheets()` returns:
   - `new TemplateDataSheet($subLokasi)` — the entry sheet.
   - `new TemplateReferensiSheet()` — the reference + instructions sheet.

2. **`TemplateDataSheet`** (same file or `app/Exports/Sheets/`) implements `FromArray`, `WithHeadings`, `WithTitle`('Data Pengadaan'), `WithColumnWidths`, `WithStyles`, `WithEvents`.
   - Headings (these become the import heading-row keys). Because barang can be created on the fly, the sheet carries both "pick existing" and "define new" columns:
     `kode_barang`, `nama_barang`, `kode_kategori_barang`, `merk_barang`, `tipe_barang`, `tahun_barang`, `sumber`, `status`, `kondisi`, `tanggal_pengadaan`, `jumlah`, `kode_satuan`, `harga_satuan`, `keterangan`.
     - `nama_barang` / `kode_kategori_barang` are **only required when `kode_barang` is new** (not found among approved barang); for existing barang they may be left blank (a column note explains this).
   - Show **Lokasi / Sub Lokasi as read-only context** — render them in a frozen title area or as two info cells above the table (the actual `lokasi_id` is taken from the form on import, NOT from the sheet, so the user cannot tamper with it).
   - `array()` returns a few **blank example rows** (e.g. 3 empty rows) so the file isn't empty.
   - `registerEvents()` → on `AfterSheet`, add `DataValidation` (TYPE_LIST) dropdowns for rows 2..N on:
     - `kode_kategori_barang` → `=Referensi!` kategori range, `kode_satuan` → satuan range, `kondisi` → kondisi range (formulas point at Referensi sheet columns).
     - `sumber` → literal fixed list (e.g. `"APBD,APBN,Hibah,Pembelian,Bantuan,Lainnya"`); `status` → literal `"baru,bekas,hibah"`.
     - `kode_barang` is intentionally **free entry** (existing OR new) — optionally provide the approved-barang list as a non-restrictive hint, but do not lock it (TYPE_LIST `setShowDropDown(true)` with `setAllowBlank` and no hard error).
   - Style the heading row bold with a fill.

3. **`TemplateReferensiSheet`** implements `FromArray`, `WithHeadings`, `WithTitle`('Referensi'), `WithStyles`.
   - Column groups (each list in its own contiguous column range so dropdown formulas can reference it): `Kode Barang | Nama Barang` (existing approved barang) · `Kode Kategori | Nama Kategori` · `Kode Satuan | Nama Satuan` · `Kondisi (kode_status / nama_status)` · `Sumber (fixed list)`.
   - Body: `MasterBarang::approved()->with('kategori')->get()` (existing-barang hint); `KategoriBarang::all()` (for the `kode_kategori_barang` dropdown used when defining a NEW barang); `MasterSatuan::where('is_active',1)->get()`; `MasterStatus::all()`; the fixed `sumber` list.
   - Top rows = a short **"Cara Pengisian"** instruction block: lokasi/sublokasi is fixed by the form; to add an **existing** barang fill only `kode_barang`; to add a **new** barang fill `kode_barang` + `nama_barang` + `kode_kategori_barang` (and optionally merk/tipe/tahun); date format `YYYY-MM-DD` and `<= today`; `jumlah` ≥1 integer; `harga_satuan` numeric.

4. **Download form + controller action.**
   - Add `templateForm()` (GET, renders a small form: Lokasi `<select>` → Sub Lokasi `<select>` dependent dropdown) and `downloadTemplate(Request $request)` (validates `sub_lokasi_id` required|exists, returns `Excel::download(new TemplatePengadaanExport($subLokasi), 'template-pengadaan-...xlsx')`).
   - Lokasi→Sub Lokasi dependency: mirror the existing `lokasiList = MasterSubLokasi::with('lokasi')` usage; either two native selects with a tiny JS filter or reuse the existing tomselect/livewire-select2 pattern already used in forms.
   - Routes: `pengadaan-barang/import/template` (GET form) and `pengadaan-barang/import/template/download` (GET file), names `pengadaan-barang.import.template` / `.template.download`, under middleware `permission:pengadaan-barang import`.

**Documentation references to copy from**
- Export skeleton & download call: [LaporanInventarisExport.php](../app/Exports/LaporanInventarisExport.php) + [LaporanController::exportInventaris](../app/Http/Controllers/LaporanController.php#L151-L155).
- Reference-data queries: [PengadaanBarangController::create](../app/Http/Controllers/PengadaanBarangController.php#L111-L126) (shows `MasterBarang::approved()->pluck`, sublokasi label `lokasi->nama_lokasi . ' - ' . nama_sub_lokasi`).

**Verification checklist**
- [ ] `php artisan route:list | grep pengadaan-barang.import` shows the two routes.
- [ ] Visiting the form, picking lokasi+sublokasi, downloading yields an `.xlsx` that opens with **two tabs**: "Data Pengadaan" and "Referensi".
- [ ] Clicking a `kode_barang` cell shows a dropdown of approved barang; `status` shows baru/bekas/hibah.
- [ ] Referensi tab lists current approved barang, active satuan, and kondisi values.

**Anti-pattern guards**
- Don't bake `lokasi_id` into a visible editable cell — keep it server-side from the form.
- Don't query `MasterBarang::all()` — use `approved()` so the dropdown matches what `create` allows.

---

## Phase 2 — Upload + validated preview (no DB writes)

**What to implement**

1. **`app/Imports/PengadaanBarangImport.php`** implementing `ToCollection`, `WithHeadingRow`, `WithStartRow` (start after the instruction/heading rows so heading keys = the Phase-1 headings). It does **not** persist; `collection()` just returns/holds the raw keyed rows (or implement a plain parser method the controller calls via `Excel::toCollection`).

2. **`previewImport(Request $request)`** controller action:
   - Validate: `file` required|mimes:xlsx,xls; `sub_lokasi_id` required|exists:master_sub_lokasi,id (carried from the upload form).
   - Store the uploaded file to `storage/app/imports/{uuid}.xlsx` (so Phase 3 can re-read it without re-upload) and keep `{uuid}` + `sub_lokasi_id` in the session or as hidden fields.
   - `Excel::toCollection(new PengadaanBarangImport, $path)` → iterate rows; for each row build a **validation result** with a `barang_mode` of `existing` or `new`:
     - **Barang resolution:** if `kode_barang` matches an `approved()` MasterBarang → mode `existing`. If not found → mode `new`, and then require `nama_barang` non-empty **and** `kode_kategori_barang` resolves to a `KategoriBarang` (error listing whichever is missing). Treat a `kode_barang` that already exists but is **not approved** as an error (ambiguous — don't silently create a duplicate).
     - `kode_satuan` resolves to a MasterSatuan? `kondisi` resolves to a MasterStatus? `status` ∈ {baru,bekas,hibah}? `sumber` ∈ fixed list?
     - `tanggal_pengadaan` parseable date & `<= today`? `jumlah` integer ≥1? `harga_satuan` numeric ≥0?
     - skip fully-blank rows.
   - Compute `total_harga = jumlah * harga_satuan` for display.
   - Return a **preview view**: a table with a row-status badge (✓ valid / ✗ with reasons), a **"New barang" tag** on rows that will create a master_barang, counts (`X valid, Y error, Z new barang`), the resolved Lokasi/Sub Lokasi header, and a **"Simpan" (confirm) button** that's disabled if there are 0 valid rows. Carry the import `{uuid}` + `sub_lokasi_id` forward in the confirm form.

3. **Upload form** (can be the same screen as Phase 1's template form, second card "Upload Template Terisi"): file input + the sub_lokasi context, POST to `previewImport`.
   - Routes: `POST pengadaan-barang/import/preview` (name `.import.preview`), `permission:pengadaan-barang import`.

**Documentation references to copy from**
- Validation idioms (`status in:baru,bekas,hibah`, `tanggal ... before_or_equal:today`, `jumlah integer min:1`, `harga numeric min:0`): [PengadaanBarangController::store validate block](../app/Http/Controllers/PengadaanBarangController.php#L130-L143).
- Resolving barang/lokasi + building rows: same store method.
- Excel facade usage already imported in [LaporanController.php:13](../app/Http/Controllers/LaporanController.php#L13).

**Verification checklist**
- [ ] Uploading the unmodified blank template → preview shows "0 valid" and Simpan disabled (no DB change).
- [ ] Uploading a template with 2 good rows + 1 bad `kode_barang` → preview shows 2 valid, 1 error with a readable reason.
- [ ] No rows are written to `pengadaan_barang` after preview (confirm with a count query).
- [ ] The stored temp file exists under `storage/app/imports/`.

**Anti-pattern guards**
- No `save()`/`create()`/`ToModel` anywhere in this phase.
- Don't trust the sheet's lokasi text — always use the form's `sub_lokasi_id`.

---

## Phase 3 — Confirm & save (transactional batch insert)

**What to implement**

1. **`storeImport(Request $request)`** controller action:
   - Validate the carried `{uuid}` temp file exists + `sub_lokasi_id`.
   - Re-read rows with `Excel::toCollection(new PengadaanBarangImport, $tempPath)`; re-run the same validation (defense-in-depth) and **insert only valid rows**.
   - Wrap in `DB::beginTransaction()`. Maintain an in-memory `$barangCache` keyed by `kode_barang`. Inside the loop, for each valid row build the `pengadaan_barang` payload mirroring `store()`:
     - **Resolve/create barang:** if mode `existing` → use the found barang. If mode `new` → check `$barangCache` first; if absent, `MasterBarang::create([...])` with `kode_barang`, `nama_barang`, `kategori_barang_id` (from `kode_kategori_barang`), optional merk/tipe/tahun, **`status_permohonan='approved'`, `is_active=true`**, `created_by=Auth::id()`, then cache it. This dedupes repeated new codes within the batch (copy field set from [MasterBarangController::store](../app/Http/Controllers/MasterBarangController.php#L65-L89), plus the approved/active flags).
     - resolve `satuan_id` (from `kode_satuan`), `status_id` (from `kondisi`), `lokasi_id` = form sub_lokasi.
     - generate `kode_inventaris` using the **store() format** (`barang->kode_barang` + `barang->kategori->kode_kategori_barang` + `subLokasi->kode_sub_lokasi` + MM + YYYY + 4-digit urut), but maintain a **per-(month,year) running counter in PHP**: seed it once from `PengadaanBarang::whereMonth/whereYear(...)->count()` before the loop, then `++` for each inserted row so codes don't collide within the batch.
     - set `total_harga = jumlah*harga_satuan`, `is_active=true`, `is_borrowed=false`, `created_by=Auth::id()`.
   - `PengadaanBarang::create($payload)` per row (keeps fillable/casts behavior). `DB::commit()`. Delete the temp file. Redirect to `pengadaan-barang.index` with `success` = "N aset berhasil diimport".
   - On exception → `DB::rollback()`, keep temp file, redirect back with `error`.
   - Route: `POST pengadaan-barang/import/store` (name `.import.store`), `permission:pengadaan-barang import`.

**Documentation references to copy from**
- The entire kode-generation + create payload: [PengadaanBarangController::store](../app/Http/Controllers/PengadaanBarangController.php#L145-L179) — copy, then adapt the counter to increment in-loop.

**Verification checklist**
- [ ] Confirming a 3-valid-row preview inserts exactly 3 `pengadaan_barang` rows, each with a unique sequential `kode_inventaris` for that month.
- [ ] Forcing an exception mid-batch leaves **zero** rows (rollback works).
- [ ] Temp file is deleted after a successful import.
- [ ] New rows appear in [pengadaan-barang.index](../resources/views/pengadaan-barang/index.blade.php) and respect the lokasi filter.

**Anti-pattern guards**
- Do **not** recompute the counter from `count()` inside the loop (collision/duplicate-unique error).
- Do **not** skip the transaction — partial imports are worse than none.

---

## Phase 4 — Wiring, permission, and final verification

**What to implement**
1. Add the **"Import" button** to the index card-header next to "Tambah Aset", guarded by `@can('pengadaan-barang import')`, linking to the import/template screen ([index.blade.php:75-81](../resources/views/pengadaan-barang/index.blade.php#L75-L81)).
2. Add the `pengadaan-barang import` permission to the permission seeder and grant it to the roles holding `pengadaan-barang create`; re-run the seeder. (Locate seeder via search for `pengadaan-barang create`.)
3. Add the `import` ability to the controller's `middleware()` map (`only: ['templateForm','downloadTemplate','previewImport','storeImport']`).

**Final verification (prove the whole flow)**
- [ ] `grep -r "ToModel" app/Imports` → **no matches** (preview-before-save preserved).
- [ ] `grep -rn "kategori_id" app/Exports/Template* app/Imports` → no bogus `kategori_id` filters on PengadaanBarang.
- [ ] `php artisan route:list` shows: `import.template`, `import.template.download`, `import.preview`, `import.store` — all under `permission:pengadaan-barang import`.
- [ ] End-to-end: pick lokasi/sublokasi → download → fill 2 rows referencing real barang → upload → preview shows 2 valid → Simpan → 2 new inventaris rows with correct `kode_inventaris`.
- [ ] A user **without** `pengadaan-barang import` gets 403 on all four routes and does not see the button.
- [ ] (If a test suite is desired) a Pest feature test: fake an uploaded xlsx, assert preview counts, assert `storeImport` creates the expected `pengadaan_barang` count. Pest is already in dev deps.

---

## Open questions
1. ✅ RESOLVED — import **creates new master_barang on the fly** (Phases 1–3 updated; new barang set `status_permohonan='approved'`, `is_active=true`).
2. ✅ RESOLVED — **temp file + uuid** in `storage/app/imports/`.
3. ⏳ PENDING — **exact `sumber` dropdown values.** Design currently uses placeholder `APBD, APBN, Hibah, Pembelian, Bantuan, Lainnya`. Confirm the real list before building Phase 1's Referensi sheet. (Also: should new-barang creation require `master-barang create` permission in addition to `pengadaan-barang import`, or is `pengadaan-barang import` sufficient? Default assumption: `pengadaan-barang import` is sufficient.)

