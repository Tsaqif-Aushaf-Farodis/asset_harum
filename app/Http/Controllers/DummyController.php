<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DummyController extends Controller
{
    public function formPermohonan()
    {
        return view('dummy.form-permohonan');
    }

    public function riwayatPermohonan()
    {
        return view('dummy.riwayat-permohonan');
    }

    public function generateOpname()
    {
        return view('dummy.generate-opname');
    }

    public function opname()
    {
        return view('dummy.opname');
    }

    public function formPeminjaman()
    {
        return view('dummy.form-peminjaman');
    }

    public function riwayatPeminjaman()
    {
        return view('dummy.riwayat-peminjaman');
    }

    public function formPengembalian()
    {
        return view('dummy.form-pengembalian');
    }

    public function riwayatPengembalian()
    {
        return view('dummy.riwayat-pengembalian');
    }

    public function laporanPengembalian()
    {
        return view('dummy.laporan-pengembalian');
    }
}

