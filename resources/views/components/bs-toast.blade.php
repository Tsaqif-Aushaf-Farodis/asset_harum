@if (session('success') || session('error') || session('warning') || session('status') || $errors->any())
    <div class="p-3 end-0 toast-container position-fixed" style="top: 10px; z-index: 99999;">
        @if (session('success'))
            <div class="bs-toast toast fade show bg-primary" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-autohide="true">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">Sukses</div>
                    <small class="toast-time-display" data-time="{{ now() }}"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('success') }}</div>
            </div>
        @elseif (session('error'))
            <div class="bs-toast toast fade show bg-danger" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-autohide="true">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">Error</div>
                    <small class="toast-time-display" data-time="{{ now() }}"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('error') }}</div>
            </div>
        @elseif (session('warning'))
            <div class="bs-toast toast fade show bg-warning" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-autohide="false">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">Peringatan!</div>
                    <small class="toast-time-display" data-time="{{ now() }}"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('warning') }}</div>
            </div>
        @elseif (session('info'))
            <div class="bs-toast toast fade show bg-info" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-autohide="true">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">Info</div>
                    <small class="toast-time-display" data-time="{{ now() }}"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">{{ session('info') }}</div>
            </div>
        @elseif ($errors->any())
            <div class="bs-toast toast fade show bg-warning" role="alert" aria-live="assertive" aria-atomic="true"
                data-bs-autohide="true">
                <div class="toast-header">
                    <i class="bx bx-bell me-2"></i>
                    <div class="me-auto fw-medium">Errors</div>
                    <small class="toast-time-display" data-time="{{ now() }}"></small>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>

    @pushOnce('script')
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                function timeAgo(date) {
                    const now = new Date();
                    const diffInSeconds = Math.floor((now - new Date(date)) / 1000);
                    const diffInMinutes = Math.floor(diffInSeconds / 60);
                    const diffInHours = Math.floor(diffInMinutes / 60);
                    const diffInDays = Math.floor(diffInHours / 24);

                    if (diffInSeconds < 60) {
                        return 'Baru saja'; // kurang dari 1 menit
                    } else if (diffInMinutes < 60) {
                        return diffInMinutes + ' menit yang lalu'; // kurang dari 1 jam
                    } else if (diffInHours < 24) {
                        return diffInHours + ' jam ' + (diffInMinutes % 60) + ' menit yang lalu'; // kurang dari 1 hari
                    } else {
                        return diffInDays + ' hari yang lalu'; // lebih dari 1 hari
                    }
                }

                // Fungsi untuk memperbarui teks pada semua elemen dengan class 'time-display'
                function updateTimeDisplays() {
                    const timeElements = document.querySelectorAll('.toast-time-display');

                    timeElements.forEach(function(element) {
                        const dateString = element.getAttribute('data-time'); // Ambil nilai data-time
                        if (dateString) {
                            element.innerHTML = timeAgo(dateString); // Update teks dengan waktu relatif
                        }
                        // console.log(timeAgo(dateString));
                    });
                }

                // Update tampilan waktu pertama kali saat halaman dimuat
                updateTimeDisplays();

                // Setiap 10 detik, update waktu
                setInterval(updateTimeDisplays, 1000); // 10000 ms = 10 detik
            });
        </script>
    @endPushOnce
@endif
