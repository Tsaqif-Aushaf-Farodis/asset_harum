<x-layout.app title="Dashboard" activeMenu="dashboard" :withError="true">
    <div class="container my-5">
        <div class="card">
            <div class="card-header">{{ __('Dashboard') }}</div>

            @role('Admin')
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            @else
                <div class="card-body">
                    <p>
                        Selamat datang User.
                    </p>
                </div>
            @endrole
        </div>
    </div>
</x-layout.app>
