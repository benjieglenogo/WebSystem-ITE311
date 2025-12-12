<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - My Learning System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .error-container {
            max-width: 600px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .error-title {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .error-message {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .back-button {
            margin: 10px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <h2 class="error-title">Page Not Found</h2>
        <p class="error-message">The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.</p>

        <div class="d-flex justify-content-center">
            <a href="javascript:history.back()" class="btn btn-outline-secondary back-button">Go Back</a>
            <a href="<?= base_url() ?>" class="btn btn-primary back-button">Home</a>
        </div>
    </div>
</body>
</html>
