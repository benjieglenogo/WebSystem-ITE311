<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container mt-5">
    <h1>Welcome, Admin!</h1>

    <div class="card border-0 shadow-lg rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-semibold mb-3">Manage Course Materials</h5>
            <?php if (!empty($courses)): ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="card-title fw-semibold"><?php echo esc($course['course_name']); ?></h6>
                                    <p class="card-text text-muted"><?php echo esc($course['description'] ?? 'No description available'); ?></p>
                                    <a href="<?php echo base_url('admin/course/' . $course['id'] . '/upload'); ?>" class="btn btn-primary">Upload Materials</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted">No courses available.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
