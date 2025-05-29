{{-- 
contoh penggunaan:
<x-input.confirm-button text="Data ini akan diupdate!" positive="Ya, simpan!" icon="info">
    Simpan
</x-input.confirm-button>
--}}

@props([
    'url' => null,
    'class' => 'btn btn-danger',
    'title' => 'Apakah Anda yakin?',
    'text' => 'Data ini akan dihapus!',
    'icon' => 'warning',
    'positive' => 'Ya, hapus!',
    'negative' => 'Batal',
])

<a href="#" {{ $attributes->merge(['class' => $class . ' confirm-button']) }} data-url="{{ $url }}"
    data-title="{{ $title }}" data-text="{{ $text }}" data-icon="{{ $icon }}"
    data-positive="{{ $positive }}" data-negative="{{ $negative }}">
    {{ $slot }}
</a>

@pushOnce('style')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endPushOnce

@pushOnce('script')
    <script>
        function handleConfirmAction(button) {
            Swal.fire({
                title: button.getAttribute('data-title'),
                text: button.getAttribute('data-text'),
                icon: button.getAttribute('data-icon'),
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: button.getAttribute('data-positive'),
                cancelButtonText: button.getAttribute('data-negative')
            }).then((result) => {
                if (result.isConfirmed) {
                    if (button.getAttribute('data-url')) {
                        window.location.href = button.getAttribute('data-url');
                    } else {
                        button.closest('form').submit();
                    }
                }
            });
        }

        // Event delegation pada document level
        document.addEventListener('click', function(event) {
            const button = event.target.closest('.confirm-button');
            if (button) {
                event.preventDefault();
                handleConfirmAction(button);
            }
        });
    </script>
@endPushOnce
