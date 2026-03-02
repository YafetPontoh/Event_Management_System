<?php
session_start();
include 'config.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$uid  = (int) $_SESSION['user_id'];
$role = strtoupper($_SESSION['role']);

// Hanya Admin dan Panitia yang boleh akses
if ($role !== 'ADMIN' && $role !== 'PANITIA') {
    header("Location: dashboard.php");
    exit;
}

// PROSES SUBMIT
if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $date  = $_POST['date'];
    $start = $_POST['start_time'];
    $end   = $_POST['end_time'];
    $loc   = mysqli_real_escape_string($conn, $_POST['location']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $quota = (int) $_POST['quota'];
    $access_type = $_POST['access_type'];

    // LOGIKA STATUS: Admin langsung Approved, Panitia masuk Pending
    $status = ($role === 'ADMIN') ? 'approved' : 'pending';

    // Upload Gambar
    $image = '';
    if (!empty($_FILES['image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $filename = uniqid() . '.' . $ext;
            if(move_uploaded_file($_FILES['image']['tmp_name'], "uploads/$filename")){
                $image = $filename;
            }
        }
    }

    $query = "INSERT INTO events (title, event_date, start_time, end_time, location, description, quota, access_type, created_by, status, image)
              VALUES ('$title','$date','$start','$end','$loc','$desc','$quota','$access_type','$uid','$status', '$image')";

    if(mysqli_query($conn, $query)){
        echo "<script>alert('Event berhasil disimpan!'); window.location='dashboard.php';</script>";
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
            <h2 class="fs-2 fw-bold mb-4">
                <?= ($role === 'ADMIN') ? 'Buat Event Baru' : 'Ajukan Event Baru'; ?>
            </h2>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Judul Event</label>
                                <input type="text" name="title" class="form-control" required placeholder="Contoh: Seminar Teknologi 2025">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tanggal</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Jam Mulai</label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Jam Selesai</label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Lokasi</label>
                            <input type="text" name="location" class="form-control" required placeholder="Contoh: Aula Kampus Lt. 3">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi Event</label>
                            <textarea name="description" class="form-control" rows="5" required></textarea>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Kuota Peserta</label>
                                <input type="number" name="quota" class="form-control" required min="1" value="50">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Tipe Akses</label>
                                <select name="access_type" class="form-select">
                                    <option value="public">Public (Semua Orang)</option>
                                    <option value="internal">Internal (Khusus Mahasiswa/Karyawan)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Upload Flyer/Banner</label>
                                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                                <div class="form-text text-muted">Format: JPG, PNG. Maks 2MB.</div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="dashboard.php" class="btn btn-light border">Batal</a>
                            <button type="submit" name="submit" class="btn btn-primary fw-bold px-4">
                                <i class="bi bi-save me-2"></i>Simpan Event
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>