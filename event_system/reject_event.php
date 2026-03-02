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

if (isset($_POST['reject'])) {
    $note = mysqli_real_escape_string($conn, $_POST['note']);

    mysqli_query($conn, "
        UPDATE events 
        SET status='rejected', rejection_note='$note'
        WHERE id=$id
    ");

    echo "<script>alert('Event ditolak'); window.location='review_event.php';</script>";
}
?>

<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h4 class="fw-bold mb-3">Tolak Event</h4>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-bold">Alasan Penolakan</label>
            <textarea name="note" class="form-control" rows="4" required></textarea>
        </div>

        <div class="d-flex gap-2">
            <a href="review_event.php" class="btn btn-light">Batal</a>
            <button type="submit" name="reject" class="btn btn-danger">
                Tolak Event
            </button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
