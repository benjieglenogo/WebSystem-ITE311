<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Course Grades Management Page -->
	<style>
		.grades-management-container {
			background-color: #f8f9fa;
			min-height: 100vh;
			padding: 20px;
		}

		.grades-management {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			padding: 30px;
		}

		.grades-header {
			border-bottom: 2px solid #dee2e6;
			padding-bottom: 20px;
			margin-bottom: 30px;
		}

		.grades-header h1 {
			color: #343a40;
			margin-bottom: 5px;
		}

		.course-info {
			font-size: 14px;
			color: #666;
		}

		.grades-table {
			width: 100%;
			border-collapse: collapse;
		}

		.grades-table th {
			background-color: #f8f9fa;
			border-bottom: 2px solid #dee2e6;
			padding: 15px;
			text-align: left;
			font-weight: 600;
			color: #495057;
		}

		.grades-table td {
			padding: 15px;
			border-bottom: 1px solid #dee2e6;
		}

		.grades-table tbody tr:hover {
			background-color: #f8f9fa;
		}

		.grade-input-cell {
			display: flex;
			gap: 10px;
			align-items: center;
		}

		.grade-input {
			width: 100px;
			padding: 8px 10px;
			border: 1px solid #ced4da;
			border-radius: 4px;
			font-size: 14px;
		}

		.grade-input:focus {
			outline: none;
			border-color: #007bff;
			box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
		}

		.save-button {
			padding: 5px 15px;
			background-color: #28a745;
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			font-size: 12px;
		}

		.save-button:hover {
			background-color: #218838;
		}

		.status-badge {
			display: inline-block;
			padding: 5px 10px;
			border-radius: 4px;
			font-size: 11px;
			font-weight: 600;
			background-color: #d1ecf1;
			color: #0c5460;
		}

		.action-buttons {
			display: flex;
			gap: 10px;
		}

		.btn-sm {
			padding: 5px 10px;
			font-size: 12px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}

		.btn-primary {
			background-color: #007bff;
			color: white;
		}

		.btn-primary:hover {
			background-color: #0056b3;
		}

		.bulk-actions {
			margin-bottom: 20px;
			padding: 15px;
			background-color: #f8f9fa;
			border-radius: 8px;
		}

		.bulk-actions label {
			margin-right: 20px;
		}

		.bulk-actions select {
			padding: 8px 10px;
			border: 1px solid #ced4da;
			border-radius: 4px;
		}

		.success-message {
			background-color: #d4edda;
			color: #155724;
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
			display: none;
		}

		.error-message {
			background-color: #f8d7da;
			color: #721c24;
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
			display: none;
		}
	</style>

	<div class="grades-management-container">
		<div class="container mt-5">
			<!-- Back Button -->
			<div class="mb-3">
				<a href="<?= base_url('grades') ?>" class="btn btn-outline-secondary">
					<i class="bi bi-arrow-left"></i> Back to Grades
				</a>
			</div>

			<?php if ($course): ?>
				<div class="grades-management">
					<div class="grades-header">
						<h1><?= esc($course['course_name']) ?></h1>
						<div class="course-info">
							Course Code: <?= esc($course['course_code']) ?> | 
							Status: <span class="status-badge"><?= esc($course['status']) ?></span>
						</div>
					</div>

					<!-- Messages -->
					<div id="successMessage" class="success-message"></div>
					<div id="errorMessage" class="error-message"></div>

					<?php if (!empty($grades) && is_array($grades)): ?>
						<table class="grades-table">
							<thead>
								<tr>
									<th>Student ID</th>
									<th>Student Name</th>
									<th>Current Grade</th>
									<th>New Grade</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grades as $grade): ?>
									<tr data-enrollment-id="<?= $grade['enrollment_id'] ?>">
										<td><?= esc($grade['student_id']) ?></td>
										<td><?= esc($grade['student_name']) ?></td>
										<td><?= $grade['grade'] !== '' ? (int)$grade['grade'] : 'TBD' ?></td>
										<td>
											<input type="number" class="grade-input" min="0" max="100" 
												   value="<?= $grade['grade'] !== '' ? (int)$grade['grade'] : '' ?>"
												   placeholder="0-100">
										</td>
										<td>
											<span class="status-badge"><?= esc($grade['status']) ?></span>
										</td>
										<td>
											<button class="btn-sm btn-primary" onclick="saveGrade(this, <?= $grade['enrollment_id'] ?>, <?= $course['id'] ?>)">
												<i class="bi bi-check"></i> Save
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else: ?>
						<p class="text-center text-muted">No students enrolled in this course yet.</p>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="alert alert-danger" role="alert">
					<i class="bi bi-exclamation-circle"></i> Course not found.
				</div>
			<?php endif; ?>
		</div>
	</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	function saveGrade(button, enrollmentId, courseId) {
		var row = $(button).closest('tr');
		var gradeInput = row.find('input.grade-input').val();

		// Validate input
		if (gradeInput === '') {
			alert('Please enter a grade (0-100)');
			return;
		}

		if (isNaN(gradeInput) || gradeInput < 0 || gradeInput > 100) {
			alert('Grade must be a number between 0 and 100');
			return;
		}

		// Send update via AJAX
		$.ajax({
			url: '<?= base_url('grades/') ?>' + courseId,
			type: 'POST',
			data: {
				enrollment_id: enrollmentId,
				grade: gradeInput,
				<?= csrf_token() ?>: '<?= csrf_hash() ?>'
			},
			dataType: 'json',
			success: function(response) {
				if (response.success) {
					// Update the display
					row.find('td:nth-child(3)').text(parseInt(gradeInput));
					
					// Show success message
					$('#successMessage').text(response.message).show();
					setTimeout(function() {
						$('#successMessage').hide();
					}, 3000);
				} else {
					$('#errorMessage').text('Error: ' + response.message).show();
				}
			},
			error: function() {
				$('#errorMessage').text('An error occurred while saving the grade.').show();
			}
		});
	}
</script>
<?= $this->endSection() ?>
