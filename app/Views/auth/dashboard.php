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
		<div class="card border-0 shadow-lg rounded-4 mb-3">
			<div class="card-body p-4">
				<h5 class="fw-semibold mb-3">Student Area</h5>
				<ul class="mb-0">
					<li>My courses (<?= isset($widgets['courses']) ? (int)$widgets['courses'] : 0 ?>)</li>
					<li>Pending assignments (<?= isset($widgets['assignments']) ? (int)$widgets['assignments'] : 0 ?>)</li>
					<li>Announcements (<?= isset($widgets['announcements']) ? (int)$widgets['announcements'] : 0 ?>)</li>
				</ul>
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



