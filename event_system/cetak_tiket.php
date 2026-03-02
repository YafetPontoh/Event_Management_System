<?php
session_start();
include 'config.php';

// Security Check
if (!isset($_SESSION['user_id']) || !isset($_GET['reg_id'])) { 
    die("Akses ditolak."); 
}

$reg_id = $_GET['reg_id'];
$user_id = $_SESSION['user_id'];

// Ambil Data Tiket Spesifik
$query = "SELECT registrations.*, events.title, events.event_date, events.start_time, events.end_time, events.location, events.image, users.name 
          FROM registrations 
          JOIN events ON registrations.event_id = events.id 
          JOIN users ON registrations.user_id = users.id
          WHERE registrations.id = '$reg_id' AND registrations.user_id = '$user_id' AND registrations.status = 'confirmed'";

$data = mysqli_fetch_assoc(mysqli_query($conn, $query));

if(!$data) { 
    die("Tiket tidak ditemukan atau status belum dikonfirmasi."); 
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Ticket: <?= $data['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .ticket-container { max-width: 800px; margin: 50px auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); display: flex; }
        .ticket-left { flex: 2; padding: 40px; position: relative; border-right: 2px dashed #dee2e6; }
        .ticket-right { flex: 1; background: #f8f9fa; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 20px; text-align: center; }
        
        /* Bulatan pemotong kertas */
        .ticket-left::after, .ticket-left::before { content: ""; position: absolute; height: 30px; width: 30px; background: #f4f6f9; border-radius: 50%; right: -15px; }
        .ticket-left::after { top: -15px; }
        .ticket-left::before { bottom: -15px; }

        @media print {
            body { background: white; }
            .no-print { display: none; }
            .ticket-container { box-shadow: none; border: 1px solid #ddd; margin: 0; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="ticket-container">
        <div class="ticket-left">
            <h6 class="text-uppercase text-muted fw-bold mb-3">OFFICIAL E-TICKET</h6>
            <h2 class="fw-bold text-primary mb-4"><?= htmlspecialchars($data['title']); ?></h2>
            
            <div class="row g-4">
                <div class="col-6">
                    <small class="text-muted d-block mb-1">PEMEGANG TIKET</small>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($data['name']); ?></h5>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block mb-1">LOKASI</small>
                    <h5 class="fw-bold mb-0"><?= htmlspecialchars($data['location']); ?></h5>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block mb-1">TANGGAL</small>
                    <h5 class="fw-bold mb-0"><?= date('d F Y', strtotime($data['event_date'])); ?></h5>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block mb-1">WAKTU</small>
                    <h5 class="fw-bold mb-0"><?= $data['start_time']; ?> WIB</h5>
                </div>
            </div>
        </div>

        <div class="ticket-right">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=REG-<?= $data['id']; ?>" alt="QR Code" class="img-fluid mb-3 border p-2 bg-white rounded">
            <small class="text-muted mb-0">ID REGISTRASI</small>
            <strong class="font-monospace">#<?= $data['id']; ?></strong>
        </div>
    </div>

    <div class="text-center no-print mt-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer me-2" viewBox="0 0 16 16">
              <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
              <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Tiket PDF
        </button>
    </div>
</div>

</body>
</html>