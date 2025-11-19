<div>
    <select
        name="{{ $name }}"
        id="{{ $name }}"
        wire:model="{{ $wire }}"
        x-data
        x-init="new TomSelect($refs.select)"
        x-ref="select"
        class="form-input w-full">
        <option value="">Pilih {{ ucwords(str_replace('_', ' ', $placeholder)) }}</option>
        @foreach ($options as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>
</div>