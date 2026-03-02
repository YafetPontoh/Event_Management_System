<?php
session_start();
include 'config.php';

// CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// CEK ROLE ADMIN
$role = strtoupper(trim($_SESSION['role']));
if ($role !== 'ADMIN') {
    echo "<script>alert('Akses khusus admin!'); window.location='dashboard.php';</script>";
    exit;
}

// AMBIL EVENT PENDING
$query = mysqli_query($conn, "
    SELECT e.*, u.name AS creator_name
    FROM events e
    JOIN users u ON e.created_by = u.id
    WHERE e.status = 'pending'
    ORDER BY e.event_date ASC
");
?>

<?php include 'includes/header.php'; ?>
<div class="d-flex" id="wrapper">
<?php include 'includes/sidebar.php'; ?>

<div id="page-content-wrapper" class="w-100">
<?php include 'includes/topbar.php'; ?>

<div class="container-fluid px-4">
    <h2 class="fw-bold mb-4">Review Pengajuan Event</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Pembuat</th>
                        <th>Akses</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(mysqli_num_rows($query) == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">
                            Tidak ada event menunggu review.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php while($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= $row['event_date']; ?></td>
                        <td><?= htmlspecialchars($row['creator_name']); ?></td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= ucfirst($row['access_type']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="detail_event.php?id=<?= $row['id']; ?>" 
                               class="btn btn-sm btn-info">
                               Detail
                            </a>

                            <a href="approve_event.php?id=<?= $row['id']; ?>" 
                               class="btn btn-sm btn-success"
                               onclick="return confirm('Setujui event ini?')">
                               Setujui
                            </a>

                            <a href="reject_event.php?id=<?= $row['id']; ?>" 
                               class="btn btn-sm btn-danger">
                               Tolak
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
</div>

<?php include 'includes/footer.php'; ?>
