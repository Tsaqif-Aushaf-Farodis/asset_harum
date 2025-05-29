@props(['toast' => false])

@pushOnce('style')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endPushOnce

@push('script')
    @if (session('success'))
        @if (!$toast)
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: "{{ session('success') }}",
                });
            </script>
        @else
            <script>
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: "{{ session('success') }}",
                    animation: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
            </script>
        @endif
    @endif

    @if (session('error'))
        @if (!$toast)
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: "{{ session('error') }}",
                });
            </script>
        @else
            <script>
                Swal.fire({
                    toast: true,
                    icon: 'error',
                    title: "{{ session('error') }}",
                    animation: true,
                    position: 'top-right',
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true,
                    showCloseButton: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })
            </script>
        @endif
    @endif

    @if ($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: `
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        `,
            });
        </script>
    @endif
@endpush
