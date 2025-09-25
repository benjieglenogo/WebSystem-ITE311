<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0 text-primary fw-bold">Dashboard</h1>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger rounded-3 px-4">Logout</a>
    </div>

    <div class="alert alert-info shadow-sm rounded-3" role="alert">
        Welcome, <strong><?= esc(session('email')) ?></strong>!
    </div>

    <?php $role = session('role'); ?>

    <?php if ($role === 'admin'): ?>
        <div class="card border-0 shadow-lg rounded-4 mb-3">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Admin Overview</h5>
                <ul class="mb-0">
                    <li>Manage users</li>
                    <li>View system reports</li>
                    <li>Configure site settings</li>
                </ul>
            </div>
        </div>
    <?php elseif ($role === 'teacher'): ?>
        <div class="card border-0 shadow-lg rounded-4 mb-3">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Teacher Tools</h5>
                <ul class="mb-0">
                    <li>My classes</li>
                    <li>Assignments</li>
                    <li>Gradebook</li>
                </ul>
            </div>
        </div>
    <?php elseif ($role === 'student'): ?>
        <div class="card border-0 shadow-lg rounded-4 mb-3">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Student Area</h5>
                <ul class="mb-0">
                    <li>My courses</li>
                    <li>Assignments</li>
                    <li>Announcements</li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="card border-0 shadow-lg rounded-4 mb-3">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">General</h5>
                <p class="mb-0">Your role is not set. Please contact an administrator.</p>
            </div>
        </div>
    <?php endif; ?>
<?= $this->endSection() ?>


