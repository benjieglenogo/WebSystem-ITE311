<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Grades Page -->
	<style>
		.grades-container {
			background-color: #f8f9fa;
			min-height: 100vh;
			padding: 20px;
		}

		.grades-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 30px;
		}

		.grades-title {
			color: #343a40;
			font-weight: 600;
			font-size: 28px;
		}

		.grade-card {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 20px;
			margin-bottom: 15px;
			border-left: 4px solid #007bff;
			transition: transform 0.2s ease, box-shadow 0.2s ease;
		}

		.grade-card:hover {
			transform: translateY(-2px);
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
		}

		.grade-item {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 15px;
			border-bottom: 1px solid #f0f0f0;
		}

		.grade-item:last-child {
			border-bottom: none;
		}

		.grade-subject {
			flex: 1;
		}

		.grade-subject-name {
			font-weight: 600;
			color: #333;
			margin-bottom: 5px;
		}

		.grade-subject-meta {
			font-size: 12px;
			color: #999;
		}

		.grade-value {
			display: flex;
			align-items: center;
			gap: 20px;
			margin-left: 20px;
		}

		.grade-score {
			font-size: 24px;
			font-weight: 700;
			color: #007bff;
			min-width: 60px;
			text-align: center;
		}

		.grade-bar {
			width: 100px;
			height: 8px;
			background: #e9ecef;
			border-radius: 4px;
			overflow: hidden;
		}

		.grade-bar-fill {
			height: 100%;
			background: linear-gradient(90deg, #28a745, #007bff);
			transition: width 0.3s ease;
		}

		.grade-status {
			display: inline-block;
			padding: 5px 10px;
			border-radius: 5px;
			font-size: 11px;
			font-weight: 600;
			background-color: #d1ecf1;
			color: #0c5460;
		}

		.no-grades {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			padding: 40px;
			text-align: center;
			color: #999;
		}

		.summary-cards {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 15px;
			margin-bottom: 30px;
		}

		.summary-card {
			background: white;
			padding: 20px;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			text-align: center;
		}

		.summary-card h4 {
			color: #666;
			margin-bottom: 10px;
			font-size: 14px;
		}

		.summary-card .value {
			font-size: 28px;
			font-weight: 700;
			color: #007bff;
		}

		.grades-table {
			background: white;
			border-radius: 8px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			overflow: hidden;
		}

		.table {
			margin-bottom: 0;
		}

		.table th {
			background-color: #f8f9fa;
			border-bottom: 2px solid #dee2e6;
			font-weight: 600;
			color: #495057;
		}

		.table td {
			vertical-align: middle;
			padding: 15px;
		}

		.grade-input {
			width: 80px;
			padding: 5px 10px;
			border: 1px solid #ced4da;
			border-radius: 4px;
		}
	</style>

	<div class="grades-container">
		<div class="container mt-5">
			<!-- Header -->
			<div class="grades-header">
				<h1 class="grades-title">📊 Grades</h1>
			</div>

			<?php if (!empty($grades) && is_array($grades)): ?>
				<!-- Summary Cards (for Students) -->
				<?php if ($userRole === 'student'): ?>
					<div class="summary-cards">
						<?php
							$total = 0;
							$count = 0;
							foreach ($grades as $grade) {
								if ($grade['grade'] != 'TBD' && is_numeric($grade['grade'])) {
									$total += (int)$grade['grade'];
									$count++;
								}
							}
							$average = $count > 0 ? round($total / $count, 2) : 0;
						?>
						<div class="summary-card">
							<h4>Average Grade</h4>
							<div class="value"><?= $average ?>%</div>
						</div>
						<div class="summary-card">
							<h4>Total Courses</h4>
							<div class="value"><?= count($grades) ?></div>
						</div>
						<div class="summary-card">
							<h4>Active Courses</h4>
							<div class="value"><?= count(array_filter($grades, function($g) { return $g['status'] === 'active'; })) ?></div>
						</div>
					</div>

					<!-- Student Grades List -->
					<div class="grade-card">
						<?php foreach ($grades as $grade): ?>
							<div class="grade-item">
								<div class="grade-subject">
									<div class="grade-subject-name"><?= esc($grade['course_name']) ?></div>
									<div class="grade-subject-meta">
										<?= esc($grade['course_code']) ?> • Taught by <?= esc($grade['teacher_name']) ?>
									</div>
								</div>
								<div class="grade-value">
									<div class="grade-score">
										<?= $grade['grade'] !== 'TBD' ? (int)$grade['grade'] . '%' : 'TBD' ?>
									</div>
									<div class="grade-bar">
										<div class="grade-bar-fill" style="width: <?= $grade['percentage'] ?>%"></div>
									</div>
									<span class="grade-status"><?= esc($grade['status']) ?></span>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

				<?php elseif ($userRole === 'teacher' || $userRole === 'admin'): ?>
					<!-- Teacher/Admin Grades Table -->
					<div class="grades-table">
						<table class="table">
							<thead>
								<tr>
									<th>Course</th>
									<th>Student ID</th>
									<th>Student Name</th>
									<th>Grade</th>
									<th>Percentage</th>
									<th>Status</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($grades as $grade): ?>
									<tr>
										<td><?= esc($grade['course_name']) ?></td>
										<td><?= esc($grade['student_id']) ?></td>
										<td><?= esc($grade['student_name']) ?></td>
										<td><?= $grade['grade'] !== 'TBD' ? (int)$grade['grade'] : 'TBD' ?></td>
										<td>
											<div class="grade-bar" style="width: 100px;">
												<div class="grade-bar-fill" style="width: <?= $grade['percentage'] ?>%"></div>
											</div>
										</td>
										<td>
											<span class="grade-status"><?= esc($grade['status']) ?></span>
										</td>
										<td>
											<button class="btn btn-sm btn-primary" onclick="editGrade(<?= $grade['course_id'] ?>)">
												<i class="bi bi-pencil"></i> Edit
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>

			<?php else: ?>
				<div class="no-grades">
					<h4>📊 No Grades Available</h4>
					<p>
						<?php if ($userRole === 'student'): ?>
							You have not been enrolled in any courses yet, or grades have not been posted.
						<?php else: ?>
							No grades data available for your courses.
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
		</div>
	</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
	function editGrade(courseId) {
		alert('Opening grade editor for course ' + courseId);
		// Implementation for editing grades
	}
</script>
<?= $this->endSection() ?>
