<?php

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'role_pivot_key' => null, //default 'role_id',
        'permission_pivot_key' => null, //default 'permission_id',

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the teams feature and your related model's
         * foreign key is other than `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true, the method for checking permissions will be registered on the gate.
     * Set this to false if you want to implement custom logic for checking permissions.
     */

    'register_permission_check_method' => true,

    /*
     * When set to true, Laravel\Octane\Events\OperationTerminated event listener will be registered
     * this will refresh permissions on every TickTerminated, TaskTerminated and RequestTerminated
     * NOTE: This should not be needed in most cases, but an Octane/Vapor combination benefited from it.
     */
    'register_octane_reset_listener' => false,

    /*
     * Teams Feature.
     * When set to true the package implements teams using the 'team_foreign_key'.
     * If you want the migrations to register the 'team_foreign_key', you must
     * set this to true before doing the migration.
     * If you already did the migration then you must make a new migration to also
     * add 'team_foreign_key' to 'roles', 'model_has_roles', and 'model_has_permissions'
     * (view the latest version of this package's migration file)
     */

    'teams' => false,

    /*
     * Passport Client Credentials Grant
     * When set to true the package will use Passports Client to check permissions
     */

    'use_passport_client_credentials' => false,

    /*
     * When set to true, the required permission names are added to exception messages.
     * This could be considered an information leak in some contexts, so the default
     * setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to exception messages.
     * This could be considered an information leak in some contexts, so the default
     * setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     * See documentation to understand supported syntax.
     */

    'enable_wildcard_permission' => false,

    /*
     * The class to use for interpreting wildcard permissions.
     * If you need to modify delimiters, override the class and specify its name here.
     */
    // 'permission.wildcard_permission' => Spatie\Permission\WildcardPermission::class,

    /* Cache-specific settings */

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],

    'permissions' => [
        [
            'group' => 'users',
            'pengguna' => ['admin'],
            'access' => [
                'user change-password',
                'user profile',
                'user view',
                'user create',
                'user edit',
                'user delete',
            ]
        ],
        [
            'group' => 'roles & permissions',
            'pengguna' => ['admin'],
            'access' => [
                'role & permission view',
                'role & permission create',
                'role & permission edit',
                'role & permission delete',
            ]
        ],
        [
            'group' => 'instansi',
            'pengguna' => ['admin'],
            'access' => [
                'instansi view',
                'instansi create',
                'instansi edit',
                'instansi delete',
            ]
        ],
        //kategori barang
        [
            'group' => 'kategori barang',
            'pengguna' => ['admin'],
            'access' => [
                'kategori-barang view',
                'kategori-barang create',
                'kategori-barang edit',
                'kategori-barang delete',
            ]
        ],
        //master lokasi
        [
            'group' => 'master lokasi',
            'pengguna' => ['admin'],
            'access' => [
                'master-lokasi view',
                'master-lokasi create',
                'master-lokasi edit',
                'master-lokasi delete',
            ]
        ],
        //master satuan
        [
            'group' => 'master satuan',
            'pengguna' => ['admin'],
            'access' => [
                'master-satuan view',
                'master-satuan create',
                'master-satuan edit',
                'master-satuan delete',
            ]
        ],
        //master barang
        [
            'group' => 'master barang',
            'pengguna' => ['admin'],
            'access' => [
                'master-barang view',
                'master-barang create',
                'master-barang edit',
                'master-barang delete',
            ]
        ],
        //master status
        [
            'group' => 'master status',
            'pengguna' => ['admin'],
            'access' => [
                'master-status view',
                'master-status create',
                'master-status edit',
                'master-status delete',
            ]
        ],
        //pengadaan barang
        [
            'group' => 'pengadaan barang',
            'pengguna' => ['admin'],
            'access' => [
                'pengadaan-barang view',
                'pengadaan-barang create',
                'pengadaan-barang edit',
                'pengadaan-barang delete',
                'pengadaan-barang import',
            ]
        ],
        //master sub lokasi
        [
            'group' => 'master sub lokasi',
            'pengguna' => ['admin'],
            'access' => [
                'master-sub-lokasi view',
                'master-sub-lokasi create',
                'master-sub-lokasi edit',
                'master-sub-lokasi delete',
            ]
        ],
        //tanah
        [
            'group' => 'tanah',
            'pengguna' => ['admin'],
            'access' => [
                'tanah view',
                'tanah create',
                'tanah edit',
                'tanah delete',
            ]
        ],
        //bangunan
        [
            'group' => 'bangunan',
            'pengguna' => ['admin'],
            'access' => [
                'bangunan view',
                'bangunan create',
                'bangunan edit',
                'bangunan delete',
            ]
        ],
        //kendaraan
        [
            'group' => 'kendaraan',
            'pengguna' => ['admin'],
            'access' => [
                'kendaraan view',
                'kendaraan create',
                'kendaraan edit',
                'kendaraan delete',
            ]
        ],
        //mutasi aset
        [
            'group' => 'mutasi aset',
            'pengguna' => ['admin'],
            'access' => [
                'mutasi-aset view',
                'mutasi-aset list',
                'mutasi-aset create',
                'mutasi-aset edit',
                'mutasi-aset delete',
                'mutasi-aset approve',
            ]
        ],
        //opname
        [
            'group' => 'opname',
            'pengguna' => ['admin'],
            'access' => [
                'opname view',
                'opname list',
                'opname create',
                'opname edit',
                'opname delete',
                'opname complete',
            ]
        ],
        //peminjaman
        [
            'group' => 'peminjaman',
            'pengguna' => ['admin'],
            'access' => [
                'peminjaman view',
                'peminjaman list',
                'peminjaman create',
                'peminjaman edit',
                'peminjaman delete',
                'peminjaman approve',
                'peminjaman return',
            ]
        ],
        //laporan
        [
            'group' => 'laporan',
            'pengguna' => ['admin'],
            'access' => [
                'laporan view',
                'laporan export',
            ]
        ],
        //permohonan
        [
            'group' => 'permohonan',
            'pengguna' => ['admin'],
            'access' => [
                'permohonan view',
                'permohonan create',
                'permohonan edit',
                'permohonan delete',
                'permohonan approve',
            ]
        ],
        
    ]
];
