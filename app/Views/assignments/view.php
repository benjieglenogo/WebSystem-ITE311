<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Assignment Details Page -->
	<style>
		.assignment-details-container {
			background-color: #f8f9fa;
			min-height: 100vh;
			padding: 20px;
		}

		.assignment-details {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
			padding: 40px;
		}

		.assignment-details h1 {
			color: #343a40;
			margin-bottom: 10px;
		}

		.assignment-meta-info {
			display: flex;
			gap: 20px;
			margin-bottom: 30px;
			padding-bottom: 20px;
			border-bottom: 1px solid #dee2e6;
		}

		.meta-item {
			display: flex;
			gap: 10px;
			align-items: center;
		}

		.meta-label {
			font-weight: 600;
			color: #666;
		}

		.meta-value {
			color: #999;
		}

		.assignment-description {
			background: #f8f9fa;
			padding: 20px;
			border-radius: 8px;
			margin-bottom: 30px;
			line-height: 1.6;
		}

		.submission-form {
			background: #f8f9fa;
			padding: 20px;
			border-radius: 8px;
		}

		.submission-form h3 {
			margin-bottom: 20px;
		}
	</style>

	<div class="assignment-details-container">
		<div class="container mt-5">
			<!-- Back Button -->
			<div class="mb-3">
				<a href="<?= base_url('assignments') ?>" class="btn btn-outline-secondary">
					<i class="bi bi-arrow-left"></i> Back to Assignments
				</a>
			</div>

			<?php if ($assignment): ?>
				<div class="assignment-details">
					<h1><?= esc($assignment['title'] ?? 'Assignment') ?></h1>

					<div class="assignment-meta-info">
						<div class="meta-item">
							<span class="meta-label">Course ID:</span>
							<span class="meta-value"><?= esc($assignment['course_id'] ?? 'N/A') ?></span>
						</div>
						<div class="meta-item">
							<span class="meta-label">Created:</span>
							<span class="meta-value"><?= date('M d, Y', strtotime($assignment['created_at'] ?? 'now')) ?></span>
						</div>
						<div class="meta-item">
							<span class="meta-label">Due Date:</span>
							<span class="meta-value" style="color: #dc3545; font-weight: 600;">
								<?= date('M d, Y', strtotime($assignment['due_date'] ?? 'now')) ?>
							</span>
						</div>
					</div>

					<div class="assignment-description">
						<h3>Description</h3>
						<?= nl2br(esc($assignment['description'] ?? 'No description provided')) ?>
					</div>

                    <!-- View Submissions Button (for teachers) -->
                    <?php if (session()->get('role') === 'teacher' || session()->get('role') === 'admin'): ?>
                        <div class="mb-4">
                            <a href="<?= base_url('assignments/' . ($assignment['id'] ?? '#') . '/submissions') ?>" class="btn btn-primary">
                                <i class="bi bi-eye"></i> View Submissions
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Submission Form (for students) -->
                    <?php if (session()->get('role') === 'student'): ?>
						<div class="submission-form">
							<h3>Submit Your Assignment</h3>
							<form id="submissionForm">
								<div class="mb-3">
									<label for="submissionText" class="form-label">Your Answer</label>
									<textarea class="form-control" id="submissionText" name="submission" rows="6" required></textarea>
								</div>
								<div class="mb-3">
									<label for="submissionFile" class="form-label">Attach File (Optional)</label>
									<input type="file" class="form-control" id="submissionFile" name="file">
									<small class="text-muted">Allowed: PDF, DOC, DOCX, TXT (Max 10MB)</small>
								</div>
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-upload"></i> Submit Assignment
								</button>
							</form>
						</div>
					<?php endif; ?>
				</div>
			<?php else: ?>
				<div class="alert alert-danger" role="alert">
					<i class="bi bi-exclamation-circle"></i> Assignment not found.
				</div>
			<?php endif; ?>
		</div>
	</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	$(document).ready(function() {
		$('#submissionForm').submit(function(e) {
			e.preventDefault();

			var formData = new FormData(this);
			var assignmentId = '<?= $assignment['id'] ?? '' ?>';

			$.ajax({
				url: '<?= base_url('assignments/') ?>' + assignmentId + '/submit',
				type: 'POST',
				data: formData,
				processData: false,
				contentType: false,
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						alert('Assignment submitted successfully!');
						window.location.href = '<?= base_url('assignments') ?>';
					} else {
						alert('Error: ' + response.message);
					}
				},
				error: function() {
					alert('An error occurred while submitting the assignment.');
				}
			});
		});
	});
</script>
<?= $this->endSection() ?>
