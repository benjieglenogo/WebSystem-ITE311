<?php
echo view('templates/header');
?>

<!-- Gradebook Header -->
<div class="page-header mb-4">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <a href="<?php echo base_url('gradebook'); ?>" class="text-white text-decoration-none">
                    <i class="bi bi-graph-up"></i> Gradebook
                </a>
                <span class="text-white-50 mx-2">/</span>
                <h1 class="h3 d-inline mb-0">
                    <?php echo htmlspecialchars($course['course_code']); ?> - <?php echo htmlspecialchars($course['course_name']); ?>
                </h1>
            </div>
            <div class="col-auto">
                <button class="btn btn-light btn-sm" id="saveGradesBtn">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
            </div>
        </div>
        <small class="text-white-50">
            <i class="bi bi-calendar"></i> <?php echo htmlspecialchars($course['school_year']); ?> - <?php echo htmlspecialchars($course['semester']); ?>
        </small>
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

    <!-- Statistics Cards -->
    <div class="row mb-4 g-3">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted small mb-1">Total Students</p>
                            <h4 class="mb-0"><?php echo $totalStudents; ?></h4>
                        </div>
                        <div class="text-primary" style="font-size: 2rem;">
                            <i class="bi bi-people"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted small mb-1">Graded</p>
                            <h4 class="mb-0"><?php echo $gradedStudents; ?>/<?php echo $totalStudents; ?></h4>
                        </div>
                        <div class="text-success" style="font-size: 2rem;">
                            <i class="bi bi-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted small mb-1">Pending</p>
                            <h4 class="mb-0"><?php echo $totalStudents - $gradedStudents; ?></h4>
                        </div>
                        <div class="text-warning" style="font-size: 2rem;">
                            <i class="bi bi-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="card-text text-muted small mb-1">Completion</p>
                            <h4 class="mb-0">
                                <?php echo ($totalStudents > 0) ? round(($gradedStudents / $totalStudents) * 100) : 0; ?>%
                            </h4>
                        </div>
                        <div class="text-info" style="font-size: 2rem;">
                            <i class="bi bi-percent"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gradebook Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light border-bottom">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="mb-0"><i class="bi bi-table"></i> Student Grades</h5>
                </div>
                <div class="col-auto">
                    <small class="text-muted">Click on grades to edit</small>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <?php if (empty($grades)): ?>
                <div class="alert alert-info m-4 mb-0">
                    <i class="bi bi-info-circle"></i> No students enrolled in this course yet.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                </th>
                                <th style="width: 200px;">Student Name</th>
                                <th style="width: 250px;">Email</th>
                                <th style="width: 120px;">Current Grade</th>
                                <th style="width: 100px;">Status</th>
                                <th style="width: 150px;">Enrolled Date</th>
                            </tr>
                        </thead>
                        <tbody id="gradesTableBody">
                            <?php foreach ($grades as $index => $grade): ?>
                                <tr class="grade-row" data-enrollment-id="<?php echo $grade['enrollment_id']; ?>">
                                    <td>
                                        <input type="checkbox" class="form-check-input student-checkbox">
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($grade['student_name']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?php echo htmlspecialchars($grade['student_email']); ?></span>
                                    </td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control grade-input" 
                                                   min="0" max="100" step="0.01"
                                                   value="<?php echo htmlspecialchars($grade['current_grade']); ?>"
                                                   placeholder="0-100"
                                                   data-original-value="<?php echo htmlspecialchars($grade['current_grade']); ?>">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo ($grade['status'] === 'active') ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($grade['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M d, Y', strtotime($grade['enrolled_date'])); ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($grades)): ?>
            <div class="card-footer bg-light border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span id="changedCount" class="text-muted small">No changes</span>
                    </div>
                    <div>
                        <button class="btn btn-outline-secondary btn-sm" id="resetBtn">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </button>
                        <button class="btn btn-primary btn-sm" id="saveGradesFooterBtn">
                            <i class="bi bi-check-circle"></i> Save Changes
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Styles -->
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -16px -16px 0 -16px;
    }

    .page-header a {
        opacity: 0.9;
    }

    .page-header a:hover {
        opacity: 1;
    }

    .grade-input {
        border-color: #dee2e6;
    }

    .grade-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .grade-input.changed {
        background-color: #fff3cd;
        border-color: #ffc107;
    }

    .grade-row:hover {
        background-color: #f8f9fa;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>

<!-- Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gradeInputs = document.querySelectorAll('.grade-input');
        const saveBtn = document.getElementById('saveGradesBtn');
        const saveFooterBtn = document.getElementById('saveGradesFooterBtn');
        const resetBtn = document.getElementById('resetBtn');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const changedCountDisplay = document.getElementById('changedCount');

        // Track changes
        const changedGrades = new Map();

        gradeInputs.forEach(input => {
            input.addEventListener('change', function() {
                const enrollmentId = this.closest('tr').dataset.enrollmentId;
                const originalValue = this.dataset.originalValue;
                const currentValue = this.value;

                if (currentValue !== originalValue && currentValue !== '') {
                    changedGrades.set(enrollmentId, currentValue);
                    this.classList.add('changed');
                } else {
                    changedGrades.delete(enrollmentId);
                    this.classList.remove('changed');
                }

                updateChangedCount();
            });
        });

        function updateChangedCount() {
            const count = changedGrades.size;
            if (count > 0) {
                changedCountDisplay.textContent = count + ' grade' + (count !== 1 ? 's' : '') + ' changed';
                changedCountDisplay.classList.remove('text-muted');
                changedCountDisplay.classList.add('text-warning', 'fw-bold');
            } else {
                changedCountDisplay.textContent = 'No changes';
                changedCountDisplay.classList.add('text-muted');
                changedCountDisplay.classList.remove('text-warning', 'fw-bold');
            }
        }

        // Save changes
        function saveGrades() {
            if (changedGrades.size === 0) {
                alert('No changes to save');
                return;
            }

            const gradesData = Object.fromEntries(changedGrades);

            fetch('<?php echo base_url('gradebook/course/' . $course['id'] . '/update'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                },
                body: JSON.stringify({ grades: gradesData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Grades saved successfully! ' + data.updated_count + ' grades updated.');
                    location.reload();
                } else {
                    alert('Error saving grades: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving grades');
            });
        }

        saveBtn.addEventListener('click', saveGrades);
        saveFooterBtn.addEventListener('click', saveGrades);

        // Reset changes
        resetBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to reset all changes?')) {
                gradeInputs.forEach(input => {
                    input.value = input.dataset.originalValue;
                    input.classList.remove('changed');
                });
                changedGrades.clear();
                updateChangedCount();
            }
        });

        // Select all checkbox
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                selectAllCheckbox.checked = Array.from(studentCheckboxes).every(cb => cb.checked);
            });
        });
    });
</script>

<?php
echo view('templates/footer');
?>
