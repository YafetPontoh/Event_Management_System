<?php
session_start();
include 'config.php';

// --- 1. CEK USER & ROLE ---
// Default role adalah 'guest' jika belum login
$role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'guest';

// --- 2. LOGIKA FILTER AKSES ---
$where_access = "";

// Jika user adalah GUEST, paksa hanya menampilkan event PUBLIC
// Jika user SUDAH LOGIN (Admin/Panitia/Participant), filter ini kosong (artinya bisa lihat Internal & Public)
if ($role == 'guest') {
    $where_access = "AND access_type = 'public'";
}

// --- 3. QUERY BERDASARKAN WAKTU & STATUS (HANYA APPROVED) ---
$today = date('Y-m-d');

// A. SEDANG BERLANGSUNG (Hari Ini)
$q_ongoing = "SELECT * FROM events 
              WHERE event_date = '$today' 
              AND status = 'approved' 
              $where_access 
              ORDER BY start_time ASC";
$res_ongoing = mysqli_query($conn, $q_ongoing) or die(mysqli_error($conn));

// B. AKAN DATANG (Besok ke atas)
// Limit saya naikkan jadi 9 biar muat banyak
$q_upcoming = "SELECT * FROM events 
               WHERE event_date > '$today' 
               AND status = 'approved' 
               $where_access 
               ORDER BY event_date ASC LIMIT 9"; 
$res_upcoming = mysqli_query($conn, $q_upcoming) or die(mysqli_error($conn));

// C. SUDAH SELESAI (Kemarin ke bawah)
$q_past = "SELECT * FROM events 
           WHERE event_date < '$today' 
           AND status = 'approved' 
           $where_access 
           ORDER BY event_date DESC LIMIT 4";
$res_past = mysqli_query($conn, $q_past) or die(mysqli_error($conn));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventApp - Temukan Event Seru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
            color: white;
            padding: 80px 0;
            border-radius: 0 0 50px 50px;
            margin-bottom: 50px;
        }
        .section-title {
            font-weight: 800;
            color: #343a40;
            margin-bottom: 25px;
            padding-left: 15px;
            border-left: 5px solid #0d6efd;
        }
        .img-card-custom {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }
    </style>
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-calendar-event-fill me-2"></i>EventApp</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item me-3 text-white">
                            Halo, <b><?= htmlspecialchars($_SESSION['name']); ?></b>
                            <span class="badge bg-light text-primary ms-1"><?= strtoupper($role); ?></span>
                        </li>
                        <li class="nav-item"><a href="dashboard.php" class="btn btn-light text-primary fw-bold rounded-pill px-4">Dashboard</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="login.php" class="btn btn-outline-light me-2 rounded-pill px-4">Login</a></li>
                        <li class="nav-item"><a href="register.php" class="btn btn-light text-primary fw-bold rounded-pill px-4">Daftar</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">TEMUKAN EVENT SERU</h1>
            <p class="lead opacity-90">Ikuti kegiatan bermanfaat, seminar, dan workshop terkini.</p>
        </div>
    </header>

    <div class="container pb-5">

        <div class="mb-5">
            <h3 class="section-title text-danger border-danger">
                <i class="bi bi-broadcast me-2 animate-pulse"></i>Sedang Berlangsung (Hari Ini)
            </h3>
            <div class="row g-4">
                <?php if(mysqli_num_rows($res_ongoing) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($res_ongoing)): include 'includes/card_flyer.php'; endwhile; ?>
                <?php else: ?>
                    <?php include 'includes/empty_card.php'; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="section-title text-primary">
                <i class="bi bi-hourglass-split me-2"></i>Segera Hadir
            </h3>
            <div class="row g-4">
                <?php if(mysqli_num_rows($res_upcoming) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($res_upcoming)): include 'includes/card_flyer.php'; endwhile; ?>
                <?php else: ?>
                    <?php include 'includes/empty_card.php'; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="mb-5">
            <h3 class="section-title text-secondary border-secondary">
                <i class="bi bi-check-circle-fill me-2"></i>Event Selesai
            </h3>
            <div class="row g-4">
                <?php if(mysqli_num_rows($res_past) > 0): ?>
                    <?php 
                        $is_past = true; 
                        while($row = mysqli_fetch_assoc($res_past)): 
                            include 'includes/card_flyer.php'; 
                        endwhile; 
                        unset($is_past);
                    ?>
                <?php else: ?>
                    <?php include 'includes/empty_card.php'; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <footer class="text-center py-4 text-muted small mt-auto border-top bg-white">
        &copy; 2025 EventApp System | Project Informatika
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>