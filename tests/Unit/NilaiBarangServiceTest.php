<?php

use App\Services\NilaiBarangService as S;
use Carbon\Carbon;

function susut(float $nilai, string $beli, ?int $masaBulan, int $interval, bool $on, string $asOf): array
{
    return S::penyusutan($nilai, Carbon::parse($beli), $masaBulan, $interval, $on, Carbon::parse($asOf));
}

it('menghitung tahun penuh sejak tanggal perolehan', function () {
    expect(S::tahunPenuh(Carbon::parse('2026-03-15'), Carbon::parse('2026-03-14')))->toBe(0)
        ->and(S::tahunPenuh(Carbon::parse('2026-03-15'), Carbon::parse('2027-03-14')))->toBe(0)
        ->and(S::tahunPenuh(Carbon::parse('2026-03-15'), Carbon::parse('2027-03-15')))->toBe(1)
        ->and(S::tahunPenuh(Carbon::parse('2026-03-15'), Carbon::parse('2030-03-15')))->toBe(4)
        ->and(S::tahunPenuh(Carbon::parse('2026-03-15'), Carbon::parse('2025-01-01')))->toBe(0);
});

it('menganggap 29 Februari berulang tahun pada 1 Maret di tahun non-kabisat', function () {
    expect(S::tahunPenuh(Carbon::parse('2024-02-29'), Carbon::parse('2025-02-28')))->toBe(0)
        ->and(S::tahunPenuh(Carbon::parse('2024-02-29'), Carbon::parse('2025-03-01')))->toBe(1)
        ->and(S::tahunPenuh(Carbon::parse('2024-02-29'), Carbon::parse('2028-02-29')))->toBe(4);
});

it('menyusutkan per 1 tahun (laptop 12 juta, 4 tahun)', function () {
    $beli = '2026-03-15';
    $masa = 48;

    expect(susut(12_000_000, $beli, $masa, 1, true, '2026-03-15')['nilai_buku'])->toBe(12_000_000.0)
        ->and(susut(12_000_000, $beli, $masa, 1, true, '2027-03-14')['nilai_buku'])->toBe(12_000_000.0)
        ->and(susut(12_000_000, $beli, $masa, 1, true, '2027-03-15')['nilai_buku'])->toBe(9_000_000.0)
        ->and(susut(12_000_000, $beli, $masa, 1, true, '2028-03-15')['nilai_buku'])->toBe(6_000_000.0)
        ->and(susut(12_000_000, $beli, $masa, 1, true, '2029-03-15')['nilai_buku'])->toBe(3_000_000.0)
        ->and(susut(12_000_000, $beli, $masa, 1, true, '2030-03-15')['nilai_buku'])->toBe(0.0);
});

it('menyusutkan per 2 tahun (turun 6 juta tiap 2 tahun)', function () {
    $beli = '2026-03-15';

    expect(susut(12_000_000, $beli, 48, 2, true, '2028-03-14')['nilai_buku'])->toBe(12_000_000.0)
        ->and(susut(12_000_000, $beli, 48, 2, true, '2028-03-15')['nilai_buku'])->toBe(6_000_000.0)
        ->and(susut(12_000_000, $beli, 48, 2, true, '2030-03-14')['nilai_buku'])->toBe(6_000_000.0)
        ->and(susut(12_000_000, $beli, 48, 2, true, '2030-03-15')['nilai_buku'])->toBe(0.0);
});

it('tidak menurunkan nilai setelah masa habis dan tidak pernah negatif', function () {
    $r = susut(12_000_000, '2020-01-01', 48, 1, true, '2040-01-01');

    expect($r['nilai_buku'])->toBe(0.0)
        ->and($r['akumulasi'])->toBe(12_000_000.0)
        ->and($r['tanggal_penyusutan_berikutnya'])->toBeNull();
});

it('menutup sisa pembulatan pada langkah terakhir', function () {
    // 10.000.000 / 3 langkah = 3.333.333,33 → langkah terakhir harus tepat 0
    expect(susut(10_000_000, '2020-01-01', 36, 1, true, '2023-01-01')['nilai_buku'])->toBe(0.0);
});

it('nilai buku = nilai perolehan bila tidak disusutkan atau masa belum diatur', function () {
    expect(susut(5_000_000, '2020-01-01', 60, 1, false, '2030-01-01'))
        ->toMatchArray(['disusutkan' => false, 'nilai_buku' => 5_000_000.0, 'akumulasi' => 0.0]);

    expect(susut(5_000_000, '2020-01-01', null, 1, true, '2030-01-01')['nilai_buku'])->toBe(5_000_000.0);
});

it('memberi tanggal penyusutan berikutnya', function () {
    $r = susut(12_000_000, '2026-03-15', 48, 2, true, '2027-01-01');

    expect($r['tanggal_penyusutan_berikutnya']->toDateString())->toBe('2028-03-15');
});

it('menghitung nilai persediaan perlengkapan', function () {
    expect(S::nilaiPersediaan(10, 2_500))->toBe(25_000.0)
        ->and(S::nilaiPersediaan(0, 2_500))->toBe(0.0)
        ->and(S::nilaiPersediaan(-3, 2_500))->toBe(0.0);
});

it('memvalidasi pengaturan Peralatan yang disusutkan', function () {
    $ok = S::validasiPengaturan('peralatan', true, 4, 2);
    expect($ok['errors'])->toBeEmpty()
        ->and($ok['data'])->toBe(['disusutkan' => true, 'masa_pemakaian_bulan' => 48, 'interval_penyusutan_tahun' => 2]);

    expect(S::validasiPengaturan('peralatan', true, null, 1)['errors'])->toHaveKey('masa_pemakaian');
    expect(S::validasiPengaturan('peralatan', true, 1, 1)['errors'])->toHaveKey('masa_pemakaian');
    expect(S::validasiPengaturan('peralatan', true, 4, 3)['errors'])->toHaveKey('interval_penyusutan_tahun');
    expect(S::validasiPengaturan('peralatan', true, 5, 0)['errors'])->toHaveKey('interval_penyusutan_tahun');
});

it('mengizinkan Peralatan tidak disusutkan tanpa masa', function () {
    $r = S::validasiPengaturan('peralatan', false, null, 1);

    expect($r['errors'])->toBeEmpty()
        ->and($r['data']['disusutkan'])->toBeFalse()
        ->and($r['data']['masa_pemakaian_bulan'])->toBeNull();
});

it('memvalidasi Perlengkapan: masa 1-12 bulan, tidak pernah disusutkan', function () {
    $ok = S::validasiPengaturan('perlengkapan', true, 6, 3);
    expect($ok['errors'])->toBeEmpty()
        ->and($ok['data'])->toBe(['disusutkan' => false, 'masa_pemakaian_bulan' => 6, 'interval_penyusutan_tahun' => 1]);

    expect(S::validasiPengaturan('perlengkapan', false, 13, 1)['errors'])->toHaveKey('masa_pemakaian');
    expect(S::validasiPengaturan('perlengkapan', false, null, 1)['errors'])->toBeEmpty();
});
