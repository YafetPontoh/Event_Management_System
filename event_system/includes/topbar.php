<nav class="navbar navbar-expand-lg navbar-light bg-white py-3 px-4 shadow-sm mb-4">
    <div class="d-flex align-items-center w-100 justify-content-between">
        
        <button class="btn btn-outline-primary" id="menu-toggle">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="d-flex align-items-center">
            <div class="text-end me-3">
                <div class="fw-bold small"><?= isset($_SESSION['name']) ? $_SESSION['name'] : 'User'; ?></div>
                <div class="text-muted small" style="font-size: 0.8em;"><?= isset($_SESSION['role']) ? strtoupper($_SESSION['role']) : 'GUEST'; ?></div>
            </div>
            <div class="rounded-circle bg-secondary text-white d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                <i class="bi bi-person-fill"></i>
            </div>
        </div>

    </div>
</nav>