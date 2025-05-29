<x-layout.app title="Roles" activeMenu="roles">
    <div class="container my-5">
        <div class="page-heading">
            <div class="page-title">
                <div class="row">
                    <div class="order-last col-12 col-md-8 order-md-1">
                        <h3>{{ __('Roles') }}</h3>
                        <p class="text-subtitle text-muted">
                            {{ __('Below is a list of all roles.') }}
                        </p>
                    </div>
                    <x-breadcrumb>
                        <li class="breadcrumb-item"><a href="/">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('Role') }}</li>
                    </x-breadcrumb>
                </div>
            </div>

            <section class="section">
                @can('role & permission create')
                    <div class="d-flex justify-content-end">
                        <a href="{{ route('roles.create') }}" class="mb-3 btn btn-primary">
                            <i class="fas fa-plus"></i>
                            {{ __('Create a new role') }}
                        </a>
                    </div>
                @endcan

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
                                            'name',
                                            [
                                                'class' => 'raw',
                                                'attribute' => 'updated_at',
                                                'title' => 'Last Update',
                                                'formatters' => ['raw'],
                                                'value' => function ($row) {
                                                    return $row->updated_at->format('d-m-Y H:i:s');
                                                },
                                            ],
                                            [
                                                'class' => 'raw', // class option allows to change column class
                                                'formatters' => ['raw'],
                                                'value' => function ($row) {
                                                    return view('roles.include.action', ['row' => $row])->render();
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
