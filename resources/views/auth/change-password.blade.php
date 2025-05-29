<x-layouts.app title="Change Password">
    <div class="container my-5">
        <x-breadcrumb title="Change Password" :breadcrumbs="[['label' => 'Dashboard', 'url' => url('/')], ['label' => 'Change Password']]" />

        <x-error-list />

        <div class="card">
            <div class="card-body">
                <form action="{{ route('change-password') }}" method="POST" role="form">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Old Password</label>
                        <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                            id="current_password" name="current_password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                            id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Save</button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
