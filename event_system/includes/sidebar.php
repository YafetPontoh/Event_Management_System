<?php
// File: includes/sidebar.php
$role = strtoupper($_SESSION['role'] ?? '');
?>

<div class="bg-white border-end" id="sidebar-wrapper">
    <div class="sidebar-heading text-center py-4 border-bottom bg-light">
        <a href="index.php" class="text-decoration-none text-primary fw-bold fs-5">
            <i class="bi bi-calendar-event-fill me-2"></i>EventApp
        </a>
    </div>

    <div class="list-group list-group-flush my-3">

        <div class="sidebar-group-title text-muted small fw-bold px-3 mb-2 text-uppercase">
            Main Menu
        </div>

        <a href="dashboard.php" class="list-group-item list-group-item-action bg-transparent border-0">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>

        <div class="sidebar-group-title text-muted small fw-bold px-3 mb-2 mt-4 text-uppercase">
            Manajemen Event
        </div>

        <?php if ($role === 'ADMIN'): ?>
            <a href="tambah_event.php" class="list-group-item list-group-item-action bg-transparent border-0">
                <i class="bi bi-plus-circle me-2"></i>Buat Event
            </a>

            <a href="review_event.php" class="list-group-item list-group-item-action bg-transparent border-0">
                <i class="bi bi-check-circle me-2"></i>Review Event
            </a>

            <a href="kelola_user.php" class="list-group-item list-group-item-action bg-transparent border-0">
                <i class="bi bi-people me-2"></i>Kelola User
            </a>
        <?php endif; ?>

        <?php if ($role === 'PANITIA'): ?>
            <a href="tambah_event.php" class="list-group-item list-group-item-action bg-transparent border-0">
                <i class="bi bi-file-earmark-arrow-up me-2"></i>Ajukan Event
            </a>
        <?php endif; ?>

        <a href="jadwal.php" class="list-group-item list-group-item-action bg-transparent border-0">
            <i class="bi bi-calendar3 me-2"></i>Jadwal Acara
        </a>

        <?php if ($role === 'PARTICIPANT' || $role === 'GUEST'): ?>
            <a href="tiket_saya.php" class="list-group-item list-group-item-action bg-transparent border-0">
                <i class="bi bi-ticket-perforated me-2"></i>Tiket Saya
            </a>
        <?php endif; ?>

        <div class="sidebar-group-title text-muted small fw-bold px-3 mb-2 mt-4 text-uppercase">
            Akun
        </div>

        <a href="logout.php" class="list-group-item list-group-item-action bg-transparent border-0 text-danger fw-bold">
            <i class="bi bi-box-arrow-left me-2"></i>Logout
        </a>
    </div>
</div>