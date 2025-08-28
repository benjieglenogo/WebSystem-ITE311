<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?> - MyCI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Body with soft gradient */
    body {
      background: linear-gradient(135deg, #fdfbfb, #ebedee);
      min-height: 100vh;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }

    /* Navbar */
    .navbar {
      background: white;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 600;
      color: #0d6efd;
      letter-spacing: 1px;
    }

    .navbar .nav-link {
      color: #555;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
      color: #0d6efd;
    }

    /* Page container */
    .container {
      margin-top: 70px;
      background: white;
      padding: 35px 30px;
      border-radius: 16px;
      box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      border: none;
      font-weight: 500;
    }

    .btn-primary:hover {
      opacity: 0.9;
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 50px;
      color: #666;
      font-size: 0.9rem;
    }

  </style>
</head>
<body>
  <!-- Navbar -->
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
        </ul>
      </div>
    </div>
  </nav>

  <!-- Page Content -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

  <!-- Footer -->
  <footer>
    &copy; <?= date('Y') ?> All right reserved ITE-311.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
