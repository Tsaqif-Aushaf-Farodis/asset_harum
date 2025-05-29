<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stubs Path
    |--------------------------------------------------------------------------
    |
    | The stubs path directory to generate crud. You may configure your
    | stubs paths here, allowing you to customize the own stubs of the
    | model,controller or view. Or, you may simply stick with the CrudGenerator defaults!
    |
    | Example: 'stub_path' => resource_path('stubs/')
    | Default: "default"
    | Files:
    |       Controller.stub
    |       Model.stub
    |       Request.stub
    |       views/
    |           bootstrap/
    |               create.stub
    |               edit.stub
    |               form.stub
    |               form-field.stub
    |               index.stub
    |               show.stub
    |               view-field.stub
    */

    'stub_path' => resource_path('stubs/crud'),

    /*
    |--------------------------------------------------------------------------
    | Application Layout
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application layout. This value is used when creating
    | views for crud. Default will be the "layouts.app".
    |
    */

    'layout' => 'layouts.app',

    'model' => [
        'namespace' => 'App\Models',
    ],

    'controller' => [
        'namespace' => 'App\Http\Controllers',
        'apiNamespace' => 'App\Http\Controllers\Api',
    ],

    'view' => [
        /*
         * Do not make these columns in views
         */
        'excludedColumns' => [
            '{pk}',
            'password',
            'remember_token',
            'email_verified_at',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'deleted_at',
            'deleted_by'
        ]
    ],

    'resources' => [
        'namespace' => 'App\Http\Resources',
    ],

    'livewire' => [
        'namespace' => 'App\Livewire',
    ],

    'request' => [
        'namespace' => 'App\Http\Requests',

        /*
         * Do not make these columns in request
         */
        'excludedColumns' => [
            '{pk}',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
            'deleted_at',
            'deleted_by'
        ]
    ],
];
