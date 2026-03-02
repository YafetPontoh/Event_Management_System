<?php
session_start();
include 'config.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit; 
}

$uid = $_SESSION['user_id'];

// 2. NORMALISASI ROLE (ANTI CASE-SENSITIVE)
// Ini bagian terpenting:
// "ADMIN" -> "admin"
// " Panitia " -> "panitia"
// Kode ini memaksa role jadi huruf kecil dan tanpa spasi
$access_role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : '';

// Debugging Sementara (Kalau masih error, uncomment baris bawah ini untuk lihat role asli)
// echo "Role Anda terbaca sebagai: " . $access_role; exit;

// --- LOGIKA VALIDASI (KHUSUS ADMIN) ---
if ($access_role == 'admin') {
    
    // LOGIKA 1: APPROVE (TERIMA EVENT)
    if (isset($_GET['approve_id'])) {
        $eid = mysqli_real_escape_string($conn, $_GET['approve_id']);
        
        // Update status jadi 'approved' dan kosongkan rejection_note
        $query_approve = "UPDATE events SET status='approved', rejection_note=NULL WHERE id='$eid'";
        
        if (mysqli_query($conn, $query_approve)) {
            echo "<script>alert('SUKSES! Event berhasil divalidasi dan tayang.'); window.location='dashboard.php';</script>";
            exit; // Stop script biar ga lanjut load page
        } else {
            // Tampilkan error database jika gagal
            $error = mysqli_error($conn);
            echo "<script>alert('GAGAL VALIDASI! Error Database: $error'); window.location='dashboard.php';</script>";
            exit;
        }
    }

    // LOGIKA 2: REJECT (TOLAK EVENT)
    if (isset($_POST['reject_event'])) {
        $eid = mysqli_real_escape_string($conn, $_POST['event_id']);
        $reason = mysqli_real_escape_string($conn, $_POST['rejection_note']);
        
        $query_reject = "UPDATE events SET status='rejected', rejection_note='$reason' WHERE id='$eid'";
        
        if (mysqli_query($conn, $query_reject)) {
            echo "<script>alert('Event berhasil ditolak.'); window.location='dashboard.php';</script>";
            exit;
        } else {
            $error = mysqli_error($conn);
            echo "<script>alert('GAGAL MENOLAK! Error Database: $error'); window.location='dashboard.php';</script>";
            exit;
        }
    }
}

// --- LOGIKA QUERY DATA EVENT ---
if ($access_role == 'admin') {
    // Admin lihat SEMUA event
    // Urutan: Pending (paling atas) -> Approved -> Rejected
    $query = "SELECT events.*, users.name as panitia_name FROM events 
              LEFT JOIN users ON events.created_by = users.id 
              ORDER BY FIELD(status, 'pending', 'approved', 'rejected'), event_date DESC";
    
    // Hitung total semua pendaftar
    $q_count = mysqli_query($conn, "SELECT id FROM registrations");
    $count_reg = mysqli_num_rows($q_count);

} elseif ($access_role == 'panitia') {
    // Panitia CUMA lihat event buatannya sendiri
    $query = "SELECT * FROM events WHERE created_by = '$uid' ORDER BY event_date DESC";
    
    // Hitung total pendaftar (Hanya event milik dia)
    $query_count = "SELECT r.id FROM registrations r JOIN events e ON r.event_id = e.id WHERE e.created_by = '$uid'";
    $q_count = mysqli_query($conn, $query_count);
    $count_reg = mysqli_num_rows($q_count);

} else {
    // Participant / Guest / Role Lain -> Lempar ke tiket saya
    // Jika user "ADMIN" tapi script gagal baca, dia akan terlempar ke sini.
    // Tapi karena sudah ada strtolower() di atas, harusnya aman.
    header("Location: tiket_saya.php"); 
    exit;
}

$result = mysqli_query($conn, $query);
$total_event = mysqli_num_rows($result);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4">
            <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
                <h2 class="fs-2 fw-bold text-dark">Dashboard</h2>
                <span class="badge bg-secondary">Role: <?= htmlspecialchars($access_role); ?></span>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card bg-primary text-white h-100 shadow-sm border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase mb-2 opacity-75">Event Dikelola</h6>
                                <span class="display-4 fw-bold"><?= $total_event; ?></span>
                            </div>
                            <i class="bi bi-calendar-check display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card bg-success text-white h-100 shadow-sm border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-uppercase mb-2 opacity-75">Total Pendaftar</h6>
                                <span class="display-4 fw-bold"><?= $count_reg; ?></span>
                            </div>
                            <i class="bi bi-people display-4 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="bi bi-table me-2 text-primary"></i>Daftar Pengajuan & Event
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Event</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($total_event > 0) : ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)) : 
                                        // Normalisasi status dari database biar aman
                                        $status_event = strtolower(trim($row['status']));
                                    ?>
                                    
                                    <tr class="<?= ($status_event == 'pending' && $access_role == 'admin') ? 'table-warning' : ''; ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($row['image'])): ?>
                                                    <img src="uploads/<?= $row['image']; ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center text-muted" style="width: 50px; height: 50px;"><i class="bi bi-image"></i></div>
                                                <?php endif; ?>
                                                <div>
                                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['title']); ?></div>
                                                    <small class="text-muted">
                                                        Oleh: <?= ($access_role == 'admin') ? (isset($row['panitia_name']) ? $row['panitia_name'] : 'Admin') : 'Anda'; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?= date('d M Y', strtotime($row['event_date'])); ?><br>
                                            <small class="text-muted"><?= htmlspecialchars($row['location']); ?></small>
                                        </td>
                                        <td>
                                            <?php 
                                                // Logika Badge Status
                                                if($status_event == 'pending') {
                                                    echo '<span class="badge bg-warning text-dark">Menunggu Validasi</span>';
                                                } elseif($status_event == 'approved') {
                                                    echo '<span class="badge bg-success">Tayang (Approved)</span>';
                                                } else {
                                                    // Status Rejected / Ditolak
                                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                                    if(!empty($row['rejection_note'])) {
                                                        echo '<br><a href="#" class="small text-danger text-decoration-underline" data-bs-toggle="modal" data-bs-target="#reasonModal'.$row['id'].'">Lihat Catatan</a>';
                                                    }
                                                }
                                            ?>

                                            <div class="modal fade" id="reasonModal<?= $row['id']; ?>" tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold">Catatan Penolakan</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger bg-light p-3 rounded border">
                                                                <?= nl2br(htmlspecialchars($row['rejection_note'])); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="detail_event.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary" title="Lihat Detail"><i class="bi bi-eye"></i></a>

                                                <?php 
                                                    // Logic Edit:
                                                    $bisa_edit = false;
                                                    
                                                    // Admin selalu bisa edit
                                                    if ($access_role == 'admin') {
                                                        $bisa_edit = true;
                                                    } 
                                                    // Panitia bisa edit KECUALI jika sudah Approved
                                                    elseif ($access_role == 'panitia' && $status_event != 'approved') {
                                                        $bisa_edit = true;
                                                    }
                                                ?>

                                                <?php if($bisa_edit): ?>
                                                    <a href="edit_event.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning text-white" title="Edit / Revisi">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    
                                                    <a href="hapus_event.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus event ini?')" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if($access_role == 'admin' && $status_event == 'pending'): ?>
                                                    <a href="dashboard.php?approve_id=<?= $row['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Setujui Event ini supaya tayang?')"><i class="bi bi-check-lg"></i></a>
                                                    
                                                    <button type="button" class="btn btn-sm btn-danger btn-reject" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#rejectModal" 
                                                            data-id="<?= $row['id']; ?>"
                                                            data-title="<?= htmlspecialchars($row['title']); ?>">
                                                        <i class="bi bi-x-lg"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr><td colspan="4" class="text-center py-5">Belum ada data event.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> 
    </div> 
</div> 

<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">Validasi: Tolak Pengajuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="event_id" id="reject_event_id">
                    <p>Anda akan menolak event: <strong id="reject_event_title"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Penolakan (Wajib)</label>
                        <textarea name="rejection_note" class="form-control" rows="3" placeholder="Tulis alasan kenapa ditolak..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="reject_event" class="btn btn-danger fw-bold">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var rejectButtons = document.querySelectorAll('.btn-reject');
        rejectButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                document.getElementById('reject_event_id').value = this.getAttribute('data-id');
                document.getElementById('reject_event_title').textContent = this.getAttribute('data-title');
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>