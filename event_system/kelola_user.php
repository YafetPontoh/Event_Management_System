<?php
session_start();
include 'config.php';

// Cek Admin (Hanya Admin yang boleh masuk sini)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: dashboard.php");
    exit;
}

// --- 1. LOGIKA TAMBAH USER BARU (NEW) ---
if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; 
    $role = $_POST['role'];

    // Cek apakah email sudah ada?
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Gagal! Email sudah terdaftar.'); window.location='kelola_user.php';</script>";
    } else {
        // Enkripsi Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query_insert = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
        
        if (mysqli_query($conn, $query_insert)) {
            echo "<script>alert('User baru berhasil ditambahkan!'); window.location='kelola_user.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}

// --- 2. LOGIKA UPDATE ROLE ---
if (isset($_POST['update_user'])) {
    $uid = $_POST['user_id'];
    $new_role = $_POST['role'];
    
    // Proteksi: Admin tidak boleh mengubah dirinya sendiri
    if ($uid == $_SESSION['user_id']) {
        echo "<script>alert('Dilarang mengubah role akun sendiri!'); window.location='kelola_user.php';</script>";
        exit;
    }

    $query_update = "UPDATE users SET role = '$new_role' WHERE id = '$uid'";
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Role user berhasil diubah!'); window.location='kelola_user.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// --- 3. LOGIKA HAPUS USER ---
if (isset($_GET['delete_id'])) {
    $del_id = $_GET['delete_id'];
    if ($del_id != $_SESSION['user_id']) {
        // Hapus pendaftaran user tersebut dulu
        mysqli_query($conn, "DELETE FROM registrations WHERE user_id = '$del_id'");
        // Hapus user
        mysqli_query($conn, "DELETE FROM users WHERE id = '$del_id'");
        echo "<script>alert('User berhasil dihapus!'); window.location='kelola_user.php';</script>";
    } else {
        echo "<script>alert('Tidak bisa menghapus akun sendiri!'); window.location='kelola_user.php';</script>";
    }
}

// Ambil semua data user
$query = "SELECT * FROM users ORDER BY role ASC, name ASC";
$result = mysqli_query($conn, $query);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
                <h2 class="fs-2 fw-bold mb-0">Manajemen User & Role</h2>
                <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah User
                </button>
            </div>

            <div class="card shadow-sm border-0 mb-5">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role Saat Ini</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['name']); ?></div>
                                        <?php if($row['id'] == $_SESSION['user_id']): ?>
                                            <span class="badge bg-primary" style="font-size: 0.7em;">Akun Anda</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <?php 
                                            if($row['role'] == 'admin') echo '<span class="badge bg-primary">ADMIN</span>';
                                            elseif($row['role'] == 'panitia') echo '<span class="badge bg-success">PANITIA</span>';
                                            elseif($row['role'] == 'participant') echo '<span class="badge bg-warning text-dark">INTERNAL</span>';
                                            else echo '<span class="badge bg-secondary">GUEST</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php if($row['id'] != $_SESSION['user_id']): ?>
                                            <button type="button" class="btn btn-sm btn-info text-white me-1" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>" title="Ubah Role">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="kelola_user.php?delete_id=<?= $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user ini?')" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light border" disabled><i class="bi bi-lock"></i></button>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Ubah Role User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST">
                                                <div class="modal-body">
                                                    <p>User: <strong><?= htmlspecialchars($row['name']); ?></strong></p>
                                                    <input type="hidden" name="user_id" value="<?= $row['id']; ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Pilih Role Baru</label>
                                                        <select name="role" class="form-select" required>
                                                            <option value="guest" <?= ($row['role'] == 'guest') ? 'selected' : ''; ?>>Guest (Masyarakat Umum)</option>
                                                            <option value="participant" <?= ($row['role'] == 'participant') ? 'selected' : ''; ?>>Internal (Mahasiswa/Karyawan)</option>
                                                            <option value="panitia" <?= ($row['role'] == 'panitia') ? 'selected' : ''; ?>>Panitia (Event Organizer)</option>
                                                            <option value="admin" <?= ($row['role'] == 'admin') ? 'selected' : ''; ?>>Admin (Full Akses)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" name="update_user" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" required placeholder="Contoh: Budi Santoso">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="email@contoh.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password Awal</label>
                        <input type="text" name="password" class="form-control" required placeholder="Masukkan password">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Role / Hak Akses</label>
                        <select name="role" class="form-select" required>
                            <option value="guest">Guest (Masyarakat Umum)</option>
                            <option value="participant">Internal (Mahasiswa/Karyawan)</option>
                            <option value="panitia">Panitia (Event Organizer)</option>
                            <option value="admin">Admin (Full Akses)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add_user" class="btn btn-primary fw-bold">Simpan User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>