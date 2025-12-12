<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Upload Course Material</h3>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Upload learning materials for <strong><?= esc($course_name) ?></strong> course.
                            Supported file types: PDF, DOCX, PPTX, ZIP, JPG, PNG.
                        </p>

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

                        <?php if (isset($validation)): ?>
                            <div class="alert alert-warning">
                                <?= $validation->listErrors() ?>
                            </div>
                        <?php endif; ?>

                        <form method="post" enctype="multipart/form-data" action="<?= base_url((session('userRole') === 'admin' ? 'admin' : 'teacher') . '/course/' . $course_id . '/upload') ?>">
                            <div class="mb-3">
                                <label for="material_file" class="form-label">Select File</label>
                                <input type="file" class="form-control" id="material_file" name="material_file" required>
                                <div class="form-text">
                                    Maximum file size: 10MB
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description (Optional)</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                          placeholder="Brief description of the material"></textarea>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary me-md-2">
                                    <i class="bi bi-upload"></i> Upload Material
                                </button>
                                <a href="<?= base_url('materials/course/' . $course_id) ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Back to Materials
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
