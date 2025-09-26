<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $this->renderSection('title') ?> - MyCI</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Dark background with gradient */
    body {
      background: linear-gradient(135deg, #1e1e2f, #2d2d44);
      min-height: 100vh;
      font-family: 'Segoe UI', sans-serif;
      color: #e4e4e4;
    }

    /* Navbar */
    .navbar {
      background: #232339;
      box-shadow: 0 2px 10px rgba(0,0,0,0.5);
    }

    .navbar-brand {
      font-size: 1.6rem;
      font-weight: bold;
      color: #00d4ff;
    }

    .navbar .nav-link {
      color: #ccc;
      transition: color 0.3s ease;
    }

    .navbar .nav-link:hover {
      color: #00d4ff;
    }

    /* Page container (flat cards) */
    .container {
      margin-top: 70px;
      background: #2b2b40;
      padding: 30px 28px;
      border-radius: 12px;
      box-shadow: 0 6px 14px rgba(0,0,0,0.6);
    }

    /* Buttons */
    .btn-primary {
      background: linear-gradient(135deg, #00d4ff, #007bff);
      border: none;
      font-weight: 500;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, #007bff, #0056d2);
    }

    /* Footer */
    footer {
      text-align: center;
      padding: 20px 0;
      margin-top: 50px;
      color: #aaa;
      font-size: 0.85rem;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <?= view('templates/header') ?>

  <!-- Page Content -->
  <div class="container">
    <?= $this->renderSection('content') ?>
  </div>

  <!-- Footer -->
  <footer>
    &copy; <?= date('Y') ?> ITE-311 • All rights reserved.
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
