{{-- 
contoh penggunaan:

-- VIEW
<x-input.select2 
    name="negara" 
    :options="Negara::all()->pluck('nama_negara','kode_negara')" 
    placeholder="Pilih Negara"
    :selected="old('negara', $forms['negara'])" 
    clearable="true" />

<x-input.depdrop-select2 
    name="provinsi" 
    placeholder="Pilih Provinsi" 
    :selected="old('provinsi', $forms['provinsi'])"
    clearable="true" 
    depends="negara" 
    url="{{ route('provinsi.get') }}" />

-- CONTROLLER
public function getProvinsi(Request $request) {
        $depends = $request->input('depends');
        $search = $request->input('q', '');

        $provinsi = Provinsi::where($depends, $search)
            ->get()
            ->map(function ($provinsi) {
                return ['id' => $provinsi->kode_provinsi, 'text' => $provinsi->nama_provinsi];
            });

        return response()->json($provinsi);
    }
--}}

@props([
    'name',
    'options' => [],
    'id' => null,
    'selected' => null,
    'placeholder' => null,
    'clearable' => false,
    'depends' => null,
    'url' => null,
])

<x-input.select2 :name="$name" :options="$options" :id="$id" :selected="$selected" :placeholder="$placeholder"
    :clearable="$clearable" :attributes="$attributes->merge(['data-depends' => $depends, 'data-url' => $url, 'data-selected' => $selected])" />

{{-- ini akan dipush 1x karena content didalamnya tidak berubah --}}
@pushOnce('script')
    <script>
        $(document).ready(function() {
            $('[data-depends]').each(function() {
                const $select = $(this);
                const dependsOn = $select.data('depends');
                const url = $select.data('url');
                let selectedValue = $select.data('selected');

                function loadOptions() {
                    const q = $('[name="' + dependsOn + '"]').val();

                    $.ajax({
                        url: url,
                        dataType: 'json',
                        data: {
                            depends: dependsOn,
                            q: q,
                        },
                        success: function(data) {
                            // Kosongkan opsi yang ada
                            $select.empty();

                            // Tambahkan opsi placeholder jika diperlukan
                            $select.append(new Option('Pilih opsi', '', true, true));

                            // Tambahkan opsi baru
                            $.each(data, function(index, item) {
                                $select.append(new Option(item.text, item.id, false,
                                    item.id == selectedValue));
                            });

                            // Paksa tutup Select2
                            $select.select2('close');
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error loading options:', textStatus, errorThrown);
                        }
                    });
                }

                // Muat opsi saat halaman dimuat
                loadOptions();

                // Muat ulang opsi saat elemen 'depends' berubah
                $('[name="' + dependsOn + '"]').on('change', function() {
                    loadOptions();
                });
            });
        });
    </script>
@endPushOnce
