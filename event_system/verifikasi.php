<?php
session_start();
include 'config.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// --- PERBAIKAN UTAMA DI SINI ---
// Ambil role, hilangkan spasi (trim), dan paksa jadi huruf kecil (strtolower)
// Jadi: "ADMIN" -> "admin", " Panitia " -> "panitia"
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';
$user_id = $_SESSION['user_id'];

// 2. Security Check
// Karena sudah di-strtolower, kita cukup cek huruf kecil saja
if ($role != 'admin' && $role != 'panitia') {
    // Tampilkan alert error biar tau kenapa ditolak
    echo "<script>alert('Akses Ditolak! Role Anda terbaca sebagai: " . $role . "'); window.history.back();</script>";
    exit;
}

// 3. Proses Verifikasi
if (isset($_GET['reg_id']) && isset($_GET['status']) && isset($_GET['event_id'])) {
    
    $reg_id = mysqli_real_escape_string($conn, $_GET['reg_id']);
    $status = mysqli_real_escape_string($conn, $_GET['status']); 
    $event_id = mysqli_real_escape_string($conn, $_GET['event_id']);

    // Validasi input status
    if ($status != 'confirmed' && $status != 'rejected') {
        echo "<script>alert('Status tidak valid!'); window.location='detail_event.php?id=$event_id';</script>";
        exit;
    }

    // 4. (Khusus Panitia) Cek Kepemilikan Event
    if ($role == 'panitia') {
        $cek_owner = mysqli_query($conn, "SELECT created_by FROM events WHERE id = '$event_id'");
        $event_data = mysqli_fetch_assoc($cek_owner);

        if (!$event_data || $event_data['created_by'] != $user_id) {
            echo "<script>alert('Anda tidak berhak memverifikasi event ini.'); window.location='detail_event.php?id=$event_id';</script>";
            exit;
        }
    }

    // 5. Update Database
    $query_update = "UPDATE registrations SET status = '$status' WHERE id = '$reg_id'";
    
    if (mysqli_query($conn, $query_update)) {
        // Sukses -> Balik ke detail event
        header("Location: detail_event.php?id=$event_id");
        exit;
    } else {
        echo "Error Database: " . mysqli_error($conn);
    }

} else {
    // Jika parameter URL kurang
    header("Location: dashboard.php");
    exit;
}
?>