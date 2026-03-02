<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if(mysqli_num_rows($cek_email) > 0){
         $error_msg = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Berhasil daftar! Silakan login.'); window.location='login.php';</script>";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - EventApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center min-vh-100 py-4">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card card-login p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold">Buat Akun Baru</h3>
                            <p class="text-muted">Bergabunglah untuk mengikuti event seru</p>
                        </div>

                        <?php if(isset($error_msg)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= $error_msg; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Nama Lengkap</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person"></i></span>
                                    <input type="text" name="name" class="form-control form-control-custom border-start-0 ps-0" placeholder="John Doe" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control form-control-custom border-start-0 ps-0" placeholder="nama@email.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-key"></i></span>
                                    <input type="password" name="password" class="form-control form-control-custom border-start-0 ps-0" placeholder="Buat password kuat" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Status Peserta</label>
                                <select name="role" class="form-select form-control-custom" required>
                                    <option value="" disabled selected>Pilih Status...</option>
                                    <option value="guest">Masyarakat Umum (Guest)</option>
                                    <option value="participant">Internal (Mahasiswa/Karyawan)</option>
                                </select>
                                <div class="form-text text-muted small mt-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Internal dapat mengakses seluruh event. Guest hanya event publik.
                                </div>
                            </div>

                            <button type="submit" name="register" class="btn btn-success w-100 fw-bold mt-3 py-2 rounded-3">Daftar Sekarang</button>
                        </form>

                        <div class="text-center mt-4">
                            <span class="text-muted small">Sudah punya akun?</span>
                            <a href="login.php" class="text-decoration-none fw-bold">Login disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>