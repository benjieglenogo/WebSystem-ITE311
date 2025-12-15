<?php
echo view('templates/header');
?>

<!-- Gradebook Header -->
<div class="page-header mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-0">
                    <i class="bi bi-graph-up"></i> Gradebook
                </h1>
            </div>
            <div class="col-auto">
                <span class="badge bg-info">
                    <?php echo count($courses); ?> Course<?php echo count($courses) !== 1 ? 's' : ''; ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container-fluid p-4">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> 
            <?php echo session()->getFlashdata('error'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> 
            <?php echo session()->getFlashdata('success'); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search/Filter Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" id="courseSearch" 
                       placeholder="Search courses by name or code...">
            </div>
        </div>
        <div class="col-md-6 text-end">
            <a href="<?php echo base_url('courses'); ?>" class="btn btn-outline-primary">
                <i class="bi bi-plus-circle"></i> Manage Courses
            </a>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="row g-4">
        <?php if (empty($courses)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-3 mb-0">No courses available. 
                        <?php if ($userRole === 'teacher'): ?>
                            Contact an administrator to assign courses to you.
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-md-6 col-lg-4 course-card">
                    <div class="card h-100 border-0 shadow-sm">
                        <!-- Card Header -->
                        <div class="card-header bg-light border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-subtitle text-muted mb-1 small">
                                        <?php echo htmlspecialchars($course['code']); ?>
                                    </h6>
                                    <h5 class="card-title mb-0">
                                        <?php echo htmlspecialchars($course['name']); ?>
                                    </h5>
                                </div>
                                <span class="badge bg-<?php echo ($course['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                    <?php echo ucfirst($course['status']); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Course Info -->
                            <div class="mb-3">
                                <p class="card-text text-muted small mb-2">
                                    <i class="bi bi-calendar"></i>
                                    <strong><?php echo htmlspecialchars($course['school_year']); ?></strong> - 
                                    <?php echo htmlspecialchars($course['semester']); ?>
                                </p>
                                <?php if ($userRole === 'admin'): ?>
                                    <p class="card-text text-muted small mb-0">
                                        <i class="bi bi-person"></i>
                                        Teacher: <strong><?php echo htmlspecialchars($course['teacher']); ?></strong>
                                    </p>
                                <?php endif; ?>
                            </div>

                            <!-- Stats -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="text-primary fw-bold">
                                            <?php echo $course['student_count']; ?>
                                        </div>
                                        <small class="text-muted">Students</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="text-success fw-bold">
                                            <?php echo $course['graded_count']; ?>/<?php echo $course['student_count']; ?>
                                        </div>
                                        <small class="text-muted">Graded</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <?php 
                            $gradeProgress = ($course['student_count'] > 0) 
                                ? round(($course['graded_count'] / $course['student_count']) * 100) 
                                : 0;
                            ?>
                            <div class="mt-3">
                                <small class="text-muted d-block mb-1">Grading Progress</small>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: <?php echo $gradeProgress; ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-top">
                            <a href="<?php echo base_url('gradebook/course/' . $course['id']); ?>" 
                               class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-graph-up"></i> Open Gradebook
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Styles and Scripts -->
<style>
    .course-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .course-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .card {
        border-radius: 8px;
        overflow: hidden;
    }

    .card-header {
        padding: 1rem;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -16px -16px 0 -16px;
    }

    .page-header h1 {
        color: white;
    }

    .page-header .badge {
        background-color: rgba(255, 255, 255, 0.3) !important;
        color: white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Course search functionality
        const searchInput = document.getElementById('courseSearch');
        const courseCards = document.querySelectorAll('.course-card');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                courseCards.forEach(card => {
                    const courseName = card.querySelector('.card-title').textContent.toLowerCase();
                    const courseCode = card.querySelector('.card-subtitle').textContent.toLowerCase();

                    if (courseName.includes(searchTerm) || courseCode.includes(searchTerm)) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });
</script>

<?php
echo view('templates/footer');
?>
