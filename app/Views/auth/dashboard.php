<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Course Management Admin Dashboard -->
	<style>
		/* Professional Admin Dashboard Styles */
		.dashboard-container {
			background-color: #f8f9fa;
			min-height: 100vh;
			padding: 20px;
		}

		.dashboard-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}

		.dashboard-title {
			color: #343a40;
			font-weight: 600;
		}

		/* Summary Cards */
		.summary-card {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
			margin-bottom: 20px;
			border-left: 4px solid #007bff;
		}

		.summary-card h3 {
			color: #495057;
			font-size: 28px;
			margin-bottom: 5px;
		}

		.summary-card p {
			color: #6c757d;
			font-size: 14px;
			margin-bottom: 0;
		}

		/* Search Bar */
		.search-container {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
			margin-bottom: 20px;
		}

		.search-input {
			border-radius: 25px;
			border: 1px solid #ced4da;
			padding-left: 15px;
		}

		.search-btn {
			border-radius: 25px;
			background-color: #007bff;
			border: none;
		}

		/* Courses Table */
		.courses-table {
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

		.status-dropdown {
			padding: 5px 10px;
			border-radius: 5px;
			border: 1px solid #ced4da;
		}

		.btn-edit {
			background-color: #28a745;
			color: white;
			border: none;
			border-radius: 5px;
			padding: 5px 15px;
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
			.dashboard-header {
				flex-direction: column;
				align-items: flex-start;
			}

			.table-responsive {
				overflow-x: auto;
			}
		}

		/* Icon Animations and Visual Feedback */
		.bi-bounce {
			animation: bounce 0.3s ease;
		}

		@keyframes bounce {
			0%, 100% { transform: scale(1); }
			50% { transform: scale(1.2); }
		}

		/* Button hover effects for better icon visibility */
		.btn-primary:hover i,
		.btn-info:hover i,
		.btn-success:hover i,
		.btn-danger:hover i {
			transform: scale(1.1);
			transition: transform 0.2s ease;
		}

		/* Action button styling */
		.btn-sm {
			margin-right: 5px;
		}

		/* Tooltip styling */
		.tooltip-inner {
			max-width: 200px;
			text-align: center;
		}
	</style>

	<div class="dashboard-container">
		<div class="dashboard-header">
			<h1 class="dashboard-title">Course Management Dashboard</h1>
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

		<?php
			$roleFromSession = session('userRole');
			$roleLocal = isset($role) ? $role : $roleFromSession;
			if (!$roleLocal || !in_array($roleLocal, ['admin', 'teacher', 'student'])) {
				$roleLocal = 'student';
			}
		?>

        <?php if ($roleLocal === 'admin'): ?>
            <!-- Admin Navigation with Enhanced Icons -->
            <div class="mb-4">
                <div class="btn-group" role="group" aria-label="Admin navigation">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary" title="Manage Courses">
                        <i class="bi bi-book-fill"></i> Course Management
                    </a>
                    <a href="<?= base_url('users/management') ?>" class="btn btn-info" title="Manage Users">
                        <i class="bi bi-people-fill"></i> User Management
                    </a>
                    <a href="<?= base_url('announcements') ?>" class="btn btn-success" title="View Announcements">
                        <i class="bi bi-megaphone-fill"></i> Announcements
                    </a>
                </div>
            </div>

            <!-- Summary Cards for Admin -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="summary-card">
                        <p>Total Courses</p>
                        <h3><?= isset($widgets['courses']) ? (int)$widgets['courses'] : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" style="border-left: 4px solid #28a745;">
                        <p>Active Courses</p>
                        <h3><?= isset($widgets['active_courses']) ? (int)$widgets['active_courses'] : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card" style="border-left: 4px solid #6f42c1;">
                        <p>Total Users</p>
                        <h3><?= isset($widgets['users']) ? (int)$widgets['users'] : 0 ?></h3>
                    </div>
                </div>
            </div>

            <!-- Admin Course Management Table -->
            <div class="courses-table">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0">Course Management</h3>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="bi bi-plus"></i> Create New Course
                    </button>
                </div>

                <!-- Search Bar -->
                <div class="search-container mb-3">
                    <form id="courseSearchForm" class="d-flex">
                        <input type="text" class="form-control search-input me-2" id="searchInput" placeholder="Search courses...">
                        <button type="submit" class="btn btn-primary search-btn">
                            <i class="bi bi-search"></i> Search
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Teacher</th>
                                <th>School Year</th>
                                <th>Semester</th>
                                <th>Schedule</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($courses) && !empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['course_name'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['teacher_name'] ?? 'Unassigned') ?></td>
                                        <td><?= esc($course['school_year'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                        <td>
                                            <select class="status-dropdown" data-course-id="<?= esc($course['id'] ?? '') ?>">
                                                <option value="active" <?= (($course['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
                                                <option value="inactive" <?= (($course['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary edit-course-btn" data-course-id="<?= esc($course['id'] ?? '') ?>" title="Edit Course Details">
                                                <i class="bi bi-pencil-fill"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-course-btn" data-course-id="<?= esc($course['id'] ?? '') ?>" title="Delete Course Permanently">
                                                <i class="bi bi-trash-fill"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No courses found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($roleLocal === 'teacher'): ?>
            <!-- Teacher Navigation -->
            <div class="mb-4">
                <div class="btn-group" role="group" aria-label="Teacher navigation">
                    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-book"></i> My Courses
                    </a>
                    <a href="<?= base_url('teacher/students') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-people"></i> Manage Students
                    </a>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="summary-card">
                        <p>Total Courses</p>
                        <h3><?= isset($widgets['courses']) ? (int)$widgets['courses'] : 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-card" style="border-left: 4px solid #28a745;">
                        <p>Active Courses</p>
                        <h3><?= isset($widgets['active_courses']) ? (int)$widgets['active_courses'] : 0 ?></h3>
                    </div>
                </div>
            </div>

            <!-- Teacher's Courses Table -->
            <div class="courses-table">
                <h3 class="mb-3">Your Courses</h3>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Title</th>
                                <th>Description</th>
                                <th>School Year</th>
                                <th>Semester</th>
                                <th>Schedule</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($teacherCourses) && !empty($teacherCourses)): ?>
                                <?php foreach ($teacherCourses as $course): ?>
                                    <tr>
                                        <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['course_name'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['description'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['school_year'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                        <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                        <td>
                                            <span class="badge <?= ($course['status'] ?? '') === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= esc(ucfirst($course['status'] ?? 'N/A')) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No courses assigned</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
		<?php else: ?>
			<div class="alert alert-info">
				You don't have permission to access the Course Management Dashboard.
			</div>
		<?php endif; ?>
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
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="createCourseCode" class="form-label">Course Code</label>
								<input type="text" class="form-control" id="createCourseCode" name="course_code" required>
							</div>
							<div class="col-md-6">
								<label for="createCourseTitle" class="form-label">Course Title</label>
								<input type="text" class="form-control" id="createCourseTitle" name="course_title" required>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="createSchoolYear" class="form-label">School Year</label>
								<select class="form-select" id="createSchoolYear" name="school_year" required>
									<option value="">Select Year</option>
									<option value="2023-2024">2023-2024</option>
									<option value="2024-2025">2024-2025</option>
									<option value="2025-2026">2025-2026</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="createSemester" class="form-label">Semester</label>
								<select class="form-select" id="createSemester" name="semester" required>
									<option value="">Select Semester</option>
									<option value="1st Semester">1st Semester</option>
									<option value="2nd Semester">2nd Semester</option>
									<option value="Summer">Summer</option>
								</select>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="createStartDate" class="form-label">Start Date</label>
								<input type="date" class="form-control" id="createStartDate" name="start_date" required>
							</div>
							<div class="col-md-6">
								<label for="createEndDate" class="form-label">End Date</label>
								<input type="date" class="form-control" id="createEndDate" name="end_date" required>
							</div>
						</div>

						<div class="mb-3">
							<label for="createDescription" class="form-label">Description</label>
							<textarea class="form-control" id="createDescription" name="description" rows="3" required></textarea>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="createTeacher" class="form-label">Teacher</label>
								<select class="form-select" id="createTeacher" name="teacher_id" required>
									<option value="">Select Teacher</option>
									<?php if (isset($teachers) && !empty($teachers)): ?>
										<?php foreach ($teachers as $teacher): ?>
											<option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="createSchedule" class="form-label">Schedule</label>
								<select class="form-select" id="createSchedule" name="schedule" required>
									<option value="">Select Schedule</option>
									<option value="Monday-Wednesday">Monday-Wednesday</option>
									<option value="Tuesday-Thursday">Tuesday-Thursday</option>
									<option value="Friday-Saturday">Friday-Saturday</option>
									<option value="Daily">Daily</option>
								</select>
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
					<h5 class="modal-title" id="editCourseModalLabel">Edit Course Details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<form id="editCourseForm">
					<input type="hidden" id="editCourseId" name="course_id">
					<?= csrf_field() ?>
					<div class="modal-body">
						<div class="row mb-3">
							<div class="col-md-6">
								<label for="editCourseCode" class="form-label">Course Code</label>
								<input type="text" class="form-control" id="editCourseCode" name="course_code" readonly>
							</div>
							<div class="col-md-6">
								<label for="editCourseTitle" class="form-label">Course Title</label>
								<input type="text" class="form-control" id="editCourseTitle" name="course_title" required>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="editSchoolYear" class="form-label">School Year</label>
								<select class="form-select" id="editSchoolYear" name="school_year" required>
									<option value="">Select Year</option>
									<option value="2023-2024">2023-2024</option>
									<option value="2024-2025">2024-2025</option>
									<option value="2025-2026">2025-2026</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="editSemester" class="form-label">Semester</label>
								<select class="form-select" id="editSemester" name="semester" required>
									<option value="">Select Semester</option>
									<option value="1st Semester">1st Semester</option>
									<option value="2nd Semester">2nd Semester</option>
									<option value="Summer">Summer</option>
								</select>
							</div>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="editStartDate" class="form-label">Start Date</label>
								<input type="date" class="form-control" id="editStartDate" name="start_date" required>
							</div>
							<div class="col-md-6">
								<label for="editEndDate" class="form-label">End Date</label>
								<input type="date" class="form-control" id="editEndDate" name="end_date" required>
							</div>
						</div>

						<div class="mb-3">
							<label for="editDescription" class="form-label">Description</label>
							<textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
						</div>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="editTeacher" class="form-label">Teacher</label>
								<select class="form-select" id="editTeacher" name="teacher_id" required>
									<option value="">Select Teacher</option>
									<?php if (isset($teachers) && !empty($teachers)): ?>
										<?php foreach ($teachers as $teacher): ?>
											<option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="editSchedule" class="form-label">Schedule</label>
								<select class="form-select" id="editSchedule" name="schedule" required>
									<option value="">Select Schedule</option>
									<option value="Monday-Wednesday">Monday-Wednesday</option>
									<option value="Tuesday-Thursday">Tuesday-Thursday</option>
									<option value="Friday-Saturday">Friday-Saturday</option>
									<option value="Daily">Daily</option>
								</select>
							</div>
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
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
	// Initialize tooltips for all icon buttons
	$('[title]').tooltip();

	// Make navigation icons functional with visual feedback
	$('.btn-primary, .btn-info, .btn-success').on('click', function(e) {
		// Add visual feedback for icon button clicks
		var $btn = $(this);
		$btn.addClass('active').find('i').addClass('bi-bounce');

		// Remove animation after click
		setTimeout(function() {
			$btn.removeClass('active').find('i').removeClass('bi-bounce');
		}, 300);
	});

	// Course search functionality
	$('#courseSearchForm').submit(function(e) {
		e.preventDefault();
		var searchTerm = $('#searchInput').val();

		// Filter table rows based on search term
		$('table tbody tr').each(function() {
			var rowText = $(this).text().toLowerCase();
			var searchText = searchTerm.toLowerCase();

			if (rowText.includes(searchText)) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
	});

	// Make search icon clickable with visual feedback
	$('.search-btn').click(function() {
		// Add visual feedback
		var $searchBtn = $(this);
		$searchBtn.find('i').addClass('bi-bounce');

		// Trigger the search form submission
		$('#courseSearchForm').submit();

		// Remove animation after search
		setTimeout(function() {
			$searchBtn.find('i').removeClass('bi-bounce');
		}, 300);
	});

	// Add hover effects to action buttons
	$('.edit-course-btn, .delete-course-btn').hover(
		function() {
			// Mouse enter - add hover effect
			$(this).find('i').addClass('bi-pulse');
		},
		function() {
			// Mouse leave - remove hover effect
			$(this).find('i').removeClass('bi-pulse');
		}
	);

	// Add CSS class for pulse animation
	$('head').append('<style>.bi-pulse { animation: pulse 1s infinite; } @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }</style>');

	// Edit course button click handler
	$('.edit-course-btn').click(function() {
		var courseId = $(this).data('course-id');
		$('#editCourseId').val(courseId);

		// Fetch course data from server
		$.get('<?= base_url('courses/get/') ?>' + courseId)
			.done(function(response) {
				if (response.success) {
					var course = response.course;
					$('#editCourseCode').val(course.course_code);
					$('#editCourseTitle').val(course.course_name);
					$('#editDescription').val(course.description);
					$('#editSchoolYear').val(course.school_year);
					$('#editSemester').val(course.semester);
					$('#editSchedule').val(course.schedule);
					$('#editTeacher').val(course.teacher_id);
					$('#editStartDate').val(course.start_date);
					$('#editEndDate').val(course.end_date);
				} else {
					alert('Failed to load course data: ' + response.message);
				}
			})
			.fail(function() {
				alert('An error occurred while fetching course data.');
			});
	});

	// Edit course form submission with date validation
	$('#editCourseForm').submit(function(e) {
		e.preventDefault();

		var startDate = new Date($('#editStartDate').val());
		var endDate = new Date($('#editEndDate').val());

		// Validate that end date is after start date
		if (endDate < startDate) {
			alert('End date must be after start date');
			return;
		}

		// Submit form via AJAX
		$.ajax({
			url: '<?= base_url('courses/update') ?>',
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					alert(response.message);
					$('#editCourseModal').modal('hide');
					// Refresh the page to see changes
					location.reload();
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function() {
				alert('An error occurred while updating the course.');
			}
		});
	});

	// Status dropdown change handler
	$(document).on('change', '.status-dropdown', function() {
		var courseId = $(this).data('course-id');
		var newStatus = $(this).val();

		// Update status via AJAX with CSRF token
		$.ajax({
			url: '<?= base_url('courses/update-status') ?>',
			type: 'POST',
			data: {
				course_id: courseId,
				status: newStatus,
				<?= csrf_token() ?>: '<?= csrf_hash() ?>'
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					// Show success message
					alert(response.message);
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', error);
				var errorMessage = 'An error occurred while updating course status.';
				try {
					var response = JSON.parse(xhr.responseText);
					if (response && response.message) {
						errorMessage = response.message;
					}
				} catch (e) {
					// Couldn't parse JSON, use default message
				}
				alert(errorMessage);
			}
		});
	});

	// Edit course button click handler
	$('.edit-course-btn').click(function() {
		var courseId = $(this).data('course-id');
		$('#editCourseId').val(courseId);

		// Fetch course data from server
		$.get('<?= base_url('courses/get/') ?>' + courseId)
			.done(function(response) {
				if (response.success) {
					var course = response.course;
					$('#editCourseCode').val(course.course_code);
					$('#editCourseTitle').val(course.course_name);
					$('#editDescription').val(course.description);
					$('#editSchoolYear').val(course.school_year);
					$('#editSemester').val(course.semester);
					$('#editSchedule').val(course.schedule);
					$('#editTeacher').val(course.teacher_id);
					$('#editStartDate').val(course.start_date);
					$('#editEndDate').val(course.end_date);
				} else {
					alert('Failed to load course data: ' + response.message);
				}
			})
			.fail(function() {
				alert('An error occurred while fetching course data.');
			});
	});

	// Delete course button click handler
	$(document).on('click', '.delete-course-btn', function() {
		var courseId = $(this).data('course-id');

		if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
			$.ajax({
				url: '<?= base_url('courses/delete') ?>',
				type: 'POST',
				data: {
					course_id: courseId,
					<?= csrf_token() ?>: '<?= csrf_hash() ?>'
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						alert(response.message);
						// Refresh the page to see changes
						location.reload();
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function(xhr, status, error) {
					console.error('AJAX Error:', error);
					var errorMessage = 'An error occurred while deleting the course.';
					try {
						var response = JSON.parse(xhr.responseText);
						if (response && response.message) {
							errorMessage = response.message;
						}
					} catch (e) {
						// Couldn't parse JSON, use default message
					}
					alert(errorMessage);
				}
			});
		}
	});

	// Create course form submission
	$('#createCourseForm').submit(function(e) {
		e.preventDefault();

		var startDate = new Date($('#createStartDate').val());
		var endDate = new Date($('#createEndDate').val());

		// Validate that end date is after start date
		if (endDate < startDate) {
			alert('End date must be after start date');
			return;
		}

		// Submit form via AJAX
		$.ajax({
			url: '<?= base_url('courses/create') ?>',
			type: 'POST',
			data: $(this).serialize(),
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					alert(response.message);
					$('#createCourseModal').modal('hide');
					// Refresh the page to see changes
					location.reload();
				} else {
					alert('Error: ' + response.message);
				}
			},
			error: function() {
				alert('An error occurred while creating the course.');
			}
		});
	});
});
</script>
<?= $this->endSection() ?>
