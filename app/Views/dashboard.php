<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 text-primary fw-bold">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger rounded-3 px-4">
            Logout
        </a>
    </div>

    <!-- Welcome Alert -->
    <div class="alert alert-success shadow-sm rounded-3" role="alert">
        👋 Welcome, <strong><?= esc(session('userEmail')) ?></strong>!
    </div>

    <!-- Dashboard Card -->
    <div class="card border-0 shadow-lg rounded-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Overview</h5>
            <p class="text-muted mb-0">
                This is a protected page only visible after login.
                You can place your stats, charts, or quick actions here.
            </p>
        </div>
    </div>
<?= $this->endSection() ?>
