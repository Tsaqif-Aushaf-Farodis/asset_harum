<x-layouts.app title="Profile">
    <div class="container my-5">
        <x-breadcrumb title="Profile" :breadcrumbs="[['label' => 'Dashboard', 'url' => url('/')], ['label' => 'Profile']]" />

        <x-error-list />

        <div class="card">
            <div class="card-body">
                <form action="{{ route('profile') }}" method="POST" role="form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username Login</label>
                        <input type="username" class="form-control @error('username') is-invalid @enderror"
                            id="username" name="username" value="{{ old('username', $user->username) }}" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- <div class="mb-3">
                        <label for="email" class="form-label">Email (login)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                            name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
