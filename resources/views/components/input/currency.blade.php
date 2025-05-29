{{-- 
contoh penggunaan:
<x-input.currency name="total"
    value="{{ old('total', $targetPendapatan?->total) }}" placeholder="Total"
    class="form-control text-end {{ $errors->has('total') ? 'is-invalid' : '' }}" />
--}}


@props([
    'name',
    'value' => 0,
    'class' => '',
    'id' => null,
    'readonly' => false,
    'disabled' => false,
    'hiddenClass' => 'hidden-nominal',
])

@php
    $id = $id ?? 'input-currency-' . uniqid();
    $value = is_numeric($value) ? (float) $value : 0;
@endphp

<div>
    <input type="text" id="{{ $id }}" name="{{ $name }}_display"
        value="{{ number_format($value, 0, ',', '.') }}" class="{{ $class }}" {{ $readonly ? 'readonly' : '' }}
        {{ $disabled ? 'disabled' : '' }} data-type="currency">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" class="{{ $hiddenClass }}">
</div>

@pushOnce('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Currency formatting
            document.querySelectorAll('input[data-type="currency"]').forEach(function(element) {
                element.addEventListener('input', function(e) {
                    let value = this.value.replace(/\D/g, "");
                    this.value = new Intl.NumberFormat('id-ID').format(value);

                    let hiddenInput = this.nextElementSibling;
                    if (hiddenInput && hiddenInput.type === 'hidden') {
                        hiddenInput.value = value ? parseInt(value) : 0;
                    }
                });
            });
        });
    </script>
@endPushOnce
