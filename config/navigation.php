<?php

$nav = [
    "General" => [
        [
            "title" => "Barang",
            "icon" => '<i class="menu-icon tf-icons bx bx-package"></i>',
            'route' => 'master-barang.index',
            'permissions' => ['master-barang view']
        ],
        // [
        //     "title" => "Layanan",
        //     "icon" => '<i class="menu-icon tf-icons bx bx-grid-alt"></i>',
        //     'route' => 'layanan.index',
        //     'permissions' => ['layanan view']
        // ],       
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
                //master satuan
                [
                    'title' => 'Master Satuan',
                    'route' => 'master-satuan.index',
                    'permissions' => ['master-satuan view']
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
