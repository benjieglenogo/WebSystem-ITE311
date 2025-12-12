<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
    <!-- Course Management Dashboard -->
    <style>
        /* Professional Course Management Dashboard Styles */
        .course-management-container {
            background-color: #f8f9fa;
            min-height: 100vh;
            padding: 20px;
        }

        .course-management-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .course-management-title {
            color: #343a40;
            font-weight: 600;
        }

        .course-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .course-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            position: relative;
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .course-code {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .course-details {
            padding: 20px;
            border-bottom: 1px solid #eee;
        }

        .course-actions {
            padding: 15px 20px;
            background: #f8f9fa;
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #eee;
        }

        .students-section, .materials-section {
            padding: 20px;
        }

        .students-section {
            background: #f8f9fa;
        }

        .materials-section {
            background: white;
        }

        .students-table, .materials-table {
            width: 100%;
            margin-top: 15px;
        }

        .students-table th, .students-table td,
        .materials-table th, .materials-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .students-table th, .materials-table th {
            background-color: #f8f9fa;
            font-weight: 500;
        }

        .upload-area {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin: 20px 0;
            background: #f0f8ff;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            background: #e6f7ff;
            border-color: #0056b3;
        }

        .upload-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .upload-btn:hover {
            background: #0056b3;
        }

        .file-input {
            display: none;
        }

        .btn-manage {
            margin-right: 10px;
            padding: 5px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }

        .btn-students {
            background-color: #17a2b8;
            color: white;
        }

        .btn-materials {
            background-color: #28a745;
            color: white;
        }

        .btn-upload {
            background-color: #ffc107;
            color: #212529;
        }

        .modal-content {
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .file-info {
            display: flex;
            align-items: center;
        }

        .file-info i {
            margin-right: 10px;
            color: #007bff;
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 40px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #dee2e6;
        }

        .collapse-toggle {
            cursor: pointer;
            padding: 3px;
            border: none;
            background: none;
            transition: transform 0.2s;
        }

        .collapse-toggle.collapsed {
            transform: rotate(-90deg);
        }
    </style>

    <div class="course-management-container">
        <div class="course-management-header">
            <h1 class="course-management-title">Course Management</h1>
            <div>
                <button class="btn btn-primary me-2 btn-create-course">+ Add New Course</button>
                <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('success')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= esc(session()->getFlashdata('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($teacherCourses)): ?>
            <?php foreach ($teacherCourses as $course): ?>
                <div class="course-card">
                    <div class="course-header">
                        <div class="course-title"><?= esc($course['course_name']) ?></div>
                        <div class="course-code">Code: <?= esc($course['course_code']) ?></div>
                        <div class="school-year-badge">Year: <?= esc($course['school_year']) ?> | Semester: <?= esc($course['semester']) ?></div>
                    </div>

                    <div class="course-details">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Description:</strong><br>
                                <?= esc($course['description']) ?: 'No description available' ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Schedule:</strong><br>
                                <?= esc($course['schedule']) ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Status:</strong><br>
                                <span class="badge <?= $course['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= ucfirst(esc($course['status'])) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Actions -->
                    <div class="course-actions">
                        <div>
                            <button class="btn btn-manage btn-students" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc($course['course_name']) ?>">
                                <i class="bi bi-people"></i> View Students
                            </button>
                            <button class="btn btn-manage btn-materials" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc($course['course_name']) ?>">
                                <i class="bi bi-folder"></i> View Materials
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-manage btn-upload" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc($course['course_name']) ?>">
                                <i class="bi bi-upload"></i> Upload Material
                            </button>
                            <button class="btn btn-manage btn-warning" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc(str_replace("'", "\\'", $course['course_name'])) ?>">
                                <i class="bi bi-pencil"></i> Edit Course
                            </button>
                            <button class="btn btn-manage btn-danger" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc(str_replace("'", "\\'", $course['course_name'])) ?>">
                                <i class="bi bi-trash"></i> Delete Course
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-book-x"></i>
                <h4>No Courses Assigned</h4>
                <p>You haven't been assigned any courses yet. Contact an administrator to assign courses to you.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Students Modal -->
    <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentsModalLabel">Enrolled Students - <span id="modalCourseName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="studentsContent">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p>Loading students...</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Material - <span id="uploadModalCourseName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" id="uploadCourseId" name="course_id">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="materialFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="materialFile" name="material_file" required>
                            <div class="form-text">Supported formats: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, JPEG, PNG, MP4, AVI, MOV, etc.</div>
                        </div>
                        <div class="mb-3">
                            <label for="materialDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="materialDescription" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Course Modal -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCourseModalLabel">Create New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createCourseForm">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="createCourseCode" class="form-label">Course Code *</label>
                                    <input type="text" class="form-control" id="createCourseCode" name="course_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="createCourseName" class="form-label">Course Name *</label>
                                    <input type="text" class="form-control" id="createCourseName" name="course_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="createDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="createDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="createSchoolYear" class="form-label">School Year *</label>
                                    <input type="text" class="form-control" id="createSchoolYear" name="school_year" placeholder="2023-2024" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="createSemester" class="form-label">Semester *</label>
                                    <select class="form-select" id="createSemester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="createSchedule" class="form-label">Schedule *</label>
                                    <input type="text" class="form-control" id="createSchedule" name="schedule" placeholder="MWF 9:00-11:00" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="createStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="createStartDate" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="createEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="createEndDate" name="end_date">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Course Modal -->
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editCourseForm">
                    <input type="hidden" id="editCourseId" name="course_id">
                    <?= csrf_field() ?>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCourseCode" class="form-label">Course Code *</label>
                                    <input type="text" class="form-control" id="editCourseCode" name="course_code" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editCourseName" class="form-label">Course Name *</label>
                                    <input type="text" class="form-control" id="editCourseName" name="course_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editDescription" class="form-label">Description *</label>
                            <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editSchoolYear" class="form-label">School Year *</label>
                                    <input type="text" class="form-control" id="editSchoolYear" name="school_year" placeholder="2023-2024" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editSemester" class="form-label">Semester *</label>
                                    <select class="form-select" id="editSemester" name="semester" required>
                                        <option value="">Select Semester</option>
                                        <option value="1st">1st Semester</option>
                                        <option value="2nd">2nd Semester</option>
                                        <option value="Summer">Summer</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="editSchedule" class="form-label">Schedule *</label>
                                    <input type="text" class="form-control" id="editSchedule" name="schedule" placeholder="MWF 9:00-11:00" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editStartDate" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="editStartDate" name="start_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="editEndDate" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="editEndDate" name="end_date">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editStatus" class="form-label">Status</label>
                            <select class="form-select" id="editStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Materials Modal -->
    <?= $this->include('materials/modal') ?>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Bind button clicks using jQuery
    $('.btn-manage.btn-students').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        loadStudents(courseId, courseName);
    });

    $('.btn-manage.btn-materials').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        showMaterialsModal(courseId, courseName, true);
    });

    $('.btn-manage.btn-upload').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        showUploadModal(courseId, courseName);
    });

    $('.btn-manage.btn-warning').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        showEditCourseModal(courseId, courseName);
    });

    $('.btn-manage.btn-danger').on('click', function() {
        const courseId = $(this).data('course-id');
        const courseName = $(this).data('course-name');
        deleteCourse(courseId, courseName);
    });

    $('.btn-create-course').on('click', function() {
        showCreateCourseModal();
    });

    // Load students for a course
    function loadStudents(courseId, courseName) {
        $('#modalCourseName').text(courseName);
        $('#studentsModal').modal('show');

        $.get('<?= base_url('teacher/course-management/get-students') ?>', {course_id: courseId})
            .done(function(response) {
                if (response.success) {
                    renderStudentsTable(response.students);
                } else {
                    $('#studentsContent').html('<div class="alert alert-info">No students enrolled in this course.</div>');
                }
            })
            .fail(function() {
                $('#studentsContent').html('<div class="alert alert-danger">Failed to load students. Please try again.</div>');
            });
    }

    function renderStudentsTable(students) {
        if (!students || students.length === 0) {
            $('#studentsContent').html('<div class="alert alert-info">No students enrolled in this course.</div>');
            return;
        }

        var html = '<div class="table-responsive">';
        html += '<table class="students-table">';
        html += '<thead>';
        html += '<tr>';
        html += '<th>ID</th>';
        html += '<th>Name</th>';
        html += '<th>Email</th>';
        html += '<th>Program</th>';
        html += '<th>Year Level</th>';
        html += '<th>Status</th>';
        html += '<th>Actions</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';

        students.forEach(function(student) {
            var statusClass = student.status === 'active' ? 'success' : 'secondary';
            html += '<tr>';
            html += '<td>' + (student.id || '-') + '</td>';
            html += '<td>' + (student.name || '-') + '</td>';
            html += '<td>' + (student.email || '-') + '</td>';
            html += '<td>' + (student.program || '-') + '</td>';
            html += '<td>' + (student.year_level || '-') + '</td>';
            html += '<td><span class="badge bg-' + statusClass + '">' + (student.status || 'inactive').charAt(0).toUpperCase() + (student.status || 'inactive').slice(1) + '</span></td>';
            html += '<td>';
            html += '<button class="btn btn-sm btn-warning" onclick="updateStudentStatus(' + (student.id || 0) + ', \'' + (student.status || 'inactive') + '\')" title="Update Status"><i class="bi bi-pencil"></i></button> ';
            html += '<button class="btn btn-sm btn-danger" onclick="removeStudent(' + (student.id || 0) + ', ' + student.course_id + ', \'' + (student.name || '') + '\')" title="Remove from Course"><i class="bi bi-trash"></i></button>';
            html += '</td>';
            html += '</tr>';
        });

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        $('#studentsContent').html(html);
    }

    // Show upload modal
    window.showUploadModal = function(courseId, courseName) {
        $('#uploadModalCourseName').text(courseName);
        $('#uploadCourseId').val(courseId);
        $('#uploadModal').modal('show');
    };

    // Show materials modal
    window.showMaterialsModal = function(courseId, courseName, isTeacher) {
        // This function should be defined in the included materials modal
        if (typeof showMaterialsModal !== 'undefined') {
            showMaterialsModal(courseId, courseName, isTeacher);
        } else {
            alert('Materials modal not available. Please check your setup.');
        }
    };

    // Handle file upload form submission
    $('#uploadForm').submit(function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        // Show loading
        $('button[type="submit"]').prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading...');

        $.ajax({
            url: '<?= base_url('materials/ajax-upload') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Material uploaded successfully!');
                    $('#uploadModal').modal('hide');
                    // Refresh materials if modal is open
                    if ($('#materialsModal').is(':visible')) {
                        var courseId = $('#materialsModal').data('course-id');
                        if (courseId) {
                            showMaterialsModal(courseId, $('#materialsModal').data('course-name'), true);
                        }
                    }
                } else {
                    alert('Error: ' + (response.message || 'Upload failed'));
                }
            },
            error: function(xhr) {
                var message = 'Upload failed. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {}
                alert(message);
            },
            complete: function() {
                $('button[type="submit"]').prop('disabled', false).html('Upload');
            }
        });
    });

    // Update student status
    window.updateStudentStatus = function(userId, currentStatus) {
        var newStatus = currentStatus === 'active' ? 'inactive' : 'active';

        if (confirm('Are you sure you want to change the student status to ' + newStatus + '?')) {
            var courseId = $('input[name="course_id"]').val();

            $.post('<?= base_url('teacher/course-management/update-status') ?>', {
                user_id: userId,
                new_status: newStatus,
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(response) {
                if (response.success) {
                    alert('Student status updated!');
                    // Reload students
                    loadStudents(courseId, $('#modalCourseName').text());
                } else {
                    alert('Error: ' + (response.message || 'Update failed'));
                }
            })
            .fail(function() {
                alert('An error occurred while updating student status.');
            });
        }
    };

    // Remove student from course
    window.removeStudent = function(userId, courseId, studentName) {
        if (confirm('Are you sure you want to remove ' + studentName + ' from this course?')) {
            $.post('<?= base_url('teacher/course-management/remove') ?>', {
                user_id: userId,
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(response) {
                if (response.success) {
                    alert('Student removed from course!');
                    // Reload students
                    loadStudents(courseId, $('#modalCourseName').text());
                } else {
                    alert('Error: ' + (response.message || 'Removal failed'));
                }
            })
            .fail(function() {
                alert('An error occurred while removing the student.');
            });
        }
    };

    // Show create course modal
    window.showCreateCourseModal = function() {
        // Clear form
        $('#createCourseForm')[0].reset();
        $('#createCourseModal').modal('show');
    };

    // Show edit course modal
    window.showEditCourseModal = function(courseId, courseName) {
        // Load course data
        $.get('<?= base_url('courses/get') ?>/' + courseId)
            .done(function(response) {
                if (response.success) {
                    var course = response.course;
                    $('#editCourseId').val(course.id);
                    $('#editCourseCode').val(course.course_code);
                    $('#editCourseName').val(course.course_name);
                    $('#editDescription').val(course.description);
                    $('#editSchoolYear').val(course.school_year);
                    $('#editSemester').val(course.semester);
                    $('#editSchedule').val(course.schedule);
                    $('#editStartDate').val(course.start_date);
                    $('#editEndDate').val(course.end_date);
                    $('#editStatus').val(course.status);

                    $('#editCourseModalLabel').text('Edit Course - ' + course.course_name);
                    $('#editCourseModal').modal('show');
                } else {
                    alert('Failed to load course data');
                }
            })
            .fail(function() {
                alert('An error occurred while loading course data');
            });
    };

    // Delete course
    window.deleteCourse = function(courseId, courseName) {
        if (confirm('Are you sure you want to delete the course "' + courseName + '"? This action cannot be undone and will remove all associated data.')) {
            // Show loading
            var button = $('button[onclick="deleteCourse(' + courseId + ', \'' + courseName.replace(/'/g, "\\'") + '\')"]');
            button.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Deleting...');

            $.post('<?= base_url('teacher/course-management/delete-course') ?>', {
                course_id: courseId,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            })
            .done(function(response) {
                if (response.success) {
                    alert('Course deleted successfully!');
                    location.reload(); // Refresh the page
                } else {
                    alert('Error: ' + (response.message || 'Failed to delete course'));
                }
            })
            .fail(function() {
                alert('An error occurred while deleting the course');
            })
            .always(function() {
                button.prop('disabled', false).html('<i class="bi bi-trash"></i> Delete Course');
            });
        }
    };

    // Handle create course form submission
    $('#createCourseForm').submit(function(e) {
        e.preventDefault();

        // Show loading
        $('button[type="submit"]', this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

        $.ajax({
            url: '<?= base_url('teacher/course-management/create-course') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Course created successfully!');
                    $('#createCourseModal').modal('hide');
                    location.reload(); // Refresh the page
                } else {
                    alert('Error: ' + (response.message || 'Failed to create course'));
                }
            },
            error: function(xhr) {
                var message = 'Failed to create course. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {}
                alert(message);
            },
            complete: function() {
                $('button[type="submit"]', '#createCourseForm').prop('disabled', false).html('Create Course');
            }
        });
    });

    // Handle edit course form submission
    $('#editCourseForm').submit(function(e) {
        e.preventDefault();

        // Show loading
        $('button[type="submit"]', this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Updating...');

        $.ajax({
            url: '<?= base_url('teacher/course-management/update-course') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Course updated successfully!');
                    $('#editCourseModal').modal('hide');
                    location.reload(); // Refresh the page
                } else {
                    alert('Error: ' + (response.message || 'Failed to update course'));
                }
            },
            error: function(xhr) {
                var message = 'Failed to update course. Please try again.';
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        message = response.message;
                    }
                } catch (e) {}
                alert(message);
            },
            complete: function() {
                $('button[type="submit"]', '#editCourseForm').prop('disabled', false).html('Update Course');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
