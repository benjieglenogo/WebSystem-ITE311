<?= $this->extend('teacher/layout') ?>

<?= $this->section('dashboard_content') ?>
<?php $activeTab = 'enrollment'; ?>

<!-- Enrollment Management Dashboard -->
<div class="container-fluid p-4">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="container-fluid">
            <h1 class="h3 mb-0">
                <i class="bi bi-person-check"></i> Enrollment Requests Dashboard
            </h1>
        </div>
    </div>

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

    <!-- Tabs Navigation -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-tabs" id="enrollmentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-content" type="button" role="tab" aria-controls="pending-content" aria-selected="true">
                        <i class="bi bi-hourglass-split"></i> Pending Requests
                        <span class="badge bg-warning ms-2"><?php echo count($pendingRequests ?? []); ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="approved-tab" data-bs-toggle="tab" data-bs-target="#approved-content" type="button" role="tab" aria-controls="approved-content" aria-selected="false">
                        <i class="bi bi-check-circle"></i> Enrolled Students
                        <span class="badge bg-success ms-2"><?php echo count($approvedEnrollments ?? []); ?></span>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="enrollmentTabsContent">
        <!-- Pending Requests Tab -->
        <div class="tab-pane fade show active" id="pending-content" role="tabpanel" aria-labelledby="pending-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pending Enrollment Requests</h5>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($pendingRequests)): ?>
                        <div class="alert alert-info m-4 mb-0">
                            <i class="bi bi-info-circle"></i> No pending enrollment requests at this time.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Request Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $request): ?>
                                        <tr data-enrollment-id="<?php echo $request['id']; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($request['student_name']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?php echo htmlspecialchars($request['student_email']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    <?php echo htmlspecialchars($request['course_code']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($request['course_name']); ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y H:i', strtotime($request['enrollment_date'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-success approve-btn" data-enrollment-id="<?php echo $request['id']; ?>">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                                <button class="btn btn-sm btn-danger reject-btn" data-enrollment-id="<?php echo $request['id']; ?>" data-bs-toggle="modal" data-bs-target="#rejectModal" data-student-name="<?php echo htmlspecialchars($request['student_name']); ?>">
                                                    <i class="bi bi-x-circle"></i> Reject
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Approved Enrollments Tab -->
        <div class="tab-pane fade" id="approved-content" role="tabpanel" aria-labelledby="approved-tab">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0"><i class="bi bi-check-circle"></i> Enrolled Students</h5>
                </div>

                <div class="card-body p-0">
                    <?php if (empty($approvedEnrollments)): ?>
                        <div class="alert alert-info m-4 mb-0">
                            <i class="bi bi-info-circle"></i> No approved enrollments yet.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Enrollment Date</th>
                                        <th>Approved Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($approvedEnrollments as $enrollment): ?>
                                        <tr data-enrollment-id="<?php echo $enrollment['id']; ?>">
                                            <td>
                                                <strong><?php echo htmlspecialchars($enrollment['student_name']); ?></strong>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?php echo htmlspecialchars($enrollment['student_email']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?php echo htmlspecialchars($enrollment['course_code']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($enrollment['course_name']); ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-success fw-bold">
                                                    <?php echo date('M d, Y', strtotime($enrollment['approved_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-danger unenroll-btn" data-enrollment-id="<?php echo $enrollment['id']; ?>" data-bs-toggle="modal" data-bs-target="#unenrollModal" data-student-name="<?php echo htmlspecialchars($enrollment['student_name']); ?>" data-course-name="<?php echo htmlspecialchars($enrollment['course_name']); ?>">
                                                    <i class="bi bi-dash-circle"></i> Unenroll
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="rejectModalLabel">
                    <i class="bi bi-x-circle"></i> Reject Enrollment Request
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You are about to reject the enrollment request for <strong id="rejectStudentName"></strong></p>

                <div class="mb-3">
                    <label for="rejectionReason" class="form-label">Reason for Rejection (Optional)</label>
                    <textarea class="form-control" id="rejectionReason" rows="3" placeholder="Provide a reason for the rejection..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRejectBtn">
                    <i class="bi bi-x-circle"></i> Reject Request
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Unenroll Modal -->
<div class="modal fade" id="unenrollModal" tabindex="-1" aria-labelledby="unenrollModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="unenrollModalLabel">
                    <i class="bi bi-dash-circle"></i> Unenroll Student
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to unenroll <strong id="unenrollStudentName"></strong> from <strong id="unenrollCourseName"></strong>?</p>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i> This action cannot be undone. The student will lose access to the course immediately.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmUnenrollBtn">
                    <i class="bi bi-dash-circle"></i> Unenroll Student
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin: -16px -16px 0 -16px;
    }

    .page-header h1 {
        color: white;
    }

    .nav-link {
        color: #495057;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        color: #667eea;
    }

    .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background-color: transparent;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let currentEnrollmentId = null;

        // Approve button handler
        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const enrollmentId = this.dataset.enrollmentId;

                if (confirm('Are you sure you want to approve this enrollment?')) {
                    fetch('<?php echo base_url('enrollment/'); ?>' + enrollmentId + '/approve', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Enrollment approved successfully!');
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
                }
            });
        });

        // Reject button handler - set modal data
        document.querySelectorAll('.reject-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentEnrollmentId = this.dataset.enrollmentId;
                const studentName = this.dataset.studentName;
                document.getElementById('rejectStudentName').textContent = studentName;
                document.getElementById('rejectionReason').value = '';
            });
        });

        // Confirm reject handler
        document.getElementById('confirmRejectBtn').addEventListener('click', function() {
            if (currentEnrollmentId) {
                const reason = document.getElementById('rejectionReason').value.trim();

                const formData = new FormData();
                if (reason) {
                    formData.append('rejection_reason', reason);
                }

                fetch('<?php echo base_url('enrollment/'); ?>' + currentEnrollmentId + '/reject', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Enrollment rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }
        });

        // Unenroll button handler - set modal data
        document.querySelectorAll('.unenroll-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                currentEnrollmentId = this.dataset.enrollmentId;
                const studentName = this.dataset.studentName;
                const courseName = this.dataset.courseName;
                document.getElementById('unenrollStudentName').textContent = studentName;
                document.getElementById('unenrollCourseName').textContent = courseName;
            });
        });

        // Confirm unenroll handler
        document.getElementById('confirmUnenrollBtn').addEventListener('click', function() {
            if (currentEnrollmentId) {
                fetch('<?php echo base_url('enrollment/'); ?>' + currentEnrollmentId + '/unenroll', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?php echo csrf_token(); ?>': '<?php echo csrf_hash(); ?>'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Student unenrolled successfully!');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred');
                });
            }
        });

        // Reset modal data when closing
        document.getElementById('rejectModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('rejectionReason').value = '';
            currentEnrollmentId = null;
        });

        document.getElementById('unenrollModal').addEventListener('hidden.bs.modal', function() {
            currentEnrollmentId = null;
        });
    });
</script>

<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current enrollment requests implementation
- [x] Check routes configuration
- [x] Verify controller and model
- [x] Examine view structure
- [ ] Fix navigation bar link
- [ ] Create proper dashboard layout
- [ ] Ensure MVC structure
- [ ] Fix JavaScript/UI behavior
- [ ] Test the implementation
</task_progress>
</write_to_file>
