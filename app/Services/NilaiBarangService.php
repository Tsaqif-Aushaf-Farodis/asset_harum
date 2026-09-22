<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * Perhitungan nilai barang. Semua method berupa fungsi murni (tanpa query)
 * agar mudah diuji.
 *
 * Penyusutan Peralatan: garis lurus, turun tiap N tahun pada "ulang tahun"
 * tanggal perolehan masing-masing aset.
 */
class NilaiBarangService
{
    public const JENIS_PERALATAN = 'peralatan';
    public const JENIS_PERLENGKAPAN = 'perlengkapan';

    /**
     * Jumlah tahun penuh yang sudah berlalu sejak $mulai sampai $asOf.
     * Tanggal 29 Februari dianggap "berulang tahun" pada 1 Maret di tahun non-kabisat.
     */
    public static function tahunPenuh(Carbon $mulai, Carbon $asOf): int
    {
        if ($asOf->lt($mulai)) {
            return 0;
        }

        $tahun = $asOf->year - $mulai->year;

        // Belum sampai "ulang tahun" perolehan pada tahun ini.
        if ($asOf->month < $mulai->month || ($asOf->month === $mulai->month && $asOf->day < $mulai->day)) {
            $tahun--;
        }

        return max(0, $tahun);
    }

    /**
     * Hitung penyusutan satu aset.
     *
     * @return array{
     *   disusutkan: bool,
     *   nilai_perolehan: float,
     *   akumulasi: float,
     *   nilai_buku: float,
     *   penyusutan_per_langkah: float,
     *   jumlah_langkah: int,
     *   langkah_berjalan: int,
     *   interval_tahun: int,
     *   tanggal_penyusutan_berikutnya: ?Carbon,
     * }
     */
    public static function penyusutan(
        float $nilaiPerolehan,
        Carbon $tanggalPerolehan,
        ?int $masaBulan,
        int $intervalTahun,
        bool $disusutkan,
        Carbon $asOf,
        float $residu = 0.0
    ): array {
        $nilaiPerolehan = round($nilaiPerolehan, 2);
        $intervalTahun = max(1, $intervalTahun);

        $hasil = [
            'disusutkan' => false,
            'nilai_perolehan' => $nilaiPerolehan,
            'akumulasi' => 0.0,
            'nilai_buku' => $nilaiPerolehan,
            'penyusutan_per_langkah' => 0.0,
            'jumlah_langkah' => 0,
            'langkah_berjalan' => 0,
            'interval_tahun' => $intervalTahun,
            'tanggal_penyusutan_berikutnya' => null,
        ];

        // Tidak disusutkan, atau masa belum diatur → nilai buku = nilai perolehan.
        if (!$disusutkan || !$masaBulan || $masaBulan < 1) {
            return $hasil;
        }

        $masaTahun = $masaBulan / 12;
        $jumlahLangkah = max(1, (int) ceil($masaTahun / $intervalTahun));
        $dasar = max(0.0, $nilaiPerolehan - $residu);
        $perLangkah = round($dasar / $jumlahLangkah, 2);

        $tahunPenuh = self::tahunPenuh($tanggalPerolehan->copy()->startOfDay(), $asOf->copy()->startOfDay());
        $langkah = min($jumlahLangkah, intdiv($tahunPenuh, $intervalTahun));

        // Langkah terakhir menutup sisa pembulatan supaya nilai buku tepat = residu.
        $akumulasi = $langkah >= $jumlahLangkah ? $dasar : round($perLangkah * $langkah, 2);

        $hasil['disusutkan'] = true;
        $hasil['akumulasi'] = $akumulasi;
        $hasil['nilai_buku'] = round(max($residu, $nilaiPerolehan - $akumulasi), 2);
        $hasil['penyusutan_per_langkah'] = $perLangkah;
        $hasil['jumlah_langkah'] = $jumlahLangkah;
        $hasil['langkah_berjalan'] = $langkah;

        if ($langkah < $jumlahLangkah) {
            $hasil['tanggal_penyusutan_berikutnya'] = $tanggalPerolehan->copy()->startOfDay()
                ->addYears(($langkah + 1) * $intervalTahun);
        }

        return $hasil;
    }

    /**
     * Nilai persediaan Perlengkapan yang belum dipakai. Barang yang sudah
     * dipakai bernilai 0 (menjadi "nilai terpakai").
     */
    public static function nilaiPersediaan(int $sisa, float $hargaSatuan): float
    {
        return round(max(0, $sisa) * $hargaSatuan, 2);
    }

    /**
     * Validasi & normalisasi pengaturan penyusutan Master Barang.
     *
     * Masukan `masa` bersatuan TAHUN untuk Peralatan dan BULAN untuk Perlengkapan.
     *
     * @return array{errors: array<string,string>, data: array{disusutkan: bool, masa_pemakaian_bulan: ?int, interval_penyusutan_tahun: int}}
     */
    public static function validasiPengaturan(string $jenis, bool $disusutkan, $masa, $interval): array
    {
        $errors = [];
        $masa = ($masa === null || $masa === '') ? null : $masa;
        $interval = ($interval === null || $interval === '') ? 1 : $interval;

        if ($masa !== null && (!is_numeric($masa) || (float) $masa <= 0)) {
            $errors['masa_pemakaian'] = 'Masa pemakaian harus berupa angka lebih dari 0.';
            $masa = null;
        }

        if (!is_numeric($interval) || (int) $interval < 1 || (int) $interval != $interval) {
            $errors['interval_penyusutan_tahun'] = 'Interval penyusutan harus berupa bilangan bulat minimal 1 tahun.';
            $interval = 1;
        }
        $interval = (int) $interval;

        if ($jenis === self::JENIS_PERLENGKAPAN) {
            // Perlengkapan tidak disusutkan; masa hanya informasi (1–12 bulan).
            $masaBulan = null;
            if ($masa !== null && !isset($errors['masa_pemakaian'])) {
                if ((float) $masa != (int) $masa || (int) $masa < 1 || (int) $masa > 12) {
                    $errors['masa_pemakaian'] = 'Masa pemakaian Perlengkapan harus 1–12 bulan (bilangan bulat).';
                } else {
                    $masaBulan = (int) $masa;
                }
            }

            return [
                'errors' => $errors,
                'data' => [
                    'disusutkan' => false,
                    'masa_pemakaian_bulan' => $masaBulan,
                    'interval_penyusutan_tahun' => 1,
                ],
            ];
        }

        // Peralatan: masa dalam TAHUN.
        $masaBulan = null;
        if ($masa !== null && !isset($errors['masa_pemakaian'])) {
            $bulan = (float) $masa * 12;
            if ($bulan != (int) $bulan) {
                $errors['masa_pemakaian'] = 'Masa pemakaian Peralatan diisi dalam tahun (boleh desimal kelipatan 1/12).';
            } elseif ($bulan <= 12) {
                $errors['masa_pemakaian'] = 'Masa pemakaian Peralatan harus lebih dari 1 tahun.';
            } else {
                $masaBulan = (int) $bulan;
            }
        }

        if ($disusutkan) {
            if ($masaBulan === null && !isset($errors['masa_pemakaian'])) {
                $errors['masa_pemakaian'] = 'Masa pemakaian wajib diisi bila barang disusutkan.';
            }
            if ($masaBulan !== null && !isset($errors['interval_penyusutan_tahun'])) {
                $masaTahun = $masaBulan / 12;
                if (fmod($masaTahun, $interval) != 0.0) {
                    $errors['interval_penyusutan_tahun'] = "Masa pemakaian ({$masaTahun} tahun) harus kelipatan interval ({$interval} tahun).";
                }
            }
        }

        return [
            'errors' => $errors,
            'data' => [
                'disusutkan' => $disusutkan,
                'masa_pemakaian_bulan' => $masaBulan,
                'interval_penyusutan_tahun' => $interval,
            ],
        ];
    }
}
