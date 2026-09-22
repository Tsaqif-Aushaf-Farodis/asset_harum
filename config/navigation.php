<?php

$nav = [
    "General" => [
        [
            "title" => "Barang",
            "icon" => '<i class="menu-icon tf-icons bx bx-package"></i>',
            'route' => 'master-barang.index',
            'permissions' => ['master-barang view']
        ],
        [
            "title" => "Peralatan",
            "icon" => '<i class="menu-icon tf-icons bx bx-devices"></i>',
            "submenus" => [
                [
                    'title' => 'Aset & Nilai Buku',
                    'route' => 'peralatan.index',
                    'permissions' => ['pengadaan-barang view']
                ],
                [
                    'title' => 'Perlu Perawatan',
                    'route' => 'peralatan.perawatan',
                    'permissions' => ['pengadaan-barang view']
                ],
            ]
        ],
        [
            "title" => "Perlengkapan",
            "icon" => '<i class="menu-icon tf-icons bx bx-basket"></i>',
            "submenus" => [
                [
                    'title' => 'Stok Perlengkapan',
                    'route' => 'stok-perlengkapan.index',
                    'permissions' => ['perlengkapan view']
                ],
                [
                    'title' => 'Pemakaian & Riwayat',
                    'route' => 'pemakaian-perlengkapan.index',
                    'permissions' => ['pemakaian view']
                ],
            ]
        ],
        [
            "title" => "Anggaran",
            "icon" => '<i class="menu-icon tf-icons bx bx-wallet"></i>',
            "submenus" => [
                [
                    'title' => 'Anggaran Tahunan',
                    'route' => 'anggaran.index',
                    'permissions' => ['anggaran view']
                ],
                [
                    'title' => 'Realisasi Anggaran',
                    'route' => 'laporan.realisasi-anggaran',
                    'permissions' => ['laporan view']
                ],
            ]
        ],
        [
            "title" => "Permohonan",
            "icon" => '<i class="menu-icon tf-icons bx bx-envelope"></i>',
            "route" => "permohonan.index",
            "permissions" => ["permohonan view"]
        ],  
        [
            "title" => "Inventaris",
            "icon" => '<i class="menu-icon tf-icons bx bx-cube"></i>',
            "submenus" => [
                [
                    'title' => 'Pengadaan Barang',
                    'route' => 'pengadaan-barang.index',
                    'permissions' => ['pengadaan-barang view']
                ],
                [
                    'title' => 'Tanah',
                    'route' => 'tanah.index',
                    'permissions' => ['tanah view']
                ],
                 [
                    'title' => 'Bangunan',
                    'route' => 'bangunan.index',
                    'permissions' => ['bangunan view']
                ],
                 [
                    'title' => 'Kendaraan',
                    'route' => 'kendaraan.index',
                    'permissions' => ['kendaraan view']
                ],
            ]
        ],     
        [
            "title" => "Mutasi Aset",
            "icon" => '<i class="menu-icon tf-icons bx bx-transfer"></i>',
            'route' => 'mutasi-aset.index',
            'permissions' => ['mutasi-aset view']
        ],
        [
            "title" => "Opname",
            "icon" => '<i class="menu-icon tf-icons bx bx-check-circle"></i>',
            'route' => 'opname.index',
            'permissions' => ['opname view']
        ],  
        [
            "title" => "Peminjaman",
            "icon" => '<i class="menu-icon tf-icons bx bx-share"></i>',
            "submenus" => [
                [
                    'title' => 'Daftar Peminjaman',
                    'route' => 'peminjaman.index',
                    'permissions' => ['peminjaman view']
                ],
                [
                    'title' => 'Riwayat Peminjaman',
                    'route' => 'peminjaman.riwayat',
                    'permissions' => ['peminjaman view']
                ],
            ]
        ],  
        [
            "title" => "Laporan",
            "icon" => '<i class="menu-icon tf-icons bx bx-file"></i>',
            "submenus" => [
                [
                    'title' => 'Laporan Inventaris',
                    'route' => 'laporan.inventaris',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Mutasi',
                    'route' => 'laporan.mutasi',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Opname',
                    'route' => 'laporan.opname',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Peminjaman',
                    'route' => 'laporan.peminjaman',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Nilai Aset',
                    'route' => 'laporan.nilai-aset',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Penyusutan',
                    'route' => 'laporan.penyusutan',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Stok Perlengkapan',
                    'route' => 'laporan.stok-perlengkapan',
                    'permissions' => ['laporan view']
                ],
                [
                    'title' => 'Laporan Pemakaian',
                    'route' => 'laporan.pemakaian-perlengkapan',
                    'permissions' => ['laporan view']
                ],
            ]
        ],         
    ],
    "Misc" => [
         [
            "title" => "Master Setting",
            "icon" => '<i class="menu-icon tf-icons bx bx-briefcase"></i>',
            "submenus" => [
                [
                    'title' => 'Master Instansi',
                    'route' => 'instansi.index',
                    'permissions' => ['instansi view']
                ],
                //kategori barang
                [
                    'title' => 'Kategori Barang',
                    'route' => 'kategori-barang.index',
                    'permissions' => ['kategori-barang view']
                ],
                //master lokasi
                [
                    'title' => 'Master Lokasi',
                    'route' => 'master-lokasi.index',
                    'permissions' => ['master-lokasi view']
                ],
                //sub lokasi
                [
                    'title' => 'Master Sub Lokasi',
                    'route' => 'master-sub-lokasi.index',
                    'permissions' => ['master-sub-lokasi view']
                ],
                //master satuan
                [
                    'title' => 'Master Satuan',
                    'route' => 'master-satuan.index',
                    'permissions' => ['master-satuan view']
                ],
                //master status
                [
                    'title' => 'Master Status',
                    'route' => 'master-status.index',
                    'permissions' => ['master-status view']
                ],
            ],
        ],
        [
            "title" => "Manajemen Users",
            "icon" => '<i class="menu-icon tf-icons bx bx-lock-open-alt"></i>',
            "submenus" => [
                [
                    'title' => 'Users',
                    'route' => 'users.index',
                    'permissions' => ['user view']
                ],
                [
                    'title' => 'Roles',
                    'route' => 'roles.index',
                    'permissions' => ['role & permission view']
                ],
            ],
        ],
    ]
];

return $nav;
