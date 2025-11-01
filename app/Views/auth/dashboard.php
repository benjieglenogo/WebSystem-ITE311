<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h1 class="mb-0 text-primary fw-bold">Dashboard</h1>
		<a href="<?= base_url('logout') ?>" class="btn btn-outline-danger rounded-3 px-4">Logout</a>
	</div>

	<div class="alert alert-info shadow-sm rounded-3" role="alert">
		Welcome, <strong><?= esc(isset($userName) ? $userName : session('userName')) ?></strong>!
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
		// Ensure we have a valid role
		if (!$roleLocal || !in_array($roleLocal, ['admin', 'teacher', 'student'])) {
			$roleLocal = 'student'; // Default fallback
		}
	?>

	<?php if ($roleLocal === 'admin'): ?>
		<!-- Admin Statistics Cards -->
		<div class="row mb-4">
			<div class="col-md-3 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Total Users</h6>
								<h2 class="mb-0 text-primary"><?= isset($widgets['users']) ? (int)$widgets['users'] : 0 ?></h2>
							</div>
							<div class="text-primary" style="font-size: 2.5rem;">
								👥
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Total Courses</h6>
								<h2 class="mb-0 text-info"><?= isset($widgets['courses']) ? (int)$widgets['courses'] : 0 ?></h2>
							</div>
							<div class="text-info" style="font-size: 2.5rem;">
								📚
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">Enrollments</h6>
								<h2 class="mb-0 text-success"><?= isset($widgets['enrollments']) ? (int)$widgets['enrollments'] : 0 ?></h2>
							</div>
							<div class="text-success" style="font-size: 2.5rem;">
								📝
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="col-md-3 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<div class="d-flex justify-content-between align-items-center">
							<div>
								<h6 class="text-muted mb-2">System Status</h6>
								<h6 class="mb-0 text-success">Active</h6>
							</div>
							<div class="text-success" style="font-size: 2.5rem;">
								✅
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- User Role Breakdown -->
		<div class="row mb-4">
			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">Students</h6>
						<h3 class="mb-0 text-primary"><?= isset($widgets['students']) ? (int)$widgets['students'] : 0 ?></h3>
					</div>
				</div>
			</div>

			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">Teachers</h6>
						<h3 class="mb-0 text-info"><?= isset($widgets['teachers']) ? (int)$widgets['teachers'] : 0 ?></h3>
					</div>
				</div>
			</div>

			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">Administrators</h6>
						<h3 class="mb-0 text-warning"><?= isset($widgets['admins']) ? (int)$widgets['admins'] : 0 ?></h3>
					</div>
				</div>
			</div>
		</div>

		<!-- User Management -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="fw-semibold mb-0">User Management</h5>
					<button class="btn btn-primary btn-sm">Add New User</button>
				</div>
				
				<?php if (isset($allUsers) && !empty($allUsers)): ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Name</th>
									<th>Email</th>
									<th>Role</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach (array_slice($allUsers, 0, 10) as $user): ?>
									<tr>
										<td><?= esc($user['id'] ?? '') ?></td>
										<td><?= esc($user['name'] ?? '') ?></td>
										<td><?= esc($user['email'] ?? '') ?></td>
										<td>
											<span class="badge bg-<?= ($user['role'] ?? '') === 'admin' ? 'warning' : (($user['role'] ?? '') === 'teacher' ? 'info' : 'primary') ?>">
												<?= esc($user['role'] ?? 'student') ?>
											</span>
										</td>
										<td>
											<button class="btn btn-sm btn-outline-primary">Edit</button>
											<button class="btn btn-sm btn-outline-danger">Delete</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
					<?php if (count($allUsers) > 10): ?>
						<div class="text-center mt-3">
							<a href="#" class="btn btn-outline-secondary">View All Users</a>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="text-muted">No users found.</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Course Management -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="fw-semibold mb-0">Course Management</h5>
					<button class="btn btn-primary btn-sm">Create New Course</button>
				</div>
				
				<?php if (isset($allCourses) && !empty($allCourses)): ?>
					<div class="row">
						<?php foreach (array_slice($allCourses, 0, 6) as $course): ?>
							<div class="col-md-6 mb-3">
								<div class="card border-0 shadow-sm h-100">
									<div class="card-body">
										<h6 class="card-title fw-semibold mb-2">
											<?= esc($course['course_name'] ?? 'Unnamed Course') ?>
										</h6>
										<?php if (isset($course['course_code'])): ?>
											<small class="text-muted d-block mb-2">
												Code: <?= esc($course['course_code']) ?>
											</small>
										<?php endif; ?>
										<p class="card-text text-muted small mb-3">
											<?= esc($course['description'] ?? 'No description available') ?>
										</p>
										<div class="d-flex gap-2">
											<button class="btn btn-sm btn-outline-primary">Edit</button>
											<button class="btn btn-sm btn-outline-info">Manage</button>
											<button class="btn btn-sm btn-outline-danger">Delete</button>
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
					<?php if (count($allCourses) > 6): ?>
						<div class="text-center mt-3">
							<a href="#" class="btn btn-outline-secondary">View All Courses</a>
						</div>
					<?php endif; ?>
				<?php else: ?>
					<div class="text-muted">No courses found.</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Upload Material Section for Admin -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Upload Course Material</h5>
				<form action="<?= base_url('materials/upload') ?>" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="course_id_admin" class="form-label">Select Course</label>
							<select class="form-select" id="course_id_admin" name="course_id" required>
								<option value="">Choose a course...</option>
								<?php if (isset($allCourses) && !empty($allCourses)): ?>
									<?php foreach ($allCourses as $course): ?>
										<option value="<?= esc($course['id']) ?>">
											<?= esc($course['course_name'] ?? 'Unnamed Course') ?>
											<?php if (isset($course['course_code'])): ?>
												(<?= esc($course['course_code']) ?>)
											<?php endif; ?>
										</option>
									<?php endforeach; ?>
								<?php else: ?>
									<option value="">No courses available</option>
								<?php endif; ?>
							</select>
						</div>
						<div class="col-md-6 mb-3">
							<label for="material_file_admin" class="form-label">Select File</label>
							<input type="file" class="form-control" id="material_file_admin" name="material_file" required>
						</div>
						<div class="col-md-2 mb-3 d-flex align-items-end">
							<button type="submit" class="btn btn-primary w-100">Upload</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- All Materials Management -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">All Course Materials</h5>
				<?php if (isset($allMaterials) && !empty($allMaterials)): ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Course</th>
									<th>File Name</th>
									<th>Uploaded</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($allMaterials as $material): ?>
									<tr>
										<td><?= esc($material['course_name'] ?? 'N/A') ?></td>
										<td><?= esc($material['file_name'] ?? 'N/A') ?></td>
										<td><?= isset($material['created_at']) ? date('M d, Y', strtotime($material['created_at'])) : 'N/A' ?></td>
										<td>
											<a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">Download</a>
											<a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this material?')">Delete</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-muted">No materials uploaded yet.</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Quick Actions</h5>
				<div class="row">
					<div class="col-md-3 mb-2">
						<a href="#" class="btn btn-outline-primary w-100">Manage Users</a>
					</div>
					<div class="col-md-3 mb-2">
						<a href="#" class="btn btn-outline-info w-100">System Settings</a>
					</div>
					<div class="col-md-3 mb-2">
						<a href="#" class="btn btn-outline-success w-100">View Reports</a>
					</div>
					<div class="col-md-3 mb-2">
						<a href="#" class="btn btn-outline-warning w-100">Backup Data</a>
					</div>
				</div>
			</div>
		</div>
	<?php elseif ($roleLocal === 'teacher'): ?>
		<!-- Teacher Statistics -->
		<div class="row mb-4">
			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">My Classes</h6>
						<h2 class="mb-0 text-primary"><?= isset($widgets['classes']) ? (int)$widgets['classes'] : 0 ?></h2>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">To Grade</h6>
						<h2 class="mb-0 text-warning"><?= isset($widgets['toGrade']) ? (int)$widgets['toGrade'] : 0 ?></h2>
					</div>
				</div>
			</div>
			<div class="col-md-4 mb-3">
				<div class="card border-0 shadow-lg rounded-4 h-100">
					<div class="card-body p-4">
						<h6 class="text-muted mb-2">Announcements</h6>
						<h2 class="mb-0 text-info"><?= isset($widgets['announcements']) ? (int)$widgets['announcements'] : 0 ?></h2>
					</div>
				</div>
			</div>
		</div>

		<!-- Upload Material Section -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Upload Course Material</h5>
				<form action="<?= base_url('materials/upload') ?>" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-4 mb-3">
							<label for="course_id" class="form-label">Select Course</label>
							<select class="form-select" id="course_id" name="course_id" required>
								<option value="">Choose a course...</option>
								<?php if (isset($teacherCourses) && !empty($teacherCourses)): ?>
									<?php foreach ($teacherCourses as $course): ?>
										<option value="<?= esc($course['id']) ?>">
											<?= esc($course['course_name'] ?? 'Unnamed Course') ?>
											<?php if (isset($course['course_code'])): ?>
												(<?= esc($course['course_code']) ?>)
											<?php endif; ?>
										</option>
									<?php endforeach; ?>
								<?php else: ?>
									<option value="">No courses available</option>
								<?php endif; ?>
							</select>
						</div>
						<div class="col-md-6 mb-3">
							<label for="material_file" class="form-label">Select File</label>
							<input type="file" class="form-control" id="material_file" name="material_file" required>
						</div>
						<div class="col-md-2 mb-3 d-flex align-items-end">
							<button type="submit" class="btn btn-primary w-100">Upload</button>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- My Uploaded Materials -->
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">My Uploaded Materials</h5>
				<?php if (isset($materials) && !empty($materials)): ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Course</th>
									<th>File Name</th>
									<th>Uploaded</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($materials as $material): ?>
									<tr>
										<td><?= esc($material['course_name'] ?? 'N/A') ?></td>
										<td><?= esc($material['file_name'] ?? 'N/A') ?></td>
										<td><?= isset($material['created_at']) ? date('M d, Y', strtotime($material['created_at'])) : 'N/A' ?></td>
										<td>
											<a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-outline-primary">Download</a>
											<a href="<?= base_url('materials/delete/' . $material['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this material?')">Delete</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-muted">No materials uploaded yet.</div>
				<?php endif; ?>
			</div>
		</div>
	<?php elseif ($roleLocal === 'student'): ?>
		<!-- Enrolled Courses Section -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">My Enrolled Courses</h5>
				<?php
				$enrolledCoursesForDisplay = isset($enrolledCourses) ? $enrolledCourses : [];
				?>

				<?php if (empty($enrolledCoursesForDisplay)): ?>
					<div class="text-muted">You are not enrolled in any courses yet.</div>
				<?php else: ?>
					<div class="list-group">
						<?php foreach ($enrolledCoursesForDisplay as $course): ?>
							<div class="list-group-item d-flex justify-content-between align-items-center">
								<div>
									<h6 class="mb-1 fw-semibold"><?php echo esc($course['course_name'] ?? 'Unknown Course'); ?></h6>
									<p class="mb-1 text-muted"><?php echo esc($course['description'] ?? 'No description available'); ?></p>
									<?php if (isset($course['enrollment_date']) && !empty($course['enrollment_date'])): ?>
										<small class="text-muted">Enrolled: <?php echo date('M d, Y', strtotime($course['enrollment_date'])); ?></small>
									<?php endif; ?>
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
				try {
					$enrollmentModel = new \App\Models\EnrollmentModel();
					$user_id = session()->get('userId');
					$availableCourses = $user_id ? $enrollmentModel->getAvailableCourses($user_id) : [];
				} catch (\Exception $e) {
					$availableCourses = [];
				}
				?>

				<?php if (empty($availableCourses)): ?>
					<div class="text-muted">No courses available for enrollment.</div>
				<?php else: ?>
					<div class="row">
						<?php foreach ($availableCourses as $course): ?>
							<div class="col-md-6 mb-3">
								<div class="card h-100 border-0 shadow-sm">
									<div class="card-body">
										<h6 class="card-title fw-semibold"><?php echo esc($course['course_name'] ?? 'Unknown Course'); ?></h6>
										<p class="card-text text-muted"><?php echo esc($course['description'] ?? 'No description available'); ?></p>
										<?php
										$isEnrolled = false;
										if ($user_id && isset($course['id'])) {
											try {
												$isEnrolled = $enrollmentModel->isAlreadyEnrolled($user_id, $course['id']);
											} catch (\Exception $e) {
												$isEnrolled = false;
											}
										}
										?>
										<button class="btn btn-primary enroll-btn"
												data-course-id="<?php echo esc($course['id'] ?? ''); ?>"
												<?php echo $isEnrolled ? 'disabled' : ''; ?>>
											<?php echo $isEnrolled ? 'Already Enrolled' : 'Enroll'; ?>
										</button>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Course Materials Section -->
		<div class="card border-0 shadow-lg rounded-4 mb-4">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Course Materials</h5>
				<?php 
				$materialsForDisplay = isset($materials) ? $materials : [];
				if (!empty($materialsForDisplay)): ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Course</th>
									<th>File Name</th>
									<th>Uploaded</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($materialsForDisplay as $material): ?>
									<tr>
										<td><?= esc($material['course_name'] ?? 'N/A') ?></td>
										<td><?= esc($material['file_name'] ?? 'N/A') ?></td>
										<td>
											<?php 
											if (isset($material['created_at']) && !empty($material['created_at'])) {
												try {
													echo date('M d, Y', strtotime($material['created_at']));
												} catch (\Exception $e) {
													echo 'N/A';
												}
											} else {
												echo 'N/A';
											}
											?>
										</td>
										<td>
											<?php if (isset($material['id'])): ?>
												<a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-sm btn-primary">Download</a>
											<?php else: ?>
												<span class="text-muted">N/A</span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="text-muted">
						<?php 
						$studentEnrolledCourses = isset($enrolledCoursesForDisplay) ? $enrolledCoursesForDisplay : [];
						if (empty($studentEnrolledCourses)): ?>
							You need to enroll in courses to access materials.
						<?php else: ?>
							No materials available for your enrolled courses yet.
						<?php endif; ?>
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
						<h3 class="text-primary mb-2"><?php echo isset($enrolledCoursesForDisplay) ? count($enrolledCoursesForDisplay) : 0; ?></h3>
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
