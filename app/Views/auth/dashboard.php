<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="mb-0 text-primary fw-bold">Dashboard</h1>
		<a href="<?= base_url('logout') ?>" class="btn btn-outline-danger rounded-3 px-4">Logout</a>
	</div>

	<div class="alert alert-info shadow-sm rounded-3" role="alert">
		Welcome, <strong><?= esc(isset($userName) ? $userName : session('userName')) ?></strong>!
	</div>

	<?php 
		$roleFromSession = session('userRole'); 
		$roleLocal = isset($role) ? $role : $roleFromSession; 
		// Ensure we have a valid role
		if (!$roleLocal || !in_array($roleLocal, ['admin', 'teacher', 'student'])) {
			$roleLocal = 'student'; // Default fallback
		}
	?>

	<?php if ($roleLocal === 'admin'): ?>
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Admin Overview</h5>
				<ul class="mb-0">
					<li>Manage users (<?= isset($widgets['users']) ? (int)$widgets['users'] : 0 ?>)</li>
					<li>View system reports (<?= isset($widgets['reports']) ? (int)$widgets['reports'] : 0 ?>)</li>
					<li>Configure site settings</li>
				</ul>
			</div>
		</div>
	<?php elseif ($roleLocal === 'teacher'): ?>
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Teacher Tools</h5>
				<ul class="mb-0">
					<li>My classes (<?= isset($widgets['classes']) ? (int)$widgets['classes'] : 0 ?>)</li>
					<li>Assignments to grade (<?= isset($widgets['toGrade']) ? (int)$widgets['toGrade'] : 0 ?>)</li>
					<li>Announcements (<?= isset($widgets['announcements']) ? (int)$widgets['announcements'] : 0 ?>)</li>
				</ul>
			</div>
		</div>
	<?php elseif ($roleLocal === 'student'): ?>
		<!-- Enrolled Courses Section -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">My Enrolled Courses</h5>
				<?php
				$enrollmentModel = new \App\Models\EnrollmentModel();
				$user_id = session()->get('userId');
				$enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
				?>

				<?php if (empty($enrolledCourses)): ?>
					<div class="text-muted">You are not enrolled in any courses yet.</div>
				<?php else: ?>
					<div class="list-group">
						<?php foreach ($enrolledCourses as $course): ?>
							<div class="list-group-item d-flex justify-content-between align-items-center">
								<div>
									<h6 class="mb-1 fw-semibold"><?php echo esc($course['course_name']); ?></h6>
									<p class="mb-1 text-muted"><?php echo esc($course['description'] ?? 'No description available'); ?></p>
									<small class="text-muted">Enrolled: <?php echo date('M d, Y', strtotime($course['enrollment_date'])); ?></small>
								</div>
								<span class="badge bg-success rounded-pill">Enrolled</span>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Available Courses Section -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Available Courses</h5>
				<?php
				$availableCourses = $enrollmentModel->getAvailableCourses($user_id);
				?>

				<?php if (empty($availableCourses)): ?>
					<div class="text-muted">No courses available for enrollment.</div>
				<?php else: ?>
					<div class="row">
						<?php foreach ($availableCourses as $course): ?>
							<div class="col-md-6 mb-3">
								<div class="card h-100 border-0 shadow-sm">
									<div class="card-body">
										<h6 class="card-title fw-semibold"><?php echo esc($course['course_name']); ?></h6>
										<p class="card-text text-muted"><?php echo esc($course['description'] ?? 'No description available'); ?></p>
										<button class="btn btn-primary enroll-btn"
												data-course-id="<?php echo $course['id']; ?>"
												<?php echo $enrollmentModel->isAlreadyEnrolled($user_id, $course['id']) ? 'disabled' : ''; ?>>
											<?php echo $enrollmentModel->isAlreadyEnrolled($user_id, $course['id']) ? 'Already Enrolled' : 'Enroll'; ?>
										</button>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Student Stats -->
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">My Progress</h5>
				<div class="row text-center">
					<div class="col-md-4">
						<h3 class="text-primary mb-2"><?php echo count($enrolledCourses); ?></h3>
						<p class="mb-0">Enrolled Courses</p>
					</div>
					<div class="col-md-4">
						<h3 class="text-info mb-2"><?php echo isset($widgets['assignments']) ? (int)$widgets['assignments'] : 0; ?></h3>
						<p class="mb-0">Assignments</p>
					</div>
					<div class="col-md-4">
						<h3 class="text-warning mb-2"><?php echo isset($widgets['announcements']) ? (int)$widgets['announcements'] : 0; ?></h3>
						<p class="mb-0">Announcements</p>
					</div>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">General</h5>
				<p class="mb-0">Your role is not set. Please contact an administrator.</p>
			</div>
		</div>
	<?php endif; ?>
<?= $this->endSection() ?>
