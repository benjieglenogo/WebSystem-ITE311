<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
	<!-- Course Search Bar (added for dashboard) -->
	<div class="row mb-4">
		<div class="col-md-6">
			<form id="searchForm" class="d-flex">
				<div class="input-group">
					<input type="text" id="searchInput" class="form-control" placeholder="Search courses..." name="search_term">
					<button class="btn btn-outline-primary" type="submit">
						<i class="bi bi-search"></i> Search
					</button>
				</div>
			</form>
		</div>
	</div>
	<div id="coursesContainer" class="row mb-4"></div>
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

		<!-- Debug Panel (visible on page for troubleshooting) -->
		<div id="search-debug-panel" style="position:fixed;right:12px;bottom:12px;width:320px;background:#111827;color:#e6edf3;border-radius:8px;padding:10px;box-shadow:0 6px 18px rgba(0,0,0,0.5);z-index:1200;font-size:13px;display:none;">
			<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
				<strong>Search Debug</strong>
				<button id="search-debug-close" class="btn btn-sm btn-outline-light">Hide</button>
			</div>
			<div style="line-height:1.3">
				<div><strong>Admin query:</strong> <span id="dbg-admin-q">(none)</span></div>
				<div><strong>Admin visible:</strong> <span id="dbg-admin-count">0</span></div>
				<hr style="border-color:rgba(255,255,255,0.06);margin:6px 0">
				<div><strong>Student query:</strong> <span id="dbg-student-q">(none)</span></div>
				<div><strong>Student visible:</strong> <span id="dbg-student-count">0</span></div>
				<hr style="border-color:rgba(255,255,255,0.06);margin:6px 0">
				<div id="dbg-last-log" style="font-size:12px;color:#cbd5e1;">Awaiting actions...</div>
			</div>
			<div style="margin-top:8px;text-align:right;">
				<button id="search-debug-toggle" class="btn btn-sm btn-light">Refresh</button>
			</div>
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

				<!-- Admin Search (Client + Server) -->
				<div class="row mb-3">
					<div class="col-md-6 mb-2">
						<input type="text" id="admin-client-search" class="form-control" placeholder="Filter displayed courses (client-side)...">
					</div>
					<div class="col-md-6 mb-2 d-flex">
						<input type="text" id="admin-server-search" class="form-control me-2" placeholder="Search courses on server (press Enter or click)">
						<button id="admin-server-search-btn" class="btn btn-outline-info me-2">Search</button>
						<button id="admin-server-clear-btn" class="btn btn-outline-secondary">Clear</button>
					</div>
				</div>

				<div id="admin-server-results" class="row mb-3" style="display:none;"></div>
				
				<?php if (isset($allCourses) && !empty($allCourses)): ?>
					<div class="row admin-courses-container">
						<?php foreach (array_slice($allCourses, 0, 6) as $course): ?>
							<div class="col-md-6 mb-3" data-course-id="<?= esc($course['id'] ?? '') ?>">
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

					<!-- Student Search -->
					<div class="row mb-3">
						<div class="col-md-6 mb-2">
							<input type="text" id="student-client-search" class="form-control" placeholder="Filter available courses (client-side)...">
						</div>
						<div class="col-md-6 mb-2 d-flex">
							<input type="text" id="student-server-search" class="form-control me-2" placeholder="Search courses on server (press Enter)">
							<button id="student-server-search-btn" class="btn btn-outline-info me-2">Search</button>
							<button id="student-server-clear-btn" class="btn btn-outline-secondary">Clear</button>
						</div>
					</div>

					<div id="student-server-results" class="row mb-3" style="display:none;"></div>
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
					<div class="row available-courses-container">
						<?php foreach ($availableCourses as $course): ?>
							<div class="col-md-6 mb-3" data-course-id="<?= esc($course['id'] ?? '') ?>">
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
<script>
$(document).ready(function() {
	// Server-side search with AJAX for dashboard
	$('#searchForm').submit(function(e) {
		e.preventDefault();
		var searchTerm = $('#searchInput').val();
		$.ajax({
			url: '/courses/search',
			type: 'GET',
			data: { search_term: searchTerm },
			success: function(data) {
				var $coursesContainer = $('#coursesContainer');
				$coursesContainer.empty();
				if (data.length > 0) {
					data.forEach(function(course) {
						$coursesContainer.append(
							'<div class="col-md-4 mb-4">' +
								'<div class="card course-card">' +
									'<div class="card-body">' +
										'<h5 class="card-title">' + course.course_name + '</h5>' +
										'<p class="card-text">' + course.course_description + '</p>' +
										'<a href="/courses/view/' + course.id + '" class="btn btn-primary">View Course</a>' +
									'</div>' +
								'</div>' +
							'</div>'
						);
					});
				} else {
					$coursesContainer.html('<div class="col-12 text-center"><div class="alert alert-info">No courses found matching your search.</div></div>');
				}
			}
		});
	});
});
</script>
<?= $this->endSection() ?>
<!-- Search Results Modal -->
<div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="searchResultsModalLabel">Search Results</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="searchResultsModalBody">
				<!-- Results inserted here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<script>
// Client-side filtering and AJAX server-side search for courses
$(function(){
	// Helper to render server results into a container
	function renderServerResults(container, courses){
		container.empty();
		if (!courses || courses.length === 0) {
			container.html('<div class="text-muted">No courses found.</div>').show();
			return;
		}

		courses.forEach(function(course){
			var card = `
				<div class="col-md-6 mb-3">
					<div class="card h-100 border-0 shadow-sm">
						<div class="card-body">
							<h6 class="card-title fw-semibold">${escapeHtml(course.course_name || 'Unnamed Course')}</h6>
							<p class="card-text text-muted">${escapeHtml(course.description || 'No description available')}</p>
							<button class="btn btn-primary enroll-btn" data-course-id="${course.id}">Enroll</button>
						</div>
					</div>
				</div>
			`;
			container.append(card);
		});

		container.show();
	}

	// Escape HTML helper
	function escapeHtml(text) {
		if (!text) return '';
		return String(text).replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]; });
	}

	// Debounce helper
	function debounce(fn, wait) {
		var t;
		return function() {
			var args = arguments, ctx = this;
			clearTimeout(t);
			t = setTimeout(function(){ fn.apply(ctx, args); }, wait || 200);
		};
	}

	// Escape RegExp for highlighting
	function escapeRegExp(s) {
		return String(s).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
	}

	// Store original texts (used for restoring after highlight)
	function storeOriginalText($card){
		var $title = $card.find('.card-title');
		var $desc = $card.find('.card-text');
		if ($title.length && !$title.attr('data-orig')) $title.attr('data-orig', $title.text());
		if ($desc.length && !$desc.attr('data-orig')) $desc.attr('data-orig', $desc.text());
	}

	function highlightTextInElement($el, q){
		if (!$el || !$el.length) return;
		var orig = $el.attr('data-orig') || $el.text();
		if (!q) { $el.html(orig); return; }
		var reg = new RegExp('(' + escapeRegExp(q) + ')', 'ig');
		var highlighted = orig.replace(reg, '<mark>$1</mark>');
		$el.html(highlighted);
	}

	function resetHighlight($el){
		if (!$el || !$el.length) return;
		if ($el.attr('data-orig')) $el.html($el.attr('data-orig'));
		else $el.find('mark').each(function(){ $(this).replaceWith($(this).text()); });
	}

	function highlightCard($card, q){
		if (!q) { resetHighlight($card.find('.card-title')); resetHighlight($card.find('.card-text')); return; }
		storeOriginalText($card);
		highlightTextInElement($card.find('.card-title'), q);
		highlightTextInElement($card.find('.card-text'), q);
	}

	// CLIENT-SIDE: admin filter (debounced + highlight)
	$('#admin-client-search').on('input', debounce(function(){
		var q = $.trim($(this).val()).toLowerCase();
		if (window.console && console.log) console.log('[SEARCH DEBUG] admin input:', q);
		var $cards = $('.admin-courses-container').find('[data-course-id]');
		if (!q) { $cards.show(); $cards.each(function(){ resetHighlight($(this).find('.card-title')); resetHighlight($(this).find('.card-text')); }); return; }
		$cards.each(function(){
			var $c = $(this);
			var title = ($c.find('.card-title').text() || '').toLowerCase();
			var desc = ($c.find('.card-text').text() || '').toLowerCase();
			var text = (title + ' ' + desc).toLowerCase();
			var match = text.indexOf(q) !== -1;
			$c.toggle(match);
			if (match) { highlightCard($c, q); }
			else { resetHighlight($c.find('.card-title')); resetHighlight($c.find('.card-text')); }
		});
		if (window.console && console.log) console.log('[SEARCH DEBUG] admin matches:', $('.admin-courses-container').find('[data-course-id]:visible').length);
		updateDebugPanel();
	}, 200));

	// CLIENT-SIDE: student filter for available courses (debounced + highlight)
	$('#student-client-search').on('input', debounce(function(){
		var q = $.trim($(this).val()).toLowerCase();
		if (window.console && console.log) console.log('[SEARCH DEBUG] student input:', q);
		var $cards = $('.available-courses-container').find('[data-course-id]');
		if (!q) { $cards.show(); $cards.each(function(){ resetHighlight($(this).find('.card-title')); resetHighlight($(this).find('.card-text')); }); return; }
		$cards.each(function(){
			var $c = $(this);
			var title = ($c.find('.card-title').text() || '').toLowerCase();
			var desc = ($c.find('.card-text').text() || '').toLowerCase();
			var text = (title + ' ' + desc).toLowerCase();
			var match = text.indexOf(q) !== -1;
			$c.toggle(match);
			if (match) highlightCard($c, q);
			else { resetHighlight($c.find('.card-title')); resetHighlight($c.find('.card-text')); }
		});
		if (window.console && console.log) console.log('[SEARCH DEBUG] student matches:', $('.available-courses-container').find('[data-course-id]:visible').length);
		updateDebugPanel();
	}, 200));

	// SERVER-SIDE AJAX search (shared function)
	function doServerSearch(query, resultContainer, showModal){
		if (!query || query.trim() === ''){
			if (resultContainer) resultContainer.hide();
			return;
		}

		$.get('<?= base_url('course/search') ?>', { q: query })
			.done(function(resp){
				if (resp && resp.success){
					var data = resp.data || [];
					if (showModal) {
						// Hide original containers so only modal is visible
						$('.admin-courses-container').find('[data-course-id]').hide();
						$('.available-courses-container').find('[data-course-id]').hide();
						// Render into modal
						var body = $('#searchResultsModalBody');
						body.empty();
						if (data.length === 0) {
							body.html('<div class="text-muted">No courses found.</div>');
						} else {
							var row = $('<div class="row"></div>');
							data.forEach(function(course){
								var card = `
									<div class="col-md-6 mb-3" data-course-id="${escapeHtml(String(course.id))}">
										<div class="card h-100 border-0 shadow-sm">
											<div class="card-body">
												<h6 class="card-title fw-semibold">${escapeHtml(course.course_name || 'Unnamed Course')}</h6>
												<p class="card-text text-muted">${escapeHtml(course.description || 'No description available')}</p>
												<button class="btn btn-primary enroll-btn" data-course-id="${escapeHtml(String(course.id))}">Enroll</button>
											</div>
										</div>
									</div>
								`;
								row.append(card);
							});
								body.append(row);
								// Highlight matches in modal results
								var q = String(query || '').trim();
								if (q) {
									body.find('[data-course-id]').each(function(){
										highlightCard($(this), q.toLowerCase());
									});
								}
						}
						var modalEl = document.getElementById('searchResultsModal');
						var modal = new bootstrap.Modal(modalEl);
						modal.show();
					} else if (resultContainer) {
						// Use returned IDs to show/hide original cards and append new ones
						var resultIds = data.map(function(c){ return String(c.id); });

						// Hide all original cards first
						if (resultContainer.attr('id') === 'admin-server-results') {
							$('.admin-courses-container').find('[data-course-id]').hide();
						} else if (resultContainer.attr('id') === 'student-server-results') {
							$('.available-courses-container').find('[data-course-id]').hide();
						}

						// For each returned course, show existing card if present, otherwise render into results container
						resultContainer.empty();
						if (data.length === 0) {
							resultContainer.html('<div class="text-muted">No courses found.</div>').show();
						} else {
							data.forEach(function(course){
								var idStr = String(course.id);
								var existing = $('[data-course-id="' + idStr + '"]');
								if (existing.length) {
									existing.show();
								} else {
									// render new card into result container
									var card = `
										<div class="col-md-6 mb-3" data-course-id="${escapeHtml(idStr)}">
											<div class="card h-100 border-0 shadow-sm">
												<div class="card-body">
													<h6 class="card-title fw-semibold">${escapeHtml(course.course_name || 'Unnamed Course')}</h6>
													<p class="card-text text-muted">${escapeHtml(course.description || 'No description available')}</p>
													<button class="btn btn-primary enroll-btn" data-course-id="${escapeHtml(idStr)}">Enroll</button>
												</div>
											</div>
										</div>
									`;
									resultContainer.append(card);
									// highlight freshly appended card
									if (query && String(query || '').trim()) {
										highlightCard(resultContainer.find('[data-course-id="' + idStr + '"]'), String(query).trim().toLowerCase());
									}
								}
							});
							resultContainer.show();
						}
					}
				} else {
					if (resultContainer) resultContainer.html('<div class="text-muted">Search failed.</div>').show();
					else $('#searchResultsModalBody').html('<div class="text-muted">Search failed.</div>');
				}
			})
			.fail(function(){
				if (resultContainer) resultContainer.html('<div class="text-muted">An error occurred while searching.</div>').show();
				else $('#searchResultsModalBody').html('<div class="text-muted">An error occurred while searching.</div>');
			});
	}

	// Admin server search events (modal results)
	$('#admin-server-search-btn').on('click', function(){
		var q = $('#admin-server-search').val();
		doServerSearch(q, $('#admin-server-results'), true);
	});
	$('#admin-server-search').on('keyup', function(e){ if (e.key === 'Enter') { $('#admin-server-search-btn').click(); } });
	$('#admin-server-clear-btn').on('click', function(){
		$('#admin-server-search').val('');
		$('#admin-server-results').hide().empty();
		// Hide/clear modal if open
		try { var m = bootstrap.Modal.getInstance(document.getElementById('searchResultsModal')); if (m) m.hide(); } catch(e){}
		$('.admin-courses-container').show().find('[data-course-id]').show();
	});

	// Student server search events (modal results)
	$('#student-server-search-btn').on('click', function(){
		var q = $('#student-server-search').val();
		doServerSearch(q, $('#student-server-results'), true);
	});
	$('#student-server-search').on('keyup', function(e){ if (e.key === 'Enter') { $('#student-server-search-btn').click(); } });
	$('#student-server-clear-btn').on('click', function(){
		$('#student-server-search').val('');
		$('#student-server-results').hide().empty();
		try { var m2 = bootstrap.Modal.getInstance(document.getElementById('searchResultsModal')); if (m2) m2.hide(); } catch(e){}
		$('.available-courses-container').show().find('[data-course-id]').show();
	});

	// Delegated enroll handler for dynamically added enroll buttons (works for server results)
	$(document).on('click', '.enroll-btn', function(e){
		e.preventDefault();
		var btn = $(this);
		var courseId = btn.data('course-id');
		if (!courseId) return;
		if (btn.data('processing')) return;
		var originalText = btn.text();
		btn.data('processing', true).prop('disabled', true).text('Enrolling...');

		$.post('<?= base_url('course/enroll') ?>', { course_id: courseId })
			.done(function(response){
				if (response && response.success){
					btn.removeClass('btn-primary').addClass('btn-success').text('Enrolled').prop('disabled', true);
				} else {
					alert(response.message || 'Failed to enroll.');
					btn.prop('disabled', false).text(originalText);
				}
			})
			.fail(function(){
				alert('An error occurred enrolling in the course.');
				btn.prop('disabled', false).text(originalText);
			})
			.always(function(){ btn.data('processing', false); });
	});

	// Debug panel: update function and toggle handlers
	function updateDebugPanel(){
		var adminQ = $.trim($('#admin-client-search').val() || '');
		var adminCount = $('.admin-courses-container').find('[data-course-id]:visible').length;
		var studentQ = $.trim($('#student-client-search').val() || '');
		var studentCount = $('.available-courses-container').find('[data-course-id]:visible').length;
		$('#dbg-admin-q').text(adminQ || '(none)');
		$('#dbg-admin-count').text(adminCount);
		$('#dbg-student-q').text(studentQ || '(none)');
		$('#dbg-student-count').text(studentCount);
		$('#dbg-last-log').text('Last update: ' + new Date().toLocaleTimeString());
		// ensure panel visible when there are actions
		$('#search-debug-panel').show();
	}

	$('#search-debug-toggle').on('click', function(){ updateDebugPanel(); });
	$('#search-debug-close').on('click', function(){ $('#search-debug-panel').hide(); });

    // Initialize debug panel visibility if running locally
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        $('#search-debug-panel').show();
        updateDebugPanel();
    }
});
</script>
