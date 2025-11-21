<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Instansi
        DB::table('instansi')->insert([
            [
                'nama_instansi' => 'Pondok Pesantren Darul Arqam Garut',
                'alamat' => 'Garut, Jawa Barat',
                'telepon' => '0262-000000',
                'email' => 'info@darularqamgarut.sch.id',
                'logo' => null,
                'deskripsi' => 'Pondok Pesantren Darul Arqam Garut dengan unit pendidikan MTs dan MA Putra-Putri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // Master Lokasi - Unit Sekolah
        $lokasi = [
            ['kode_lokasi' => 'MTSPA', 'nama_lokasi' => 'MTs Putra'],
            ['kode_lokasi' => 'MTSPI', 'nama_lokasi' => 'MTs Putri'],
            ['kode_lokasi' => 'MAPA', 'nama_lokasi' => 'MA Putra'],
            ['kode_lokasi' => 'MAPI', 'nama_lokasi' => 'MA Putri'],
            ['kode_lokasi' => 'KSTR', 'nama_lokasi' => 'Kesekretariatan'],
            ['kode_lokasi' => 'KNGN', 'nama_lokasi' => 'Keuangan'],
            ['kode_lokasi' => 'PDDK', 'nama_lokasi' => 'Pendidikan'],
            ['kode_lokasi' => 'PGSH', 'nama_lokasi' => 'Pengasuhan'],
            ['kode_lokasi' => 'SRPRS', 'nama_lokasi' => 'Sarana dan Prasarana'],
            ['kode_lokasi' => 'KRTGG', 'nama_lokasi' => 'Kerumahtanggaan'],
        ];

        foreach ($lokasi as $item) {
            DB::table('master_lokasi')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Master Sub Lokasi
        $subLokasi = [
            // MTs Putra
            ['lokasi_id' => 1, 'kode_sub_lokasi' => 'KLS7A', 'nama_sub_lokasi' => 'Kelas 7A'],
            ['lokasi_id' => 1, 'kode_sub_lokasi' => 'KLS8A', 'nama_sub_lokasi' => 'Kelas 8A'],
            ['lokasi_id' => 1, 'kode_sub_lokasi' => 'KLS9A', 'nama_sub_lokasi' => 'Kelas 9A'],
            ['lokasi_id' => 1, 'kode_sub_lokasi' => 'KKGR', 'nama_sub_lokasi' => 'Kantor Guru MTs Putra'],
            
            // MTs Putri
            ['lokasi_id' => 2, 'kode_sub_lokasi' => 'KLS7B', 'nama_sub_lokasi' => 'Kelas 7B'],
            ['lokasi_id' => 2, 'kode_sub_lokasi' => 'KLS8B', 'nama_sub_lokasi' => 'Kelas 8B'],
            ['lokasi_id' => 2, 'kode_sub_lokasi' => 'KLS9B', 'nama_sub_lokasi' => 'Kelas 9B'],
            ['lokasi_id' => 2, 'kode_sub_lokasi' => 'KKGRI', 'nama_sub_lokasi' => 'Kantor Guru MTs Putri'],
            
            // MA Putra
            ['lokasi_id' => 3, 'kode_sub_lokasi' => 'KLS10A', 'nama_sub_lokasi' => 'Kelas 10 IPA'],
            ['lokasi_id' => 3, 'kode_sub_lokasi' => 'KLS11A', 'nama_sub_lokasi' => 'Kelas 11 IPA'],
            ['lokasi_id' => 3, 'kode_sub_lokasi' => 'KLS12A', 'nama_sub_lokasi' => 'Kelas 12 IPA'],
            ['lokasi_id' => 3, 'kode_sub_lokasi' => 'KGRMA', 'nama_sub_lokasi' => 'Kantor Guru MA Putra'],
            
            // MA Putri
            ['lokasi_id' => 4, 'kode_sub_lokasi' => 'KLS10B', 'nama_sub_lokasi' => 'Kelas 10 IPS'],
            ['lokasi_id' => 4, 'kode_sub_lokasi' => 'KLS11B', 'nama_sub_lokasi' => 'Kelas 11 IPS'],
            ['lokasi_id' => 4, 'kode_sub_lokasi' => 'KLS12B', 'nama_sub_lokasi' => 'Kelas 12 IPS'],
            ['lokasi_id' => 4, 'kode_sub_lokasi' => 'KGMAI', 'nama_sub_lokasi' => 'Kantor Guru MA Putri'],
            
            // Kesekretariatan
            ['lokasi_id' => 5, 'kode_sub_lokasi' => 'SEKR1', 'nama_sub_lokasi' => 'Ruang Sekretaris'],
            ['lokasi_id' => 5, 'kode_sub_lokasi' => 'SEKR2', 'nama_sub_lokasi' => 'Ruang Administrasi'],
            ['lokasi_id' => 5, 'kode_sub_lokasi' => 'ARSIP', 'nama_sub_lokasi' => 'Ruang Arsip'],
            
            // Keuangan
            ['lokasi_id' => 6, 'kode_sub_lokasi' => 'KAS', 'nama_sub_lokasi' => 'Ruang Kas'],
            ['lokasi_id' => 6, 'kode_sub_lokasi' => 'AKNT', 'nama_sub_lokasi' => 'Ruang Akuntansi'],
            
            // Pendidikan
            ['lokasi_id' => 7, 'kode_sub_lokasi' => 'KURIK', 'nama_sub_lokasi' => 'Bidang Kurikulum'],
            ['lokasi_id' => 7, 'kode_sub_lokasi' => 'PERPUS', 'nama_sub_lokasi' => 'Perpustakaan'],
            ['lokasi_id' => 7, 'kode_sub_lokasi' => 'LAB', 'nama_sub_lokasi' => 'Laboratorium'],
            
            // Pengasuhan
            ['lokasi_id' => 8, 'kode_sub_lokasi' => 'ASRPA', 'nama_sub_lokasi' => 'Asrama Putra'],
            ['lokasi_id' => 8, 'kode_sub_lokasi' => 'ASRPI', 'nama_sub_lokasi' => 'Asrama Putri'],
            ['lokasi_id' => 8, 'kode_sub_lokasi' => 'MUSLA', 'nama_sub_lokasi' => 'Mushola'],
            
            // Sarana dan Prasarana
            ['lokasi_id' => 9, 'kode_sub_lokasi' => 'GDNG1', 'nama_sub_lokasi' => 'Gudang Alat'],
            ['lokasi_id' => 9, 'kode_sub_lokasi' => 'WRKS', 'nama_sub_lokasi' => 'Workshop'],
            
            // Kerumahtanggaan
            ['lokasi_id' => 10, 'kode_sub_lokasi' => 'DAPUR', 'nama_sub_lokasi' => 'Dapur'],
            ['lokasi_id' => 10, 'kode_sub_lokasi' => 'LAUND', 'nama_sub_lokasi' => 'Laundry'],
        ];

        foreach ($subLokasi as $item) {
            DB::table('master_sub_lokasi')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Kategori Barang
        $kategori = [
            ['kode_kategori_barang' => 'ELK', 'nama_kategori_barang' => 'Elektronik', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'FRN', 'nama_kategori_barang' => 'Furniture', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'VHC', 'nama_kategori_barang' => 'Kendaraan', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'BLD', 'nama_kategori_barang' => 'Bangunan', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'LND', 'nama_kategori_barang' => 'Tanah', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'ATK', 'nama_kategori_barang' => 'Alat Tulis Kantor', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'KMP', 'nama_kategori_barang' => 'Komputer & IT', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'OLR', 'nama_kategori_barang' => 'Olahraga', 'status_kategori_barang' => 'aktif'],
            ['kode_kategori_barang' => 'KITAB', 'nama_kategori_barang' => 'Kitab & Buku', 'status_kategori_barang' => 'aktif'],
        ];

        foreach ($kategori as $item) {
            DB::table('kategori_barang')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Master Satuan
        $satuan = [
            ['kode_satuan' => 'PCS', 'nama_satuan' => 'Piece'],
            ['kode_satuan' => 'SET', 'nama_satuan' => 'Set'],
            ['kode_satuan' => 'UNIT', 'nama_satuan' => 'Unit'],
            ['kode_satuan' => 'M2', 'nama_satuan' => 'Meter Persegi'],
            ['kode_satuan' => 'M3', 'nama_satuan' => 'Meter Kubik'],
            ['kode_satuan' => 'KG', 'nama_satuan' => 'Kilogram'],
            ['kode_satuan' => 'LITER', 'nama_satuan' => 'Liter'],
        ];

        foreach ($satuan as $item) {
            DB::table('master_satuan')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Master Status
        $status = [
            ['kode_status' => 'BAIK', 'nama_status' => 'Baik'],
            ['kode_status' => 'RSKRG', 'nama_status' => 'Rusak Ringan'],
            ['kode_status' => 'RSKBR', 'nama_status' => 'Rusak Berat'],
            ['kode_status' => 'HILNG', 'nama_status' => 'Hilang'],
        ];

        foreach ($status as $item) {
            DB::table('master_status')->insert(array_merge($item, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        // Master Barang
        $barang = [
            ['kode_barang' => 'KMP001', 'nama_barang' => 'Laptop Dell Latitude', 'kategori_barang_id' => 7, 'merk_barang' => 'Dell'],
            ['kode_barang' => 'KMP002', 'nama_barang' => 'Komputer Desktop HP', 'kategori_barang_id' => 7, 'merk_barang' => 'HP'],
            ['kode_barang' => 'KMP003', 'nama_barang' => 'Printer Canon', 'kategori_barang_id' => 7, 'merk_barang' => 'Canon'],
            ['kode_barang' => 'ELK001', 'nama_barang' => 'AC Split 1 PK', 'kategori_barang_id' => 1, 'merk_barang' => 'Panasonic'],
            ['kode_barang' => 'ELK002', 'nama_barang' => 'LED TV 32 Inch', 'kategori_barang_id' => 1, 'merk_barang' => 'Samsung'],
            ['kode_barang' => 'FRN001', 'nama_barang' => 'Meja Kantor', 'kategori_barang_id' => 2, 'merk_barang' => 'Olympic'],
            ['kode_barang' => 'FRN002', 'nama_barang' => 'Kursi Kantor', 'kategori_barang_id' => 2, 'merk_barang' => 'Futura'],
            ['kode_barang' => 'FRN003', 'nama_barang' => 'Lemari Arsip', 'kategori_barang_id' => 2, 'merk_barang' => 'Brother'],
            ['kode_barang' => 'VHC001', 'nama_barang' => 'Mobil Sedan', 'kategori_barang_id' => 3, 'merk_barang' => 'Toyota'],
            ['kode_barang' => 'VHC002', 'nama_barang' => 'Motor Bebek', 'kategori_barang_id' => 3, 'merk_barang' => 'Honda'],
            ['kode_barang' => 'OLR001', 'nama_barang' => 'Bola Futsal', 'kategori_barang_id' => 8, 'merk_barang' => 'Mikasa'],
            ['kode_barang' => 'OLR002', 'nama_barang' => 'Matras Olahraga', 'kategori_barang_id' => 8, 'merk_barang' => 'Kettler'],
            ['kode_barang' => 'KITAB001', 'nama_barang' => 'Kitab Kuning', 'kategori_barang_id' => 9, 'merk_barang' => null],
            ['kode_barang' => 'KITAB002', 'nama_barang' => 'Al-Quran', 'kategori_barang_id' => 9, 'merk_barang' => 'Syamil Quran'],
        ];

        foreach ($barang as $item) {
            DB::table('master_barang')->insert(array_merge($item, [
                'is_active' => true,
                'status_permohonan' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        $this->command->info('Master data seeded successfully!');
    }
}

