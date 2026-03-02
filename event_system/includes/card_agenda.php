<?php
// Format Tanggal Cantik
$tgl = date('d', strtotime($row['event_date']));
$bln = date('M', strtotime($row['event_date']));
$thn = date('Y', strtotime($row['event_date']));
?>

<div class="col-12">
    <div class="card shadow-sm border-0 hover-effect h-100">
        <div class="card-body p-0 d-flex align-items-stretch">
            
            <div class="bg-primary text-white p-3 d-flex flex-column align-items-center justify-content-center rounded-start" style="min-width: 100px;">
                <span class="h2 fw-bold mb-0"><?= $tgl; ?></span>
                <span class="text-uppercase small fw-bold"><?= $bln; ?></span>
                <span class="small opacity-75"><?= $thn; ?></span>
            </div>

            <div class="p-3 flex-grow-1 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 80%;">
                        <?= htmlspecialchars($row['title']); ?>
                    </h5>
                    
                    <?php if(isset($row['access_type']) && $row['access_type'] == 'internal'): ?>
                        <span class="badge bg-warning text-dark" style="font-size: 0.7em;">Internal</span>
                    <?php endif; ?>
                </div>

                <div class="text-muted small mb-2">
                    <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($row['start_time'])); ?> - <?= date('H:i', strtotime($row['end_time'])); ?> WIB
                    <span class="mx-2">|</span>
                    <i class="bi bi-geo-alt me-1"></i> <?= htmlspecialchars($row['location']); ?>
                </div>

                <p class="text-secondary small mb-0 text-truncate">
                    <?= substr(htmlspecialchars($row['description']), 0, 100); ?>...
                </p>
            </div>

            <div class="p-3 d-none d-md-flex align-items-center border-start bg-light rounded-end">
                <a href="detail_event.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-outline-primary fw-bold text-nowrap">
                    Detail <i class="bi bi-chevron-right"></i>
                </a>
            </div>

        </div>
        
        <a href="detail_event.php?id=<?= $row['id']; ?>" class="stretched-link d-md-none"></a>
    </div>
</div>