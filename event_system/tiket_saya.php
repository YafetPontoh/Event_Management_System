<?php
session_start();
include 'config.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Query Data Pendaftaran Join dengan Data Event
$query = "SELECT registrations.id as reg_id, registrations.status as reg_status, registrations.registration_date,
                 events.id as event_id, events.title, events.event_date, events.start_time, events.end_time, events.location, events.image
          FROM registrations
          JOIN events ON registrations.event_id = events.id
          WHERE registrations.user_id = '$user_id'
          ORDER BY registrations.registration_date DESC";

$result = mysqli_query($conn, $query);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4">
            <h2 class="mt-4 fw-bold text-primary"><i class="bi bi-ticket-perforated me-2"></i>Tiket & Riwayat Saya</h2>
            <p class="text-muted">Daftar semua event yang telah Anda daftarkan.</p>
            <hr>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Event Info</th>
                                        <th>Jadwal</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($row['image'])): ?>
                                                        <img src="uploads/<?= $row['image']; ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                            <i class="bi bi-calendar-event text-muted"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold"><?= htmlspecialchars($row['title']); ?></div>
                                                        <small class="text-muted"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($row['location']); ?></small>
                                                    </div>
                                                </div>
                                            </td>

                                            <td>
                                                <small>
                                                    <div class="fw-bold"><?= date('d M Y', strtotime($row['event_date'])); ?></div>
                                                    <div><?= $row['start_time']; ?> - <?= $row['end_time']; ?> WIB</div>
                                                </small>
                                            </td>

                                            <td>
                                                <?php
                                                if ($row['reg_status'] == 'confirmed') {
                                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Tiket Aktif</span>';
                                                } elseif ($row['reg_status'] == 'rejected') {
                                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark">Menunggu Verifikasi</span>';
                                                }
                                                ?>
                                            </td>

                                            <td>
                                                <div class="d-flex gap-2">
                                                    <a href="detail_event.php?id=<?= $row['event_id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        Detail
                                                    </a>
                                                    
                                                    <?php if ($row['reg_status'] == 'confirmed'): ?>
                                                        <a href="cetak_tiket.php?reg_id=<?= $row['reg_id']; ?>" target="_blank" class="btn btn-sm btn-success">
                                                            <i class="bi bi-printer"></i> Print
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-ticket-detailed display-4 text-muted opacity-50"></i>
                            <h5 class="text-muted mt-3">Anda belum memiliki tiket.</h5>
                            <a href="dashboard.php" class="btn btn-primary mt-2">Cari Event</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>