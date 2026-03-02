<?php
session_start();
include 'config.php';

// Jika user sudah login, lempar ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $row['role'];
            
            // Redirect ke Dashboard
            header("Location: dashboard.php");
            exit;
        }
    }
    $error = true;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EventApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center min-vh-100 bg-light">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="text-center mb-4">
                    <a href="index.php" class="text-decoration-none">
                        <h3 class="fw-bold text-primary"><i class="bi bi-calendar-event-fill me-2"></i>EventApp</h3>
                    </a>
                </div>

                <div class="card card-login p-4">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold">Selamat Datang</h4>
                            <p class="text-muted small">Masuk untuk mengelola event Anda</p>
                        </div>

                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <div>Email atau Password salah!</div>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Email</label>
                                <input type="email" name="email" class="form-control form-control-custom" placeholder="nama@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold small">Password</label>
                                <input type="password" name="password" class="form-control form-control-custom" placeholder="******" required>
                            </div>

                            <button type="submit" name="login" class="btn btn-primary w-100 fw-bold mt-2 btn-primary-custom">Masuk</button>
                        </form>

                        <div class="text-center mt-3">
                            <div class="text-muted small mb-2">Atau</div>
                            <a href="index.php" class="btn btn-outline-secondary w-100 fw-bold rounded-3">
                                <i class="bi bi-eye me-2"></i>Masuk sebagai Tamu
                            </a>
                        </div>

                        <div class="text-center mt-4">
                            <span class="text-muted small">Belum punya akun?</span>
                            <a href="register.php" class="text-decoration-none fw-bold">Daftar disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>