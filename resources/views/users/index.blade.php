<x-layout.app title="Users" activeMenu="users">
    <div class="container my-5">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="order-last col-12 col-md-8 order-md-1">
                        <h3>{{ __('User') }}</h3>
                        <p class="text-subtitle text-muted">
                            {{ __('Below is a list of all users.') }}
                        </p>
                    </div>
                    <x-breadcrumb>
                        <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('User') }}</li>
                    </x-breadcrumb>
                </div>
            </div>

            <section class="section">
                <div class="d-flex justify-content-end">
                    @can('user create')
                    <a href="{{ route('users.create') }}" class="mb-3 btn btn-primary me-1">
                        <i class="fas fa-plus"></i>
                        Tambah user
                    </a>
                    @endcan
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    @grid([
                                    'dataProvider' => $dataProvider, // see info about DataProviders
                                    'rowsPerPage' => $perPage,
                                    'columnOptions' => [
                                    'class' => 'attribute',
                                    'formatters' => ['text', 'raw'],
                                    ],
                                    'columns' => [
                                    [
                                    'class' => 'raw',
                                    'title' => 'No',
                                    'value' => function () use (&$i) {
                                    return ++$i . '.';
                                    },
                                    ],
                                    // [
                                    // 'class' => 'raw',
                                    // 'attribute' => 'avatar',
                                    // 'title' => 'Avatar',
                                    // 'value' => function ($row) {
                                    // return $row->avatar ?? '-';
                                    // },
                                    // ],
                                    'name',
                                    'username',
                                    // 'email',
                                    // 'role',
                                    [
                                    'class' => 'raw',
                                    'attribute' => 'role_id',
                                    'title' => 'Role',
                                    'filter' => [
                                    'name' => 'role_id',
                                    'class' => 'dropdown',
                                    'items' => $roles,
                                    ],
                                    'value' => function ($row) {
                                    return $row->getRoleNames()->toArray() !== [] ? $row->getRoleNames()[0] : '-';
                                    },
                                    ],
                                    // [
                                    // 'class' => 'raw',
                                    // 'attribute' => 'updated_at',
                                    // 'title' => 'Last Update',
                                    // 'formatters' => ['raw'],
                                    // 'value' => function ($row) {
                                    // return $row->updated_at;
                                    // // return $row->updated_at->format('d-m-Y H:i:s');
                                    // },
                                    // ],
                                    [
                                    'class' => 'raw', // class option allows to change column class
                                    'formatters' => ['raw'],
                                    'value' => function ($row) {
                                    return view('users.include.action', ['row' => $row])->render();
                                    },
                                    ],
                                    ],
                                    ])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layout.app>