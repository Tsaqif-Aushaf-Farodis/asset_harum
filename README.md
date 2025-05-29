# Project Plan

Berikut adalah rencana fitur dan perbaikan yang akan dilakukan:

## 1. Menu Profile (0%)

-   Menambahkan menu profil pengguna untuk memungkinkan pengguna melihat dan mengedit informasi profil mereka.

## 2. Change Password (0%)

-   Menambahkan fitur untuk memungkinkan pengguna mengubah kata sandi mereka melalui pengaturan profil.

## 3. Fitur User Visit Log (66%)

-   Menambahkan pencatatan untuk setiap pengguna yang login. :white_check_mark:
-   Melakukan migrasi tabel `users_login_logs` untuk mencatat waktu dan detail login pengguna. :white_check_mark:
-   Menambahkan menu untuk menampilkan log user login.

## 4. Sidebar Desktop (0%)

-   Memperbaiki fungsi untuk menutup sidebar pada mode desktop yang belum berjalan dengan baik.
-   Setelah sidebar ditutup, hanya ikon yang akan tampil (mengikuti desain seperti AdminLTE 3).

## 5. Alternatif SweetAlert2 (0%)

-   Mencari dan mengganti penggunaan SweetAlert2 dengan alternatif yang lebih modern dan up-to-date, karena SweetAlert2 mulai terasa kuno.

## 6. Modifikasi CRUD Generator (10%)

-   Penambahan fitur search :white_check_mark:
-   Penambahan fitur sorting by klik header kolom
-   Perbaikan interaksi ketika generate controller (Ketika model belum dimasukkan maka ada input untuk memasukkan nama model)
-   Model generator harus dibuat sendiri (saat ini masih tergantung ke reliese/laravel)
-   Ketika generate view data yang memiliki relasi maka dibuatkan juga view nya (form tabular pada create dan update, detail informasi relasi pada show). Tapi jadikan ini sebagai parameter ketika generate (Generate juga view dengan relasi? Y/N)
-   Tambah CRUD untuk laporan dan rekap jadi dengan template yang sama dengan yang biasa dibuat (intinya ada filder dan ada tabel)
-   kalo bisa ketika generate halaman index.php itu dari dalam variabel tau informasi tipe data kolom nya jadi bisa dibuat otomatis misal boolean/enum otomatis pakai pills dan pakai keterangan Aktif/Tidak, misal decimal otomatis pakai format number
-   Ketika generate crud bisa nambahin parameter nama controller, supaya nama controller bisa beda dengan nama tabel
