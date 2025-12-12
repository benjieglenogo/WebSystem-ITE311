<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Course Materials for <?= esc($course_name) ?></h2>
            <?php if (session('userRole') === 'admin' || session('userRole') === 'teacher'): ?>
                <a href="<?= base_url('admin/course/' . $course_id . '/upload') ?>" class="btn btn-success">
                    <i class="bi bi-upload"></i> Upload New Material
                </a>
            <?php endif; ?>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($materials)): ?>
            <div class="row">
                <?php foreach ($materials as $material): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <?php
                                        $fileType = $material['file_type'] ?? pathinfo($material['file_name'], PATHINFO_EXTENSION);
                                        $iconClass = 'bi bi-file-earmark';
                                        $iconColor = 'text-primary';

                                        switch (strtolower($fileType)) {
                                            case 'pdf': $iconClass = 'bi bi-filetype-pdf'; $iconColor = 'text-danger'; break;
                                            case 'doc':
                                            case 'docx': $iconClass = 'bi bi-filetype-docx'; $iconColor = 'text-primary'; break;
                                            case 'ppt':
                                            case 'pptx': $iconClass = 'bi bi-filetype-pptx'; $iconColor = 'text-warning'; break;
                                            case 'zip':
                                            case 'rar': $iconClass = 'bi bi-filetype-zip'; $iconColor = 'text-info'; break;
                                            case 'jpg':
                                            case 'jpeg':
                                            case 'png': $iconClass = 'bi bi-filetype-img'; $iconColor = 'text-success'; break;
                                            case 'txt': $iconClass = 'bi bi-filetype-txt'; $iconColor = 'text-secondary'; break;
                                        }
                                        ?>
                                        <i class="<?= $iconClass ?> fs-2 <?= $iconColor ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="card-title mb-1"><?= esc($material['file_name']) ?></h5>
                                        <p class="card-text text-muted small mb-2">
                                            <?= strtoupper($fileType) ?> •
                                            <?= format_bytes($material['file_size'] ?? 0) ?> •
                                            Uploaded: <?= esc(date('F j, Y H:i', strtotime($material['created_at']))) ?>
                                        </p>
                                        <?php if (!empty($material['description'])): ?>
                                            <p class="card-text text-muted small">
                                                <?= esc($material['description']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('materials/download/' . $material['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Download
                                    </a>
                                    <?php if (session('userRole') === 'admin' || session('userRole') === 'teacher'): ?>
                                        <a href="<?= base_url('materials/delete/' . $material['id']) ?>"
                                           class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Are you sure you want to delete this material?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="bi bi-folder fs-3 mb-2"></i>
                <p class="mb-0">No materials available for this course yet.</p>
                <?php if (session('userRole') === 'admin' || session('userRole') === 'teacher'): ?>
                    <p class="mb-0">Click the "Upload New Material" button to add course materials.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
<?= $this->endSection() ?>
