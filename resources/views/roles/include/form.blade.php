@pushOnce('script')
<script src="{{ asset('assets/js/jquery.checkboxall-1.0.min.js') }}"></script>
@endPushOnce

<div class="mb-2 row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">{{ __('Name') }}</label>
            <input type="text" name="name" id="name" class="mb-2 form-control @error('name') is-invalid @enderror"
                placeholder="{{ __('Name') }}" value="{{ isset($role) ? $role->name : old('name') }}" autofocus
                required>
            @error('name')
            <span class="invalid-feedback">
                {{ $message }}
            </span>
            @enderror
        </div>
    </div>
    <div class="col-md-3">
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="" class="d-none d-md-inline-block"></label>
            <x-input.confirm-button url="{{ route('permissions.refresh') }}" class="mb-2 form-control btn btn-danger"
                title="Refresh Permission?"
                text="Proses ini akan menghapus dan menambah permission dari file konfigurasi!" positive="Ya, lanjut!"
                icon="warning">
                Refresh Permission
            </x-input.confirm-button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <label class="mb-1">{{ __('Permissions') }}</label>
        @error('permissions')
        <div class="mt-0 mb-2 text-danger">{{ $message }}</div>
        @enderror
    </div>

    @foreach (config('permission.permissions') as $key => $permission)
    @php
    $containerId = "permission-group-{$key}";
    @endphp
    <div class="col-md-3">
        <div class="mb-2 border card">
            <div class="card-content">
                <div class="card-body" id={{ $containerId }}>
                    <div class="d-flex align-items-center">
                        <h4 class="mb-0 card-title d-inline">
                            {{ ucwords($permission['group']) }}
                        </h4>
                        <input class="ms-2 form-check-input allcheck" type="checkbox" />
                        @push('script')
                        <script>
                            $(document).ready(function() {
                                        $("#{{ $containerId }}").checkboxall('allcheck');
                                    })
                        </script>
                        @endpush
                    </div>

                    <div class="mt-2">
                        @foreach ($permission['access'] as $access)
                        @if (isset($existingPermissions) && in_array($access, $existingPermissions))
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{ str()->slug($access) }}"
                                name="permissions[]" value="{{ $access }}" {{ isset($role) &&
                                $role->hasPermissionTo($access) ? 'checked' : '' }} />
                            <label class="form-check-label" for="{{ str()->slug($access) }}">
                                {{ $access }}
                            </label>
                        </div>
                        @else
                        @if (isset($existingPermissions))
                        <div class="form-check">
                            <label class="form-check-label text-danger">
                                Permission "{{ $access }}" tidak ditemukan di database.
                            </label>
                        </div>
                        @else
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="{{ str()->slug($access) }}"
                                name="permissions[]" value="{{ $access }}" />
                            <label class="form-check-label" for="{{ str()->slug($access) }}">
                                {{ $access }}
                            </label>
                        </div>
                        @endif
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>