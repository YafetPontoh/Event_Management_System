# EventApp

Aplikasi web interaktif untuk menemukan event seru, seminar, dan workshop terkini. EventApp dilengkapi dengan sistem manajemen hak akses (Role-Based Access Control) yang memisahkan fitur untuk Admin, Panitia, Participant, dan Guest.

## Fitur Utama

* **Sistem Multi-Role & Autentikasi:** * Mendukung login untuk Admin, Panitia, dan Participant. 
  * Menggunakan `password_verify` untuk verifikasi keamanan *password*.
* **Filter Akses Visibilitas:** * Guest (belum login) secara otomatis hanya dapat melihat event dengan akses 'public'.
  * User yang sudah login dapat mengakses event 'public' maupun internal.
* **Sistem Validasi Event (Admin):** * Admin memiliki wewenang untuk melihat semua pengajuan event.
  * Admin dapat menyetujui (Approve) event agar tayang, atau menolaknya (Reject) dengan memberikan catatan/alasan penolakan.
* **Manajemen Event Terbatas (Panitia):** * Panitia hanya dapat melihat dan mengelola event yang mereka buat sendiri.
  * Panitia dapat mengedit/merevisi event buatannya, **kecuali** event tersebut sudah berstatus 'Approved' oleh Admin.
* **Kategorisasi Waktu Dinamis:** * Halaman beranda otomatis memfilter event yang "Sedang Berlangsung" (hari ini), "Segera Hadir" (besok ke atas), dan "Event Selesai" (kemarin ke bawah).
* **Dashboard Informatif:** * Menampilkan statistik jumlah event yang dikelola dan total pendaftar/registrasi.

## Teknologi yang Digunakan

* **Frontend:** HTML5, CSS3, Bootstrap 5.3, Bootstrap Icons.
* **Backend:** PHP Native (Session & Routing manual).
* **Database:** MySQL / MariaDB (Menggunakan ekstensi `mysqli`).

## Cara Instalasi & Menjalankan Aplikasi

1. Clone repositori ini ke dalam folder `htdocs` (jika menggunakan XAMPP) atau direktori server lokal Anda.
   ```bash
   git clone [https://github.com/username/event-app.git](https://github.com/username/event-app.git)
