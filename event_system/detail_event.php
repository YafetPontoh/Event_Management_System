<?php
session_start();
include 'config.php';

// 1. Ambil ID Event
$id = isset($_GET['id']) ? $_GET['id'] : '';
if(empty($id)) {
    header("Location: index.php"); exit;
}

// 2. Query Event
$query_event = mysqli_query($conn, "SELECT * FROM events WHERE id = '$id'");
$event = mysqli_fetch_assoc($query_event);

if(!$event) {
    echo "<script>alert('Event tidak ditemukan!'); window.location='index.php';</script>"; exit;
}

// 3. Setup User & Role (GANTI NAMA VARIABEL BIAR AMAN)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
// Ganti $role jadi $access_role supaya tidak bentrok dengan sidebar.php
$access_role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : 'guest'; 

// 4. Cek Owner
$status_event = strtolower($event['status']);
$is_owner = ($user_id && $event['created_by'] == $user_id);

// 5. Cek Registrasi
$is_registered = false;
$status_pendaftaran = "";

if ($user_id) { 
    $cek_daftar = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id = '$user_id' AND event_id = '$id'");
    if (mysqli_num_rows($cek_daftar) > 0) {
        $is_registered = true;
        $data_reg = mysqli_fetch_assoc($cek_daftar);
        $status_pendaftaran = $data_reg['status'];
    }
}

// 6. Ambil Data Peserta
$query_peserta = "SELECT registrations.id as reg_id, users.name, users.email, registrations.status, registrations.registration_date
                  FROM registrations 
                  JOIN users ON registrations.user_id = users.id 
                  WHERE registrations.event_id = '$id'
                  ORDER BY registrations.registration_date DESC";
$result_peserta = mysqli_query($conn, $query_peserta);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4 pb-5">
            <a href="<?= ($access_role == 'guest') ? 'index.php' : 'dashboard.php'; ?>" class="btn btn-sm btn-outline-secondary mb-3 mt-3">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>

            <?php 
                $is_rejected = ($status_event != 'pending' && $status_event != 'approved');
            ?>
            <?php if($is_rejected && ($access_role == 'admin' || ($access_role == 'panitia' && $is_owner))): ?>
                <div class="alert alert-danger shadow-sm border-0 mb-4">
                    <h4 class="alert-heading fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Status: Ditolak</h4>
                    <p>Event ini telah ditolak. Silakan perbaiki dan ajukan ulang.</p>
                    <hr>
                    <strong>Alasan Penolakan:</strong>
                    <div class="bg-white p-2 rounded mt-2 text-danger border border-danger">
                        <?= !empty($event['rejection_note']) ? nl2br(htmlspecialchars($event['rejection_note'])) : '-'; ?>
                    </div>
                    <div class="mt-3">
                        <a href="edit_event.php?id=<?= $id; ?>" class="btn btn-danger btn-sm fw-bold">Revisi Event</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <?php if(!empty($event['image'])): ?>
                            <img src="uploads/<?= $event['image']; ?>" class="card-img-top" style="max-height: 300px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-light text-center py-5"><i class="bi bi-image display-1 opacity-25"></i></div>
                        <?php endif; ?>

                        <div class="card-body">
                            <div class="text-center mb-3">
                                <?php 
                                    if($status_event == 'pending') echo '<span class="badge bg-warning text-dark">Menunggu Review</span>';
                                    elseif($status_event == 'approved') {
                                        if($event['quota'] <= 0) echo '<span class="badge bg-danger">Full Booked</span>';
                                        else echo '<span class="badge bg-success">Open Registration</span>';
                                    }
                                    else echo '<span class="badge bg-danger">Ditolak</span>';
                                ?>
                            </div>

                            <h3 class="fw-bold text-center"><?= htmlspecialchars($event['title']); ?></h3>
                            <hr>
                            
                            <p class="mb-2"><i class="bi bi-calendar3 text-primary me-2"></i> <?= date('d M Y', strtotime($event['event_date'])); ?></p>
                            <p class="mb-2"><i class="bi bi-clock text-primary me-2"></i> <?= $event['start_time']; ?> - <?= $event['end_time']; ?></p>
                            <p class="mb-2"><i class="bi bi-geo-alt text-danger me-2"></i> <?= htmlspecialchars($event['location']); ?></p>
                            <p class="mb-2"><i class="bi bi-people text-success me-2"></i> Sisa Kuota: <strong><?= $event['quota']; ?></strong></p>
                            
                            <div class="mt-3 p-3 bg-light rounded small mb-4">
                                <strong>Deskripsi:</strong><br>
                                <?= nl2br(htmlspecialchars($event['description'])); ?>
                            </div>

                            <div class="d-grid gap-2">
                                <?php 
                                // Update Logic: Gunakan $access_role
                                if($access_role == 'admin' || ($access_role == 'panitia' && $is_owner)): 
                                ?>
                                    <a href="edit_event.php?id=<?= $id; ?>" class="btn btn-warning text-white fw-bold">
                                        <i class="bi bi-pencil"></i> Edit Event
                                    </a>

                                <?php else: ?>
                                    <?php if ($is_registered): ?>
                                        <button class="btn btn-secondary" disabled>Status: <?= strtoupper($status_pendaftaran); ?></button>
                                    
                                    <?php elseif ($status_event == 'approved' && $event['quota'] > 0): ?>
                                        <?php if ($access_role == 'guest'): ?>
                                            <a href="login.php?redirect=detail_event.php?id=<?= $id; ?>" class="btn btn-primary fw-bold">Login untuk Daftar</a>
                                        <?php else: ?>
                                            <a href="daftar_event.php?event_id=<?= $id; ?>" class="btn btn-primary fw-bold" onclick="return confirm('Yakin ingin mendaftar?')">Daftar Sekarang</a>
                                        <?php endif; ?>

                                    <?php else: ?>
                                        <button class="btn btn-light border text-muted" disabled>Pendaftaran Tutup</button>
                                    <?php endif; ?>

                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <?php 
                    // Update Logic: Gunakan $access_role
                    if ($access_role == 'admin' || ($access_role == 'panitia' && $is_owner)): 
                    ?>
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary">Kelola Peserta</h6>
                                <a href="cetak_peserta.php?id=<?= $id; ?>" target="_blank" class="btn btn-sm btn-success"><i class="bi bi-printer"></i> PDF</a>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Peserta</th>
                                                <th>Status</th>
                                                <th>Aksi Verifikasi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(mysqli_num_rows($result_peserta) > 0): ?>
                                                <?php $no = 1; while($p = mysqli_fetch_assoc($result_peserta)): ?>
                                                <tr>
                                                    <td><?= $no++; ?></td>
                                                    <td>
                                                        <div class="fw-bold"><?= htmlspecialchars($p['name']); ?></div>
                                                        <small class="text-muted"><?= htmlspecialchars($p['email']); ?></small>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                            if($p['status'] == 'confirmed') echo "<span class='badge bg-success'>Diterima</span>";
                                                            elseif($p['status'] == 'rejected') echo "<span class='badge bg-danger'>Ditolak</span>";
                                                            else echo "<span class='badge bg-warning text-dark'>Menunggu</span>";
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <a href="verifikasi.php?reg_id=<?= $p['reg_id']; ?>&status=confirmed&event_id=<?= $id; ?>" 
                                                           class="btn btn-sm btn-success" title="Terima">
                                                           <i class="bi bi-check-lg"></i>
                                                        </a>
                                                        <a href="verifikasi.php?reg_id=<?= $p['reg_id']; ?>&status=rejected&event_id=<?= $id; ?>" 
                                                           class="btn btn-sm btn-danger ms-1" title="Tolak" onclick="return confirm('Yakin tolak peserta ini?')">
                                                           <i class="bi bi-x-lg"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="4" class="text-center py-4 text-muted">Belum ada peserta mendaftar.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        
                        <div class="alert alert-info border-0 shadow-sm">
                            <div class="d-flex">
                                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                <div>
                                    <h5>Informasi Pendaftaran</h5>
                                    <p class="mb-0">
                                        Klik tombol <strong>"Daftar Sekarang"</strong> di sebelah kiri untuk mengikuti acara ini.<br>
                                        Setelah mendaftar, status Anda akan diperiksa oleh panitia.
                                    </p>
                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>