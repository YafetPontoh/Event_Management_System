<?php
session_start();
include 'config.php';

$where_access = "";
if (!isset($_SESSION['role']) || $_SESSION['role'] == 'guest') {
    $where_access = "AND access_type = 'public'";
}

$search_query = "";
if (isset($_GET['q'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['q']);
    $search_query = "AND (title LIKE '%$keyword%' OR location LIKE '%$keyword%')";
}

$today = date('Y-m-d');

$query_upcoming = "SELECT * FROM events 
                   WHERE event_date >= '$today' 
                   $where_access 
                   $search_query 
                   ORDER BY event_date ASC, start_time ASC";
$res_upcoming = mysqli_query($conn, $query_upcoming);

$query_past = "SELECT * FROM events 
               WHERE event_date < '$today' 
               $where_access 
               $search_query 
               ORDER BY event_date DESC, start_time ASC";
$res_past = mysqli_query($conn, $query_past);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex" id="wrapper">
    <?php include 'includes/sidebar.php'; ?>

    <div id="page-content-wrapper" class="w-100">
        <?php include 'includes/topbar.php'; ?>

        <div class="container-fluid px-4 pb-5">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
                <h2 class="fs-2 fw-bold text-dark mb-3 mb-md-0">Agenda Kegiatan</h2>
                
                <form method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Cari nama acara..." value="<?= isset($_GET['q']) ? $_GET['q'] : '' ?>">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>

            <ul class="nav nav-tabs mb-4" id="scheduleTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab">
                        <i class="bi bi-calendar-check me-2"></i>Akan Datang
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-secondary fw-bold" id="past-tab" data-bs-toggle="tab" data-bs-target="#past" type="button" role="tab">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Selesai
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="scheduleTabContent">
                
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel">
                    <?php if (mysqli_num_rows($res_upcoming) > 0): ?>
                        <div class="row g-3">
                            <?php while ($row = mysqli_fetch_assoc($res_upcoming)): ?>
                                <?php include 'includes/card_agenda.php'; ?> 
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x display-4"></i>
                            <p class="mt-2">Tidak ada acara mendatang.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="tab-pane fade" id="past" role="tabpanel">
                    <?php if (mysqli_num_rows($res_past) > 0): ?>
                        <div class="row g-3">
                            <?php while ($row = mysqli_fetch_assoc($res_past)): ?>
                                <?php include 'includes/card_agenda.php'; ?>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <p>Belum ada riwayat acara.</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>