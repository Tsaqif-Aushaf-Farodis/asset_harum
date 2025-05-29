{{-- 
contoh select biasa:
<x-input.select2 
    name="status" 
    class="@error('status') is-invalid @enderror" 
    placeholder="Pilih Status" 
    :options="[
        '1' => 'Aktif',
        '0' => 'Non Aktif'
    ]" 
    :selected="old('status', $data->status)" 
/>

contoh multiple select:
<x-input.select2 
    name="roles[]" 
    :options="$roles" 
    :multiple="true"
    :selected="old('roles', $user->roles->pluck('id')->toArray())"
/>

--}}

@props([
    'name',
    'options',
    'id' => null,
    'selected' => null,
    'placeholder' => null,
    'clearable' => false,
    'multiple' => false,
    'class' => '',
    'required' => false,
    'disabled' => false,
])

@php
    $id = $id ?? 'select2_' . $name . '_' . uniqid();

    // function untuk cek apakah array berbentuk
    // $data = [
    //     'Group 1' => [
    //         1 => 'Option 1',
    //         2 => 'Option 2',
    //     ],
    //     'Group 2' => [
    //         3 => 'Option 3',
    //         4 => 'Option 4',
    //     ],
    // ];
    $hasGroups = function ($array) {
        foreach ($array as $value) {
            if (is_array($value)) {
                return true;
            }
        }
        return false;
    };
@endphp

<div wire:ignore>
    <select style="visibility: collapse; height: 1px !important;" name="{{ $name }}" id="{{ $id }}"
        data-clearable="{{ $clearable ? 'true' : 'false' }}" data-placeholder="{{ $placeholder }}"
        @if ($multiple) multiple @endif @if ($required) required @endif
        @if ($disabled) disabled @endif
        {{ $attributes->merge([
            'class' => 'form-control select2 ' . $class,
        ]) }}>

        @if ($placeholder && !$multiple)
            <option value="">{{ $placeholder }}</option>
        @endif

        @if ($hasGroups($options))
            {{-- Render options dengan groups --}}
            @foreach ($options as $groupLabel => $groupOptions)
                <optgroup label="{{ $groupLabel }}">
                    @foreach ($groupOptions as $value => $label)
                        <option value="{{ $value }}" @if (is_array($selected) ? in_array($value, $selected) : $value == $selected) selected @endif>
                            {{ $label }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        @else
            {{-- Render options tanpa groups --}}
            @foreach ($options as $value => $label)
                <option value="{{ $value }}" @if (is_array($selected) ? in_array($value, $selected) : $value == $selected) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        @endif
    </select>
</div>

@pushOnce('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css') }}/select2-bootstrap-5-theme.min.css" />
@endPushOnce

{{-- ini akan dipush 1x karena content didalamnya tidak berubah --}}
@pushOnce('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
    <script>
        function initSelect2(elementId, options = {}) {
            const $element = $(`#${elementId}`);

            const defaultOptions = {
                theme: 'bootstrap-5',
                placeholder: $element.data('placeholder'),
                allowClear: $element.data('clearable'),
                ...options
            };

            $element.select2(defaultOptions);

            $element.on('select2:open', () => {
                setTimeout(() => {
                    document.querySelector(
                        '.select2-container--open .select2-search__field')?.focus();
                }, 200);
            });
        };
    </script>
@endPushOnce

{{-- kode ini akan di render berkali2 --}}
@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initSelect2("{{ $id }}");
        });
    </script>
@endpush
