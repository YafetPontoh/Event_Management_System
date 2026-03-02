<div class="col-md-4 mb-4">
    <div class="card h-100 shadow-sm border-0 hover-card">
        <div class="position-relative">
            <?php if(!empty($row['image'])): ?>
                <img src="uploads/<?= $row['image']; ?>" class="card-img-top img-card-custom" alt="<?= htmlspecialchars($row['title']); ?>">
            <?php else: ?>
                <div class="bg-light text-center py-5 text-muted">
                    <i class="bi bi-image display-1 opacity-25"></i>
                </div>
            <?php endif; ?>

            <span class="position-absolute top-0 end-0 badge <?= ($row['access_type'] == 'internal') ? 'bg-warning text-dark' : 'bg-success'; ?> m-2 shadow-sm">
                <?= ($row['access_type'] == 'internal') ? 'Internal' : 'Public'; ?>
            </span>
        </div>

        <div class="card-body d-flex flex-column">
            <div class="text-primary fw-bold small mb-2">
                <i class="bi bi-calendar3 me-1"></i> <?= date('d M Y', strtotime($row['event_date'])); ?>
                <span class="mx-1">•</span>
                <i class="bi bi-clock me-1"></i> <?= date('H:i', strtotime($row['start_time'])); ?>
            </div>

            <h5 class="card-title fw-bold text-dark mb-2">
                <a href="detail_event.php?id=<?= $row['id']; ?>" class="text-decoration-none text-dark stretched-link">
                    <?= htmlspecialchars($row['title']); ?>
                </a>
            </h5>

            <p class="card-text text-muted small mb-3">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= htmlspecialchars($row['location']); ?>
            </p>

            <div class="mt-auto d-flex justify-content-between align-items-center border-top pt-3">
                <small class="text-muted">
                    <i class="bi bi-person-circle me-1"></i>
                    Sisa Kuota: <b><?= $row['quota']; ?></b>
                </small>
                
                <?php if(isset($is_past)): ?>
                    <span class="badge bg-secondary">Selesai</span>
                <?php else: ?>
                    <span class="text-primary fw-bold small">Lihat Detail <i class="bi bi-arrow-right"></i></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>