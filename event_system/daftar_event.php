<?php
session_start();
include 'config.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek ID Event
if (!isset($_GET['event_id'])) {
    header("Location: dashboard.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_GET['event_id'];
$today = date('Y-m-d');

$query_event = mysqli_query($conn, "SELECT * FROM events WHERE id = '$event_id'");
$event = mysqli_fetch_assoc($query_event);

if (!$event) {
    echo "<script>alert('Event tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if ($event['status'] != 'approved') {
    echo "<script>alert('Pendaftaran Gagal! Event ini belum dibuka atau sedang dalam peninjauan.'); window.location='index.php';</script>";
    exit;
}

if ($event['event_date'] < $today) {
    echo "<script>alert('Event sudah selesai!'); window.location='index.php';</script>";
    exit;
}

// Cek Kuota
if ($event['quota'] <= 0) {
    echo "<script>alert('Kuota penuh!'); window.location='index.php';</script>";
    exit;
}

$cek_daftar = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id = '$user_id' AND event_id = '$event_id'");
if (mysqli_num_rows($cek_daftar) > 0) {
    echo "<script>alert('Anda sudah terdaftar di event ini!'); window.location='tiket_saya.php';</script>";
    exit;
}

$new_quota = $event['quota'] - 1;
mysqli_query($conn, "UPDATE events SET quota = '$new_quota' WHERE id = '$event_id'");

$query_reg = "INSERT INTO registrations (user_id, event_id, registration_date, status) VALUES ('$user_id', '$event_id', NOW(), 'pending')";

if (mysqli_query($conn, $query_reg)) {
    echo "<script>alert('Pendaftaran Berhasil! Silakan tunggu verifikasi admin/panitia.'); window.location='tiket_saya.php';</script>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>