<?php
session_start();
include 'config.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id  = (int) $_GET['id'];
$uid = (int) $_SESSION['user_id'];
// Normalisasi Role lagi
$access_role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';

// 2. Ambil Data Event
$query_cek = mysqli_query($conn, "SELECT * FROM events WHERE id = $id");
$data = mysqli_fetch_assoc($query_cek);

if (!$data) {
    echo "<script>alert('Event tidak ditemukan!'); window.location='dashboard.php';</script>";
    exit;
}

// 3. Cek Kepemilikan (Panitia hanya boleh edit miliknya)
if ($access_role !== 'admin' && (int)$data['created_by'] !== $uid) {
    echo "<script>alert('Akses Ditolak! Ini bukan event Anda.'); window.location='dashboard.php';</script>";
    exit;
}

// 4. Cek Status (Panitia tidak boleh edit yang sudah Approved)
if ($access_role !== 'admin' && $data['status'] == 'approved') {
    echo "<script>alert('Event sudah tayang, tidak bisa diedit lagi.'); window.location='dashboard.php';</script>";
    exit;
}

// 5. PROSES UPDATE
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date  = $_POST['date'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];
    $loc   = mysqli_real_escape_string($conn, $_POST['location']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $quota = (int) $_POST['quota'];
    $access_type = $_POST['access_type'];

    // --- LOGIKA REVISI ---
    if ($access_role === 'admin') {
        // Jika Admin, status tidak berubah
        $new_status = $data['status']; 
        $reset_note_sql = "";
    } else {
        // Jika Panitia, status WAJIB 'pending' agar direview ulang
        $new_status = 'pending'; 
        $reset_note_sql = ", rejection_note = NULL"; 
    }

    // Ganti Gambar
    $query_img = "";
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            if(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$filename")){
                if (!empty($data['image']) && file_exists("uploads/".$data['image'])) {
                    unlink("uploads/".$data['image']);
                }
                $query_img = ", image='$filename'";
            }
        }
    }

    $sql = "UPDATE events SET
            title='$title', event_date='$date', start_time='$start', end_time='$end',
            location='$loc', description='$desc', quota=$quota, access_type='$access_type',
            status='$new_status' 
            $query_img 
            $reset_note_sql
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Berhasil disimpan!'); window.location='dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4 pb-5">
            <h2 class="fs-2 fw-bold mb-4">Edit / Revisi Event</h2>

            <?php if($data['status'] == 'rejected'): ?>
            <div class="alert alert-danger shadow-sm">
                <strong>Status: Ditolak.</strong> Silakan revisi data di bawah ini untuk diajukan ulang.
            </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Judul Event</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['title']); ?>" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="date" class="form-control" value="<?= $data['event_date']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Mulai</label>
                                <input type="time" name="start_time" class="form-control" value="<?= $data['start_time']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Selesai</label>
                                <input type="time" name="end_time" class="form-control" value="<?= $data['end_time']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi</label>
                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($data['location']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($data['description']); ?></textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Kuota</label>
                                <input type="number" name="quota" class="form-control" value="<?= $data['quota']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Akses</label>
                                <select name="access_type" class="form-select">
                                    <option value="public" <?= ($data['access_type']=='public')?'selected':''; ?>>Public</option>
                                    <option value="internal" <?= ($data['access_type']=='internal')?'selected':''; ?>>Internal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ganti Banner (Opsional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="dashboard.php" class="btn btn-light border">Batal</a>
                            <button type="submit" name="update" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>