<?php

namespace App\Helpers;

class Format
{
    public static function rupiah($angka, $prefix = 'Rp ')
    {
        if (!is_numeric($angka)) {
            return $prefix . '0';
        }

        return $prefix . number_format($angka, 0, ',', '.');
    }

    public static function tglIndo($date, $format = 'd F Y')
    {
        if ($date == null) {
            return '-';
        }

        return \Carbon\Carbon::parse($date)->translatedFormat($format);
    }
}