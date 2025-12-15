<?= $this->extend('teacher/layout') ?>

<?= $this->section('dashboard_content') ?>
<?php $activeTab = 'dashboard'; ?>

	<div class="teacher-dashboard-container">
		<div class="container mt-5">
			<!-- Welcome Section -->
			<div class="dashboard-welcome">
				<h1>Welcome, <?= esc($teacher['name'] ?? 'Teacher') ?>!</h1>
				<p>This is your course dashboard. Manage your courses, students, assignments, and grades from here.</p>
			</div>

			<!-- Summary Cards -->
			<div class="summary-cards">
				<div class="summary-card">
					<h3><?= $courseCount ?></h3>
					<p>Total Courses</p>
				</div>
				<div class="summary-card" style="border-left-color: #28a745;">
					<h3><?= $activeCourses ?></h3>
					<p>Active Courses</p>
				</div>
				<div class="summary-card" style="border-left-color: #ffc107;">
					<h3><?= $courseCount - $activeCourses ?></h3>
					<p>Inactive Courses</p>
				</div>
			</div>

			<!-- Courses Section -->
			<div class="courses-section">
				<div style="display: flex; justify-content: space-between; align-items: center; gap: 20px;">
					<h2 style="margin: 0; color: #343a40; border-bottom: none;">📚 Your Assigned Courses</h2>
					<button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addAssignmentModal">
						<i class="bi bi-plus-circle"></i> Add Assignment
					</button>
				</div>
				<hr style="margin-top: 20px; margin-bottom: 20px;">

				<?php if (!empty($courses) && is_array($courses)): ?>
					<?php foreach ($courses as $course): ?>
						<div class="course-item">
							<div class="course-info">
								<div class="course-code"><?= esc($course['course_code']) ?></div>
								<div class="course-name"><?= esc($course['course_name']) ?></div>
								<div class="course-meta">
									<?= esc($course['school_year'] ?? 'N/A') ?> • 
									<?= esc($course['semester'] ?? 'N/A') ?> • 
									<?= esc($course['schedule'] ?? 'N/A') ?>
									<span class="status-badge status-<?= strtolower($course['status']) ?>">
										<?= esc(ucfirst($course['status'])) ?>
									</span>
								</div>
							</div>
							<div class="course-actions">
								<a href="<?= base_url('teacher/course-management') ?>" class="btn-small btn-info">
									<i class="bi bi-pencil"></i> Manage
								</a>
								<a href="<?= base_url('assignments') ?>" class="btn-small btn-primary">
									<i class="bi bi-file-text"></i> Assignments
								</a>
								<a href="<?= base_url('grades') ?>" class="btn-small btn-success">
									<i class="bi bi-percent"></i> Grades
								</a>
							</div>
						</div>
					<?php endforeach; ?>
				<?php else: ?>
					<div class="no-courses">
						<p>No courses assigned to you yet. Contact your administrator to assign courses.</p>
					</div>
				<?php endif; ?>
			</div>

			<!-- Quick Links -->
			<div class="courses-section" style="margin-top: 20px;">
				<div class="courses-title">🔗 Quick Links</div>
				<div class="course-item" style="border: none;">
					<a href="<?= base_url('teacher/course-management') ?>" class="btn-small btn-primary">
						<i class="bi bi-gear"></i> Course Management
					</a>
					<a href="<?= base_url('teacher/students') ?>" class="btn-small btn-info" style="margin-left: 10px;">
						<i class="bi bi-people"></i> Manage Students
					</a>
					<a href="<?= base_url('assignments') ?>" class="btn-small btn-success" style="margin-left: 10px;">
						<i class="bi bi-file-text"></i> View Assignments
					</a>
					<a href="<?= base_url('grades') ?>" class="btn-small btn-primary" style="margin-left: 10px;">
						<i class="bi bi-percent"></i> View Grades
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Add Assignment Modal -->
	<div class="modal fade" id="addAssignmentModal" tabindex="-1" aria-labelledby="addAssignmentModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-success text-white">
					<h5 class="modal-title" id="addAssignmentModalLabel">
						<i class="bi bi-plus-circle"></i> Create New Assignment
					</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form id="createAssignmentForm">
						<div class="mb-3">
							<label for="assignmentCourse" class="form-label">Course <span class="text-danger">*</span></label>
							<select class="form-control" id="assignmentCourse" name="course_id" required>
								<option value="">Select a course...</option>
								<?php foreach ($courses ?? [] as $course): ?>
									<option value="<?= $course['id'] ?>">
										<?= esc($course['course_code']) ?> - <?= esc($course['course_name']) ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3">
							<label for="assignmentTitle" class="form-label">Title <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="assignmentTitle" name="title" placeholder="e.g., Chapter 5 Review" required>
						</div>

						<div class="mb-3">
							<label for="assignmentDescription" class="form-label">Description</label>
							<textarea class="form-control" id="assignmentDescription" name="description" rows="4" placeholder="Assignment details and instructions..."></textarea>
						</div>

						<div class="mb-3">
							<label for="assignmentDueDate" class="form-label">Due Date <span class="text-danger">*</span></label>
							<input type="datetime-local" class="form-control" id="assignmentDueDate" name="due_date" required>
						</div>

						<div id="formMessage"></div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-success" id="submitAssignmentBtn">
						<i class="bi bi-check-circle"></i> Create Assignment
					</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('createAssignmentForm');
			const submitBtn = document.getElementById('submitAssignmentBtn');
			const messageDiv = document.getElementById('formMessage');
			const modal = document.getElementById('addAssignmentModal');
			const bsModal = new bootstrap.Modal(modal);

			submitBtn.addEventListener('click', function() {
				const title = document.getElementById('assignmentTitle').value.trim();
				const courseId = document.getElementById('assignmentCourse').value;
				const dueDate = document.getElementById('assignmentDueDate').value;
				const description = document.getElementById('assignmentDescription').value.trim();

				if (!title || !courseId || !dueDate) {
					messageDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> Please fill in all required fields</div>';
					return;
				}

				submitBtn.disabled = true;
				submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';

				fetch('<?= base_url('assignments/create') ?>', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json',
						'X-Requested-With': 'XMLHttpRequest',
						'<?= csrf_token(); ?>': '<?= csrf_hash(); ?>'
					},
					body: JSON.stringify({
						title: title,
						course_id: courseId,
						due_date: dueDate,
						description: description
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						messageDiv.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle"></i> ' + data.message + '</div>';
						form.reset();
						setTimeout(() => {
							bsModal.hide();
							location.reload();
						}, 1500);
					} else {
						messageDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> ' + (data.message || 'Error creating assignment') + '</div>';
						submitBtn.disabled = false;
						submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Create Assignment';
					}
				})
				.catch(error => {
					console.error('Error:', error);
					messageDiv.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> An error occurred while creating the assignment</div>';
					submitBtn.disabled = false;
					submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Create Assignment';
				});
			});

			// Clear form message when modal is closed
			modal.addEventListener('hidden.bs.modal', function() {
				form.reset();
				messageDiv.innerHTML = '';
				submitBtn.disabled = false;
				submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Create Assignment';
			});
		});
	</script>

<?= $this->endSection() ?>
