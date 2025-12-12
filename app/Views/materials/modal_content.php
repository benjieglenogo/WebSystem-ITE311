<?php
/**
 * Materials modal content for AJAX requests
 * This file contains only the materials list HTML, not the full page template
 */
?>

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
                                    case 'mp4':
                                    case 'avi':
                                    case 'mov':
                                    case 'wmv':
                                    case 'flv':
                                    case 'mkv':
                                    case 'mpg':
                                    case 'mpeg': $iconClass = 'bi bi-film'; $iconColor = 'text-primary'; break;
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
                                <button class="btn btn-sm btn-outline-danger delete-material-btn"
                                       data-material-id="<?= $material['id'] ?>">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
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
            <p class="mb-0">Use the upload form above to add course materials.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
