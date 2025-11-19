<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DummyController extends Controller
{
    public function formPermohonan(): View
    {
        return view('dummy.form-permohonan');
    }
    
    public function riwayatPermohonan(): View
    {
        return view('dummy.riwayat-permohonan');
    }
    
    public function generateOpname(): View
    {
        return view('dummy.generate-opname');
    }
    
    public function opname(): View
    {
        return view('dummy.opname');
    }
    
    public function formPeminjaman(): View
    {
        return view('dummy.form-peminjaman');
    }
    
    public function riwayatPeminjaman(): View
    {
        return view('dummy.riwayat-peminjaman');
    }
    
    public function formPengembalian(): View
    {
        return view('dummy.form-pengembalian');
    }
    
    public function riwayatPengembalian(): View
    {
        return view('dummy.riwayat-pengembalian');
    }
    
    public function laporanPengembalian(): View
    {
        return view('dummy.laporan-pengembalian');
    }
}
