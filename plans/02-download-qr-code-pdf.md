# Plan: Download QR Code (PDF) — Pengadaan Barang

## Goal

On the **Pengadaan Barang** index page, add a way to download printable QR-code labels as **PDF**:

1. **Single download** — a new action button in the "Aksi" column of each row that downloads a **one-label PDF** for that item only.
2. **Bulk download** — a new button in the card header that opens a **modal** listing all items with checkboxes; the user ticks the items they want and downloads a **single PDF containing one label per selected item**.

Label layout (per the reference photo, simplified per the user's own description — QR replaces the logo corner, no separate logo needed):

```
┌─────────────┬───────────────────────────┐
│             │        HAK MILIK PPDA      │  ← bold header bar
│   [ QR ]    ├───────────────────────────┤
│             │        (Nama Barang)       │
│             │        (Kode Inventaris)   │
└─────────────┴───────────────────────────┘
```
Left column = QR code. Right column = "Hak Milik PPDA" / nama barang / kode inventaris.

---

## Phase 0 — Discovery (verified against the codebase)

### Stack relevant to this feature
- QR generation: **`simplesoftwareio/simple-qrcode` ^4.2**, facade `SimpleSoftwareIO\QrCode\Facades\QrCode`. Already used inline (SVG) in the index table: [index-table.blade.php:25](../resources/views/pengadaan-barang/includes/index-table.blade.php#L25) — `QrCode::size(100)->generate($row?->kode_inventaris)`. The QR **content convention** is `kode_inventaris` (also used identically in Tanah/Bangunan/Kendaraan `generateQrCode()` methods) — reuse this, do not invent a new payload.
- **No PDF library exists in this project today.** All "download a generated file" features use `maatwebsite/excel`'s `Excel::download(new SomeExport, $filename)` (e.g. [PengadaanBarangController::downloadTemplate](../app/Http/Controllers/PengadaanBarangController.php#L293)-area, [LaporanController::exportInventaris](../app/Http/Controllers/LaporanController.php#L151-L155)). We need to add **`barryvdh/laravel-dompdf`** (compatible with `laravel/framework ^11.31`, `php ^8.2` per [composer.json](../composer.json)).
- There is an **existing dead method** [`PengadaanBarangController::generateQrCode()`](../app/Http/Controllers/PengadaanBarangController.php#L286-L291) — no route, no view (`pengadaan-barang.qr-code` view doesn't exist). We will **replace/repurpose** this rather than leave it orphaned.
- Reference single-QR display page (good print-style template to draw layout ideas from, but not to be reused as-is since we need a PDF, not an HTML print page): [tanah/qr-code.blade.php](../resources/views/tanah/qr-code.blade.php).
- Aksi column / button-group markup to mirror: [index-table.blade.php:38-69](../resources/views/pengadaan-barang/includes/index-table.blade.php#L38-L69) (`btn-group`, `btn btn-icon btn-outline-* btn-sm`, `data-bs-toggle="tooltip"`).
- Card-header button placement to mirror ("Tambah Aset" / "Import" buttons): [index.blade.php:75-89](../resources/views/pengadaan-barang/index.blade.php#L75-L89).
- **No existing bulk row-selection UI** (checkboxes + shared modal) anywhere in the project — this is net-new. Bootstrap 5 JS (modal, dropdown) is already loaded globally via `components/layout/app.blade.php`, so a native Bootstrap modal needs no extra library. jQuery is also available if needed for the checkbox-filter script.
- Routes: registered inside `Route::middleware('auth')->group(...)` in [routes/web.php](../routes/web.php). Per-item asset QR routes already follow the pattern `GET /{resource}/{id}/qr-code` → name `{resource}.qr-code` (lines 60-63). Custom pengadaan-barang routes with extra path segments (`import/...`) are declared **before** `Route::resource('pengadaan-barang', ...)` at [web.php:50](../routes/web.php#L50) only because those specific paths (`pengadaan-barang/import`) are a *single extra segment* that could collide with the resource's `show` route pattern `pengadaan-barang/{pengadaan_barang}`. Our new routes use **two extra segments** (`/qr-code/download`, `/qr-code/download-bulk`) or a different HTTP verb, so they **do not collide** regardless of placement — we'll add them next to the other "QR Code routes" block (lines 60-63) for readability, no reordering needed.

### Permission
Reuse the existing **`pengadaan-barang view`** ability (already covers `index`/`show`) for both new download actions — downloading a QR label is just another way of viewing an item the user can already see. No new permission/seeder change needed. *(Flag: confirm with user if they want a separate, stricter permission — default assumption here is "view" is sufficient.)*

### Open technical risk to verify early (Phase 1, first thing)
`simplesoftwareio/simple-qrcode`'s `format('png')` (needed to embed the QR as an `<img>` in the dompdf template — dompdf's SVG support is too limited to trust for BaconQrCode's SVG output) **requires the PHP `imagick` extension**. Before writing any Blade/controller code:
```
php artisan tinker
>>> SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(150)->generate('TEST-123');
```
- If it returns binary PNG data → proceed as planned (base64-embed as `<img src="data:image/png;base64,...">`).
- If it throws (Imagick not installed) → either enable the `imagick` PHP extension in the environment, or fall back to generating the QR with `format('eps')`→unsupported, so the practical fallback is: install `imagick`. (Do **not** try to render the raw SVG inside dompdf — BaconQrCode's SVG uses `<path>` fill rules dompdf frequently mis-renders as solid black boxes.)

---

## Phase 1 — Add PDF dependency & verify QR PNG output

**What to implement**
1. `composer require barryvdh/laravel-dompdf` (adds to [composer.json](../composer.json) `require`).
2. Run the tinker check above; confirm `imagick` is enabled (`php -m | grep imagick`). Install/enable if missing — this blocks everything else.
3. Publish config if needed: `php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"` (optional, only if paper size/orientation defaults need changing later).

**Verification checklist**
- [ ] `composer.json` / `composer.lock` include `barryvdh/laravel-dompdf`.
- [ ] Tinker call above returns binary PNG data (starts with PNG magic bytes), not an exception.

---

## Phase 2 — PDF label template + shared PDF-building logic

**Files to add/change**

1. **New** `resources/views/pengadaan-barang/qr-code-pdf.blade.php`
   - Plain HTML (no `<x-layout.app>` — this is rendered by dompdf, not the browser), minimal inline `<style>` (dompdf has weak external-CSS/Bootstrap support, so keep it self-contained).
   - Loops over a passed-in `$items` collection (used for **both** single and bulk — single download just passes a 1-item collection, so there is only one template to maintain).
   - Per item, render the label box:
     ```blade
     @foreach($items as $item)
       <div class="label">
         <table class="label-table">
           <tr>
             <td class="qr-cell"><img src="data:image/png;base64,{{ $item->qr_base64 }}" width="110" height="110"></td>
             <td class="text-cell">
               <div class="ppda-bar">HAK MILIK PPDA</div>
               <div class="nama-barang">{{ $item->barang->nama_barang ?? '-' }}</div>
               <div class="kode-inventaris">{{ $item->kode_inventaris }}</div>
             </td>
           </tr>
         </table>
       </div>
     @endforeach
     ```
   - CSS: fixed label box (e.g. `width:100%; max-width:480px; border:1px solid #000; margin-bottom:12px; page-break-inside:avoid;`), `.ppda-bar{background:#1b3a5c;color:#fff;font-weight:bold;padding:4px 8px;}`, `.nama-barang{font-weight:bold;}`. Let dompdf paginate naturally (do **not** force a page-break per label — several labels should stack per A4 page, matching how these are actually printed/cut as stickers).

2. **Modify** `app/Http/Controllers/PengadaanBarangController.php`:
   - Add `use Barryvdh\DomPDF\Facade\Pdf;` near the other `use` statements (around [line 26](../app/Http/Controllers/PengadaanBarangController.php#L26)).
   - **Replace** the dead `generateQrCode()` method ([lines 286-291](../app/Http/Controllers/PengadaanBarangController.php#L286-L291)) with:
     - A private helper `buildQrCodePdf(\Illuminate\Support\Collection $items)`:
       ```php
       private function buildQrCodePdf($items)
       {
           $items->loadMissing('barang');
           foreach ($items as $item) {
               $item->qr_base64 = base64_encode(
                   QrCode::format('png')->size(300)->margin(1)->generate($item->kode_inventaris)
               );
           }
           return Pdf::loadView('pengadaan-barang.qr-code-pdf', compact('items'))->setPaper('a4');
       }
       ```
       (Generate at higher pixel size than the display size — 300px source → 110px in the label — so the printed QR stays crisp.)
     - `downloadQrCode(PengadaanBarang $pengadaanBarang)` (single item):
       ```php
       public function downloadQrCode(PengadaanBarang $pengadaanBarang)
       {
           $items = collect([$pengadaanBarang]);
           return $this->buildQrCodePdf($items)->download('qr-code-' . $pengadaanBarang->kode_inventaris . '.pdf');
       }
       ```
     - `downloadQrCodeBulk(Request $request)` (multi item):
       ```php
       public function downloadQrCodeBulk(Request $request)
       {
           $request->validate([
               'ids' => 'required|array|min:1',
               'ids.*' => 'exists:pengadaan_barang,id',
           ]);
           $items = PengadaanBarang::whereIn('id', $request->ids)->get();
           return $this->buildQrCodePdf($items)->download('qr-code-pengadaan-barang-' . now()->format('Ymd-His') . '.pdf');
       }
       ```
   - Extend the `pengadaan-barang view` middleware entry ([line 35](../app/Http/Controllers/PengadaanBarangController.php#L35)) to also cover the two new methods:
     ```php
     new Middleware('permission:pengadaan-barang view', only: ['index', 'show', 'downloadQrCode', 'downloadQrCodeBulk']),
     ```

3. **Modify** `routes/web.php` — add next to the existing "QR Code routes" block ([lines 60-63](../routes/web.php#L60-L63)):
   ```php
   Route::get('/pengadaan-barang/{pengadaan_barang}/qr-code/download', [PengadaanBarangController::class, 'downloadQrCode'])->name('pengadaan-barang.qr-code.download');
   Route::post('/pengadaan-barang/qr-code/download-bulk', [PengadaanBarangController::class, 'downloadQrCodeBulk'])->name('pengadaan-barang.qr-code.download-bulk');
   ```

**Verification checklist**
- [ ] `php artisan route:list | grep qr-code` shows both new routes.
- [ ] Visiting `pengadaan-barang/{id}/qr-code/download` while logged in as a user with `pengadaan-barang view` downloads a 1-label PDF; QR scans back to the correct `kode_inventaris`.
- [ ] A user without `pengadaan-barang view` gets 403.
- [ ] POSTing `ids[]` with 3 valid IDs to the bulk route downloads a 3-label PDF, one label per item, all labels legible (not cut off across page breaks in a way that splits a single label).

**Anti-pattern guards**
- Don't regenerate the QR at display resolution (110px) — generate at a higher resolution (300px) so print quality holds; only the `<img width/height>` should be small.
- Don't try to make dompdf render Bootstrap classes/cards — this template is fully self-contained inline CSS.
- Don't force one label per PDF page — let natural flow stack several per sheet.

---

## Phase 3 — Single-item download button (Aksi column)

**File to change:** `resources/views/pengadaan-barang/includes/index-table.blade.php`

Add a new icon button inside the existing `btn-group` ([lines 39-69](../resources/views/pengadaan-barang/includes/index-table.blade.php#L39-L69)), gated the same way as "Detail" (`@can('pengadaan-barang view')`), placed first (before Detail) since it's the most directly related to the QR column already shown in that row:

```blade
@can('pengadaan-barang view')
<div class="me-1">
    <a href="{{ route('pengadaan-barang.qr-code.download', $row) }}"
        class="btn btn-icon btn-outline-secondary btn-sm" data-bs-toggle="tooltip"
        data-bs-title="Download QR Code" data-bs-placement="top">
        <span class="bx bx-qr-scan"></span>
    </a>
</div>
@endcan
```
(A plain `<a href>` GET link is enough — the browser will trigger a file download since the controller response is `->download()`; no confirm step needed since this isn't destructive.)

**Verification checklist**
- [ ] Button appears in every row's Aksi column, tooltip shows "Download QR Code".
- [ ] Clicking it downloads a single-label PDF for exactly that row's item, no full-page navigation/reload artifacts.

---

## Phase 4 — Bulk selection modal + card-header button

**Files to change**

1. **`app/Http/Controllers/PengadaanBarangController.php` → `index()`**
   Add one extra lightweight query (separate from the paginated `$pengadaanBarang`) to feed the modal's checkbox list, and pass it to the view:
   ```php
   $allPengadaanBarangForQr = PengadaanBarang::with('barang')
       ->orderBy('kode_inventaris')
       ->get(['id', 'barang_id', 'kode_inventaris']);
   ```
   Add `'allPengadaanBarangForQr'` to the `compact(...)` / `view(...)->with(...)` call at the end of `index()` (both the normal-response and the HTMX-partial-response branches — the modal partial is only in the full view, so it's only strictly needed on the non-HTMX branch, but pass it either way for simplicity).
   > **Trade-off flagged for later**: this loads *all* pengadaan_barang rows (id/kode/nama only, no pagination) into the modal on every index-page load. Fine for the current data volume; if the table grows into the thousands, revisit with a searchable AJAX/htmx-paginated list instead of a full client-side list.

2. **New** `resources/views/pengadaan-barang/includes/qr-select-modal.blade.php`
   A single Bootstrap 5 modal (not per-row — one shared modal), containing:
   - A plain `<input>` search box that filters the visible `<li>`/`<tr>` rows client-side via a small inline `<script>` (no server round-trip — matches the "no extra library" constraint since jQuery/Bootstrap JS is already loaded).
   - A "select all" checkbox that toggles all visible (post-filter) checkboxes.
   - A scrollable list (`max-height: 400px; overflow-y:auto;`) of checkboxes, one per `$allPengadaanBarangForQr` row, label = `kode_inventaris — nama_barang`.
   - A `<form method="POST" action="{{ route('pengadaan-barang.qr-code.download-bulk') }}">` wrapping the whole list (`@csrf`), so checked boxes post as `ids[]`; submit button = "Download PDF" (`type="submit"`, disabled via JS until at least one box is checked, mirroring a lightweight version of other forms' validation feel in this app).

   ```blade
   <div class="modal fade" id="qrSelectModal" tabindex="-1">
     <div class="modal-dialog modal-lg">
       <div class="modal-content">
         <form method="POST" action="{{ route('pengadaan-barang.qr-code.download-bulk') }}" id="qrSelectForm">
           @csrf
           <div class="modal-header">
             <h5 class="modal-title">Pilih Barang untuk Download QR Code</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
           </div>
           <div class="modal-body">
             <input type="text" id="qrSearchInput" class="form-control mb-2" placeholder="Cari kode inventaris atau nama barang...">
             <div class="form-check mb-2 border-bottom pb-2">
               <input class="form-check-input" type="checkbox" id="qrSelectAll">
               <label class="form-check-label" for="qrSelectAll">Pilih Semua</label>
             </div>
             <div style="max-height:400px; overflow-y:auto;">
               @foreach($allPengadaanBarangForQr as $item)
               <div class="form-check qr-item-row">
                 <input class="form-check-input qr-item-checkbox" type="checkbox" name="ids[]" value="{{ $item->id }}" id="qrItem{{ $item->id }}"
                     data-search="{{ strtolower($item->kode_inventaris . ' ' . ($item->barang->nama_barang ?? '')) }}">
                 <label class="form-check-label" for="qrItem{{ $item->id }}">
                   {{ $item->kode_inventaris }} — {{ $item->barang->nama_barang ?? '-' }}
                 </label>
               </div>
               @endforeach
             </div>
           </div>
           <div class="modal-footer">
             <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
             <button type="submit" class="btn btn-primary" id="qrSelectSubmit" disabled>
               <i class="bx bx-download me-1"></i>Download PDF
             </button>
           </div>
         </form>
       </div>
     </div>
   </div>

   @push('script')
   <script>
     document.addEventListener('DOMContentLoaded', function () {
       const search = document.getElementById('qrSearchInput');
       const selectAll = document.getElementById('qrSelectAll');
       const submitBtn = document.getElementById('qrSelectSubmit');
       const rows = document.querySelectorAll('.qr-item-row');

       function updateSubmitState() {
         submitBtn.disabled = document.querySelectorAll('.qr-item-checkbox:checked').length === 0;
       }

       search?.addEventListener('input', function () {
         const term = this.value.toLowerCase();
         rows.forEach(row => {
           const cb = row.querySelector('.qr-item-checkbox');
           row.style.display = cb.dataset.search.includes(term) ? '' : 'none';
         });
       });

       selectAll?.addEventListener('change', function () {
         rows.forEach(row => {
           if (row.style.display !== 'none') {
             row.querySelector('.qr-item-checkbox').checked = selectAll.checked;
           }
         });
         updateSubmitState();
       });

       document.querySelectorAll('.qr-item-checkbox').forEach(cb => cb.addEventListener('change', updateSubmitState));
     });
   </script>
   @endpush
   ```
   *(Uses `@push('script')` — confirm the layout has a matching `@stack('script')`; `x-input.confirm-button` already relies on `@pushOnce('script')` in this same view tree, so the stack exists — see [confirm-button.blade.php:29](../resources/views/components/input/confirm-button.blade.php#L29).)*

3. **Modify** `resources/views/pengadaan-barang/index.blade.php`:
   - Add a button next to "Import" ([around line 89](../resources/views/pengadaan-barang/index.blade.php#L83-L89)):
     ```blade
     @can('pengadaan-barang view')
         <div class="col-auto">
             <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#qrSelectModal">
                 <i class="bx bx-qr-scan me-1"></i>Download QR Code
             </button>
         </div>
     @endcan
     ```
   - Include the modal partial once, near the end of the file (after the closing `</div>` of the card, before `</x-layout.app>`):
     ```blade
     @include('pengadaan-barang.includes.qr-select-modal', compact('allPengadaanBarangForQr'))
     ```

**Verification checklist**
- [ ] "Download QR Code" button opens the modal without a page reload.
- [ ] Typing in the search box narrows the checkbox list live; unrelated rows hide.
- [ ] "Pilih Semua" checks only the currently-visible (filtered) rows.
- [ ] Submit button is disabled with 0 boxes checked, enabled as soon as ≥1 is checked.
- [ ] Submitting with 5 checked items downloads a 5-label PDF; canceling the modal does nothing.
- [ ] Modal + button are hidden for a user without `pengadaan-barang view`.

**Anti-pattern guards**
- Don't build a second htmx/AJAX endpoint for the modal list in this pass — client-side filtering over the already-loaded list is enough for the current scale (see trade-off note in Phase 4.1).
- Don't scope the bulk PDF query to the index page's current filters — the modal's checkboxes are the single source of truth for which IDs get included, independent of whatever `lokasi_id`/`search`/etc. filters happen to be active on the underlying table.

---

## Phase 5 — Final wiring & end-to-end verification

- [ ] `php artisan route:list | grep pengadaan-barang` shows `qr-code.download` (GET, `{pengadaan_barang}`) and `qr-code.download-bulk` (POST) alongside the existing resource + import routes.
- [ ] Full manual pass: open Pengadaan Barang index → click single-row QR download → get correct PDF → open Download QR Code modal → search, select 2–3 items across different pages/filters of the underlying table → download → verify PDF has exactly those items, each label showing "HAK MILIK PPDA" / correct nama barang / correct kode inventaris, and each QR scans to the right `kode_inventaris`.
- [ ] Confirm no regression on the existing HTMX filter form (`pengadaan-barang-table` div swap) — the new modal button/markup live outside that swapped div, so a filter refresh must not remove the modal from the DOM.

---

## Open questions (confirm before/while implementing)
1. **Permission scope** — plan assumes reusing `pengadaan-barang view` for both download actions (no new permission). Confirm this is acceptable, or if a dedicated ability is wanted.
2. **Imagick availability** — must be verified in the actual deployment environment in Phase 1 before proceeding; if unavailable, installing it is a server-level change outside this codebase.
3. **Label size / page layout** — plan stacks labels one-per-row down an A4 page (simple, robust). If the real intent is a **grid of labels sized for adhesive label sheets** (e.g. fixed N-per-row matching a specific label sheet product), that needs exact label dimensions from the user and is a follow-up refinement to the Phase 2 CSS, not a blocker for v1.
