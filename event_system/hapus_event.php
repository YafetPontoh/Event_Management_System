<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); 
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$id = $_GET['id'];
$uid = $_SESSION['user_id'];
$role = $_SESSION['role'];

$query_cek = mysqli_query($conn, "SELECT created_by FROM events WHERE id = '$id'");
$data = mysqli_fetch_assoc($query_cek);

if (!$data) {
    echo "<script>alert('Event tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

if ($role != 'admin' && $data['created_by'] != $uid) {
    echo "<script>alert('Akses Ditolak! Anda bukan pemilik event ini.'); window.location='dashboard.php';</script>";
    exit;
}

mysqli_query($conn, "DELETE FROM registrations WHERE event_id = '$id'");

if (mysqli_query($conn, "DELETE FROM events WHERE id = '$id'")) {
    echo "<script>alert('Event dan seluruh data pendaftarnya berhasil dihapus.'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal menghapus event.'); window.location='dashboard.php';</script>";
}
?>