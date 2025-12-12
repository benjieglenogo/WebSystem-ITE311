<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <!-- Manage Students Dashboard -->
    <style>
        /* Professional Manage Students Dashboard Styles */
        .students-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .students-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .students-title {
            color: #343a40;
            font-weight: 600;
        }

        .course-selector {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .course-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .course-details {
            flex: 1;
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #343a40;
            margin-bottom: 5px;
        }

        .course-code {
            color: #6c757d;
            font-size: 1rem;
        }

        .course-select {
            width: 300px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
        }

        /* Search and Filters */
        .search-filters-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }

        .search-filters-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            border-radius: 25px;
            border: 1px solid #ced4da;
            padding-left: 15px;
        }

        .search-btn {
            border-radius: 25px;
            background-color: #007bff;
            border: none;
            padding: 8px 20px;
        }

        .filter-select {
            width: 180px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
        }

        /* Students Table */
        .students-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 500;
            color: #495057;
        }

        .table td {
            vertical-align: middle;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-active {
            background-color: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-dropped {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-btn {
            margin-right: 5px;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 0.85rem;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
            border: none;
        }

        .btn-update {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-remove {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            border-radius: 8px 8px 0 0;
        }

        .modal-footer {
            border-top: 1px solid #dee2e6;
        }

        .form-control, .form-select {
            border-radius: 5px;
            border: 1px solid #ced4da;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .students-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .search-filters-row {
                flex-direction: column;
                gap: 10px;
            }

            .search-input, .filter-select {
                width: 100%;
            }

            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>

    <div class="students-container">
        <div class="students-header">
            <h1 class="students-title">Manage Students</h1>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        <!-- Course Selector -->
        <div class="course-selector">
            <div class="course-info">
                <div class="course-details">
                    <div class="course-title" id="courseTitle"><?= esc($selectedCourseName) ?></div>
                    <div class="course-code" id="courseCode"><?= esc($selectedCourseCode) ?></div>
                </div>
                <div class="form-group" style="width: 300px;">
                    <label for="courseSelect" class="form-label">Select Course</label>
                    <select class="form-control course-select" id="courseSelect">
                        <?php if (!empty($teacherCourses)): ?>
                            <?php foreach ($teacherCourses as $course): ?>
                                <option value="<?= esc($course['id']) ?>"
                                    data-course-name="<?= esc($course['course_name']) ?>"
                                    data-course-code="<?= esc($course['course_code']) ?>"
                                    <?= $course['id'] == $selectedCourseId ? 'selected' : '' ?>>
                                    <?= esc($course['course_code']) ?> - <?= esc($course['course_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">No courses assigned</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="search-filters-container">
            <div class="search-filters-row">
                <div class="input-group" style="flex: 1; min-width: 250px;">
                    <input type="text" id="searchInput" class="form-control search-input" placeholder="Search by name, ID, or email...">
                    <button class="btn search-btn" id="searchBtn">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>

                <select class="form-control filter-select" id="yearFilter">
                    <option value="">Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>

                <select class="form-control filter-select" id="statusFilter">
                    <option value="">Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="dropped">Dropped</option>
                </select>

                <select class="form-control filter-select" id="programFilter">
                    <option value="">Program</option>
                    <option value="CS">Computer Science</option>
                    <option value="IT">Information Technology</option>
                    <option value="IS">Information Systems</option>
                    <option value="SE">Software Engineering</option>
                </select>
            </div>
        </div>

        <!-- Students Table -->
        <div class="students-table">
            <h3 class="mb-3">Student List</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Program</th>
                            <th>Year Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentsTableBody">
                        <tr>
                            <td colspan="7" class="text-center text-muted">Select a course to view students</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Student Details Modal -->
    <div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Student ID:</strong></label>
                            <p id="modalStudentId">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Status:</strong></label>
                            <p id="modalStudentStatus">-</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Full Name:</strong></label>
                        <p id="modalStudentName">-</p>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p id="modalStudentEmail">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Program/Major:</strong></label>
                            <p id="modalStudentProgram">-</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Year Level:</strong></label>
                            <p id="modalStudentYear">-</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Section:</strong></label>
                            <p id="modalStudentSection">-</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Enrollment Date:</strong></label>
                        <p id="modalStudentEnrollmentDate">-</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusUpdateModalLabel">Update Student Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="statusUpdateForm">
                    <input type="hidden" id="updateStudentId" name="user_id">
                    <input type="hidden" id="updateCourseId" name="course_id">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentStatus" class="form-label">Current Status</label>
                            <input type="text" class="form-control" id="currentStatus" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="newStatus" class="form-label">New Status</label>
                            <select class="form-select" id="newStatus" name="new_status" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="dropped">Dropped</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="statusRemarks" class="form-label">Remarks (optional)</label>
                            <textarea class="form-control" id="statusRemarks" name="remarks" rows="3" placeholder="Reason for status change..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Student Confirmation Modal -->
    <div class="modal fade" id="removeStudentModal" tabindex="-1" aria-labelledby="removeStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeStudentModalLabel">Remove Student from Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove <strong id="removeStudentName"></strong> from this course?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveBtn">Remove Student</button>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Course selection change handler
    $('#courseSelect').change(function() {
        var selectedOption = $(this).find('option:selected');
        var courseName = selectedOption.data('course-name');
        var courseCode = selectedOption.data('course-code');
        var courseId = $(this).val();

        $('#courseTitle').text(courseName);
        $('#courseCode').text(courseCode);

        if (courseId) {
            loadStudents(courseId);
        } else {
            $('#studentsTableBody').html('<tr><td colspan="7" class="text-center text-muted">No course selected</td></tr>');
        }
    });

    // Load students for selected course
    function loadStudents(courseId) {
        $.get('<?= base_url('teacher/students/get') ?>', {course_id: courseId})
            .done(function(response) {
                if (response.success) {
                    renderStudentsTable(response.students);
                } else {
                    $('#studentsTableBody').html('<tr><td colspan="7" class="text-center text-muted">' + response.message + '</td></tr>');
                }
            })
            .fail(function() {
                $('#studentsTableBody').html('<tr><td colspan="7" class="text-center text-muted">Failed to load students. Please try again.</td></tr>');
            });
    }

    // Render students table
    function renderStudentsTable(students) {
        if (students.length === 0) {
            $('#studentsTableBody').html('<tr><td colspan="7" class="text-center text-muted">No students found in this course</td></tr>');
            return;
        }

        var html = '';
        students.forEach(function(student) {
            var statusClass = 'status-' + student.status;
            var statusText = student.status.charAt(0).toUpperCase() + student.status.slice(1);

            html += '<tr>' +
                '<td>' + (student.id || '-') + '</td>' +
                '<td>' + (student.name || '-') + '</td>' +
                '<td>' + (student.email || '-') + '</td>' +
                '<td>' + (student.program || '-') + '</td>' +
                '<td>' + (student.year_level || '-') + '</td>' +
                '<td><span class="status-badge ' + statusClass + '">' + statusText + '</span></td>' +
                '<td>' +
                '<button class="btn action-btn btn-view view-details-btn" data-student=\'' + JSON.stringify(student).replace(/'/g, "&#39;") + '\'>View Details</button>' +
                '<button class="btn action-btn btn-update update-status-btn" data-student-id="' + student.id + '" data-current-status="' + student.status + '">Update Status</button>' +
                '<button class="btn action-btn btn-remove remove-student-btn" data-student-id="' + student.id + '" data-student-name="' + (student.name || '') + '">Remove</button>' +
                '</td>' +
                '</tr>';
        });

        $('#studentsTableBody').html(html);
    }

    // View details button click handler
    $(document).on('click', '.view-details-btn', function() {
        var student = $(this).data('student');

        $('#modalStudentId').text(student.id || '-');
        $('#modalStudentName').text(student.name || '-');
        $('#modalStudentEmail').text(student.email || '-');
        $('#modalStudentProgram').text(student.program || '-');
        $('#modalStudentYear').text(student.year_level || '-');
        $('#modalStudentSection').text(student.section || '-');
        $('#modalStudentEnrollmentDate').text(student.enrollment_date || '-');
        $('#modalStudentStatus').text(student.status ? student.status.charAt(0).toUpperCase() + student.status.slice(1) : '-');

        $('#studentDetailsModal').modal('show');
    });

    // Update status button click handler
    $(document).on('click', '.update-status-btn', function() {
        var studentId = $(this).data('student-id');
        var currentStatus = $(this).data('current-status');
        var courseId = $('#courseSelect').val();

        $('#updateStudentId').val(studentId);
        $('#updateCourseId').val(courseId);
        $('#currentStatus').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
        $('#newStatus').val('');

        $('#statusUpdateModal').modal('show');
    });

    // Status update form submission
    $('#statusUpdateForm').submit(function(e) {
        e.preventDefault();

        $.post('<?= base_url('teacher/students/update-status') ?>', $(this).serialize())
            .done(function(response) {
                if (response.success) {
                    alert(response.message);
                    $('#statusUpdateModal').modal('hide');
                    // Refresh students table
                    var courseId = $('#courseSelect').val();
                    if (courseId) {
                        loadStudents(courseId);
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .fail(function() {
                alert('An error occurred while updating student status.');
            });
    });

    // Remove student button click handler
    $(document).on('click', '.remove-student-btn', function() {
        var studentId = $(this).data('student-id');
        var studentName = $(this).data('student-name');
        var courseId = $('#courseSelect').val();

        $('#removeStudentName').text(studentName);
        $('#confirmRemoveBtn').data('student-id', studentId);
        $('#confirmRemoveBtn').data('course-id', courseId);

        $('#removeStudentModal').modal('show');
    });

    // Confirm remove button click handler
    $('#confirmRemoveBtn').click(function() {
        var studentId = $(this).data('student-id');
        var courseId = $(this).data('course-id');

        $.post('<?= base_url('teacher/students/remove') ?>', {
            user_id: studentId,
            course_id: courseId,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
        .done(function(response) {
            if (response.success) {
                alert(response.message);
                $('#removeStudentModal').modal('hide');
                // Refresh students table
                if (courseId) {
                    loadStudents(courseId);
                }
            } else {
                alert('Error: ' + response.message);
            }
        })
        .fail(function() {
            alert('An error occurred while removing student from course.');
        });
    });

    // Search functionality
    $('#searchBtn').click(function() {
        filterStudents();
    });

    $('#searchInput').keypress(function(e) {
        if (e.which === 13) { // Enter key
            filterStudents();
        }
    });

    // Filter dropdowns change handlers
    $('#yearFilter, #statusFilter, #programFilter').change(function() {
        filterStudents();
    });

    function filterStudents() {
        var searchTerm = $('#searchInput').val().toLowerCase();
        var yearFilter = $('#yearFilter').val();
        var statusFilter = $('#statusFilter').val();
        var programFilter = $('#programFilter').val();

        $('#studentsTableBody tr').each(function() {
            var row = $(this);
            var studentId = row.find('td:eq(0)').text().toLowerCase();
            var studentName = row.find('td:eq(1)').text().toLowerCase();
            var studentEmail = row.find('td:eq(2)').text().toLowerCase();
            var studentProgram = row.find('td:eq(3)').text().toLowerCase();
            var studentYear = row.find('td:eq(4)').text().toLowerCase();
            var studentStatus = row.find('td:eq(5) span').text().toLowerCase();

            // Search term matching
            var matchesSearch = true;
            if (searchTerm) {
                matchesSearch = studentId.includes(searchTerm) ||
                               studentName.includes(searchTerm) ||
                               studentEmail.includes(searchTerm);
            }

            // Year filter matching
            var matchesYear = true;
            if (yearFilter) {
                matchesYear = studentYear.includes(yearFilter.toLowerCase());
            }

            // Status filter matching
            var matchesStatus = true;
            if (statusFilter) {
                matchesStatus = studentStatus.includes(statusFilter.toLowerCase());
            }

            // Program filter matching
            var matchesProgram = true;
            if (programFilter) {
                matchesProgram = studentProgram.includes(programFilter.toLowerCase());
            }

            if (matchesSearch && matchesYear && matchesStatus && matchesProgram) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Load initial students if a course is selected
    var initialCourseId = $('#courseSelect').val();
    if (initialCourseId) {
        loadStudents(initialCourseId);
    }
});
</script>
<?= $this->endSection() ?>
<task_progress>
- [x] Analyze current codebase structure
- [x] Examine Course controller and models
- [x] Examine teacher dashboard view
- [x] Identify the issue with course assignment display
- [x] Fix course assignment display issue
- [x] Implement Manage Students page
- [x] Add routes for Manage Students
- [x] Create Manage Students controller method
- [x] Create Manage Students view
- [ ] Test the implementation
</task_progress>
</write_to_file>
