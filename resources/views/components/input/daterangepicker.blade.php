{{-- 
@source=[
        https://wehelpcode.com/free-code-snippets/how-to-use-date-range-picker-in-laravel-php,
        https://www.daterangepicker.com
] 
Contoh penggunaan:
Range Calendar:
<x-input.daterangepicker name1="waktu_awal" name2="waktu_akhir" :value1="old('waktu_awal', $forms['waktu_awal'])"
        :value2="old('waktu_akhir', $forms['waktu_akhir'])" placeholder="Pilih Rentang Tanggal" opens="right"
        customRangeLabel="1 Thn Ajaran" />

Single Calendar:
<x-input.daterangepicker name1="tanggal_lahir" value1="{{ old('tanggal_lahir', $pendaftaranTryout?->tanggal_lahir) }}"
        placeholder="Pilih Tanggal" opens="right"
        singleDatePicker="true" :ranges="false" />
--}}

@props([
    'name1',
    'name2' => null,
    'id1' => null,
    'id2' => null,
    'value1' => null,
    'value2' => null,
    'placeholder' => '',
    'opens' => 'left',
    'format' => 'DD/MM/YYYY',
    'ranges' => true,
    'singleDatePicker' => 'false',
    'customRangeLabel' => 'Custom',
    'class' => '',
])

@php
    $id1 = $id1 ?? $name1;
    $id2 = $id2 ?? $name2;
    $value1 = empty($value1) ? date('Y-m-d') : $value1;
    $value2 = empty($value2) ? date('Y-m-d') : $value2;
@endphp

<div class="date-range-picker-wrapper" x-data="{
    init() {
        const config = {
            opens: '{{ $opens }}',
            locale: {
                format: '{{ $format }}',
                separator: ' - ',
                applyLabel: 'Ok',
                cancelLabel: 'Batal',
                fromLabel: 'Awal',
                toLabel: 'Akhir',
                customRangeLabel: '{{ $customRangeLabel }}',
                weekLabel: 'W',
                daysOfWeek: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                monthNames: [
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
                ],
                firstDay: 1
            },
            singleDatePicker: {{ $singleDatePicker }},
            alwaysShowCalendars: true,
            startDate: moment('{{ $value1 }}'),
            endDate: moment('{{ $value2 }}'),
        };

        @if ($ranges) config.ranges = {
            'Hari Ini': [moment(), moment()],
            'Besok': [moment().add(1, 'day').startOf('day'), moment().add(1, 'day').endOf('day')],
            '7 Hari Kedepan': [moment().startOf('day'), moment().add(6, 'days').endOf('day')],
            'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
            'Bulan Depan': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
        }; @endif

        // Init daterangepicker
        $($refs.input).daterangepicker(config, (start, end) => {
            $refs.startDate.value = start.format('YYYY-MM-DD');
            $refs.endDate.value = end.format('YYYY-MM-DD');
        });
    }
}">
    <input type="text" x-ref="input" placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control ' . $class]) }} />

    <input type="hidden" x-ref="startDate" name="{{ $name1 }}" id="{{ $id1 }}"
        value="{{ $value1 }}" />

    <input type="hidden" x-ref="endDate" name="{{ $name2 }}" id="{{ $id2 }}"
        value="{{ $value2 }}" />
</div>

{{-- once ini dan dibawah --}}
@once
    @push('style')
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    @endpush

    @push('script')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.3/cdn.min.js"
            integrity="sha512-ZVf/lRjmZflPdIT4hvK4g1T6WupvrXtoTAM86z3S+5En7AhDVhBaxLRF4blGftmzhhPigloA8EP8OTO/Aabmng=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @endpush
@endonce
