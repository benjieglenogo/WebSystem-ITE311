<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="<?= base_url('/') ?>">MyCI</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/about') ?>">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= base_url('/contact') ?>">Contact</a></li>

        <?php if (session('isLoggedIn')): ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
          <?php if (session('role') === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="#">Admin Panel</a></li>
          <?php elseif (session('role') === 'teacher'): ?>
            <li class="nav-item"><a class="nav-link" href="#">My Classes</a></li>
          <?php elseif (session('role') === 'student'): ?>
            <li class="nav-item"><a class="nav-link" href="#">My Courses</a></li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/logout') ?>">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= base_url('/login') ?>">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
  </nav>


