<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Assignments Page -->
	<style>
		.assignments-container {
			background-color: #f8f9fa;
			min-height: 100vh;
			padding: 20px;
		}

		.assignments-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 30px;
		}

		.assignments-title {
			color: #343a40;
			font-weight: 600;
			font-size: 28px;
		}

		.assignment-card {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
			margin-bottom: 15px;
			border-left: 4px solid #007bff;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.assignment-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
		}

		.assignment-title {
			color: #007bff;
			font-weight: 600;
			font-size: 18px;
			margin-bottom: 10px;
		}

		.assignment-description {
			color: #666;
			font-size: 14px;
			margin-bottom: 10px;
		}

		.assignment-meta {
			display: flex;
			justify-content: space-between;
			align-items: center;
			font-size: 12px;
			color: #999;
		}

		.due-date {
			color: #dc3545;
			font-weight: 600;
		}

		.assignment-actions {
			margin-top: 10px;
		}

		.assignment-actions .btn {
			margin-right: 5px;
			margin-top: 5px;
		}

		.no-assignments {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 40px;
			text-align: center;
			color: #999;
		}

		.nav-tabs {
			border-bottom: 2px solid #dee2e6;
			margin-bottom: 30px;
		}

		.nav-link {
			color: #666;
			border: none;
			padding: 10px 20px;
			font-weight: 500;
		}

		.nav-link.active {
			color: #007bff;
			border-bottom: 3px solid #007bff;
		}

		.filter-section {
			background: white;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 20px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}

		.status-badge {
			display: inline-block;
			padding: 5px 10px;
			border-radius: 5px;
			font-size: 11px;
			font-weight: 600;
			margin-right: 10px;
		}

		.status-pending {
			background-color: #fff3cd;
			color: #856404;
		}

		.status-submitted {
			background-color: #d1ecf1;
			color: #0c5460;
		}

		.status-graded {
			background-color: #d4edda;
			color: #155724;
		}
	</style>

	<div class="assignments-container">
		<div class="container mt-5">
			<!-- Header -->
			<div class="assignments-header">
				<h1 class="assignments-title">📋 Assignments</h1>
				<?php if ($userRole === 'teacher' || $userRole === 'admin'): ?>
					<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
						<i class="bi bi-plus"></i> Create New Assignment
					</button>
				<?php endif; ?>
			</div>

			<!-- Filter Section -->
			<div class="filter-section">
				<form id="assignmentFilterForm" class="d-flex gap-2">
					<input type="text" id="assignmentSearch" class="form-control" placeholder="Search assignments...">
					<select id="statusFilter" class="form-select">
						<option value="">All Status</option>
						<option value="pending">Pending</option>
						<option value="submitted">Submitted</option>
						<option value="graded">Graded</option>
					</select>
					<button type="submit" class="btn btn-secondary">Filter</button>
				</form>
			</div>

			<!-- Assignments List -->
			<?php if (!empty($assignments) && is_array($assignments)): ?>
				<div id="assignmentsList">
					<?php foreach ($assignments as $assignment): ?>
						<div class="assignment-card">
							<div class="assignment-title">
								<?= esc($assignment['title'] ?? 'Untitled Assignment') ?>
							</div>
							<div class="assignment-description">
								<?= esc(substr($assignment['description'] ?? '', 0, 150)) ?>
								<?php if (strlen($assignment['description'] ?? '') > 150): ?>
									...
								<?php endif; ?>
							</div>
							<div class="assignment-meta">
								<span>
									<span class="status-badge status-pending">Not Started</span>
									Course: <?= esc($assignment['course_id'] ?? 'N/A') ?>
								</span>
								<span class="due-date">
									Due: <?= date('M d, Y', strtotime($assignment['due_date'] ?? 'now')) ?>
								</span>
							</div>
                            <div class="assignment-actions">
                                <a href="<?= base_url('assignments/' . ($assignment['id'] ?? '#')) ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <?php if ($userRole === 'student'): ?>
                                    <a href="<?= base_url('assignments/' . ($assignment['id'] ?? '#')) ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View Submission
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="submitAssignment(<?= $assignment['id'] ?? '#' ?>)">
                                        <i class="bi bi-upload"></i> Submit
                                    </button>
                                <?php endif; ?>
                                <?php if ($userRole === 'teacher' || $userRole === 'admin'): ?>
                                    <a href="<?= base_url('assignments/' . ($assignment['id'] ?? '#') . '/submissions') ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> View Submissions
                                    </a>
                                    <button class="btn btn-sm btn-warning" onclick="editAssignment(<?= $assignment['id'] ?? '#' ?>)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteAssignment(<?= $assignment['id'] ?? '#' ?>)">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                <?php endif; ?>
                            </div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else: ?>
				<div class="no-assignments">
					<h4>📭 No Assignments Yet</h4>
					<p>
						<?php if ($userRole === 'teacher' || $userRole === 'admin'): ?>
							Create an assignment to get started!
						<?php else: ?>
							No assignments have been assigned to your courses.
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Create Assignment Modal -->
	<?php if ($userRole === 'teacher' || $userRole === 'admin'): ?>
		<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Create New Assignment</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<form id="createAssignmentForm">
						<div class="modal-body">
							<div class="mb-3">
								<label for="assignmentTitle" class="form-label">Assignment Title</label>
								<input type="text" class="form-control" id="assignmentTitle" name="title" required>
							</div>
							<div class="mb-3">
								<label for="assignmentDescription" class="form-label">Description</label>
								<textarea class="form-control" id="assignmentDescription" name="description" rows="4" required></textarea>
							</div>
							<div class="mb-3">
								<label for="assignmentCourse" class="form-label">Course</label>
								<select class="form-select" id="assignmentCourse" name="course_id" required>
									<option value="">Select a course...</option>
									<!-- Will be populated by JavaScript -->
								</select>
							</div>
							<div class="mb-3">
								<label for="assignmentDueDate" class="form-label">Due Date</label>
								<input type="datetime-local" class="form-control" id="assignmentDueDate" name="due_date" required>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
							<button type="submit" class="btn btn-primary">Create Assignment</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function() {
		// Filter assignments
		$('#assignmentFilterForm').submit(function(e) {
			e.preventDefault();
			var searchTerm = $('#assignmentSearch').val().toLowerCase();
			
			$('.assignment-card').each(function() {
				var title = $(this).find('.assignment-title').text().toLowerCase();
				if (title.includes(searchTerm)) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
		});

		// Create assignment form submission
		$('#createAssignmentForm').submit(function(e) {
			e.preventDefault();
			
			$.ajax({
				url: '<?= base_url('assignments/create') ?>',
				type: 'POST',
				data: $(this).serialize(),
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						alert('Assignment created successfully!');
						$('#createAssignmentModal').modal('hide');
						location.reload();
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function() {
					alert('An error occurred while creating the assignment.');
				}
			});
		});
	});

	function submitAssignment(assignmentId) {
		alert('Opening submission form for assignment ' + assignmentId);
		// Implementation for submission form
	}

	function editAssignment(assignmentId) {
		alert('Opening edit form for assignment ' + assignmentId);
		// Implementation for edit form
	}

	function deleteAssignment(assignmentId) {
		if (confirm('Are you sure you want to delete this assignment?')) {
			alert('Deleting assignment ' + assignmentId);
			// Implementation for delete
		}
	}
</script>
<?= $this->endSection() ?>
