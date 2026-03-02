<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); exit;
}

$role = strtoupper(trim($_SESSION['role']));
if ($role !== 'ADMIN') {
    echo "<script>alert('Akses ditolak'); window.location='dashboard.php';</script>";
    exit;
}

$id = (int) $_GET['id'];

mysqli_query($conn, "
    UPDATE events 
    SET status='approved', rejection_note=NULL 
    WHERE id=$id
");

echo "<script>alert('Event disetujui'); window.location='review_event.php';</script>";
