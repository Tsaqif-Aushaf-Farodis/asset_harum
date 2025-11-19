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
            "title" => "Permohonan",
            "icon" => '<i class="menu-icon tf-icons bx bx-barcode"></i>',
            "submenus" => [
                [
                    'title' => 'Form Permohonan',
                    'route' => 'permohonan.index',
                    'permissions' => null
                ],
                [
                    'title' => 'Riwayat Permohonan',
                    'route' => null,
                    'permissions' => null
                ],
            ]
        ],  
        [
            "title" => "Inventaris",
            "icon" => '<i class="menu-icon tf-icons bx bx-barcode"></i>',
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
                [
                    'title' => 'Mutasi',
                    'route' => 'pengadaan-barang.index',
                    'permissions' => ['pengadaan-barang view']
                ],
                [
                    'title' => 'Non Aktif',
                    'route' => 'pengadaan-barang.index',
                    'permissions' => ['pengadaan-barang view']
                ],
                [
                    'title' => 'Hapus Barang',
                    'route' => 'pengadaan-barang.index',
                    'permissions' => ['pengadaan-barang view']
                ],
            ]
        ],     
           [
            "title" => "Opname",
            "icon" => '<i class="menu-icon tf-icons bx bx-barcode"></i>',
            "submenus" => [
                [
                    'title' => 'Generate Opname',
                    'route' => null,
                    'permissions' => null
                ],
                [
                    'title' => 'Opname',
                    'route' => null,
                    'permissions' => null
                ],
            ]
        ],  
         [
            "title" => "Peminjaman",
            "icon" => '<i class="menu-icon tf-icons bx bx-barcode"></i>',
            "submenus" => [
                [
                    'title' => 'Form Peminjaman',
                    'route' => null,
                    'permissions' => null
                ],
                [
                    'title' => 'Riwayat Peminjaman',
                    'route' => null,
                    'permissions' => null
                ],
            ]
        ],  
        [
            "title" => "Pengembalian",
            "icon" => '<i class="menu-icon tf-icons bx bx-barcode"></i>',
            "submenus" => [
                [
                    'title' => 'Form Pengembalian',
                    'route' => null,
                    'permissions' => null
                ],
                [
                    'title' => 'Riwayat Pengembalian',
                    'route' => null,
                    'permissions' => null
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
