<?php
include 'config.php';

$id = $_GET['id'];

$event = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM events WHERE id='$id'"));

$peserta = mysqli_query($conn, "SELECT users.name, users.email, registrations.registration_date 
                                FROM registrations 
                                JOIN users ON registrations.user_id = users.id 
                                WHERE registrations.event_id = '$id' AND registrations.status = 'confirmed'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Peserta - <?= $event['title']; ?></title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 8px; text-align: left; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body onload="window.print()"> <div class="header">
        <h2>Laporan Daftar Hadir Peserta</h2>
        <h3>Event: <?= $event['title']; ?></h3>
        <p>Tanggal: <?= $event['event_date']; ?> | Lokasi: <?= $event['location']; ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peserta</th>
                <th>Email</th>
                <th>Tanggal Daftar</th>
                <th>Tanda Tangan</th> </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1; 
            if(mysqli_num_rows($peserta) > 0) {
                while($row = mysqli_fetch_assoc($peserta)): 
            ?>
            <tr>
                <td><?= $no++; ?></td>
                <td><?= $row['name']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['registration_date']; ?></td>
                <td></td>
            </tr>
            <?php 
                endwhile; 
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>Belum ada peserta terkonfirmasi.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>