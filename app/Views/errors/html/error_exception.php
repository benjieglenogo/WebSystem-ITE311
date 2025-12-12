<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - My Learning System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .error-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .error-title {
            color: #dc3545;
            font-weight: 600;
        }
        .error-details {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 15px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .back-button {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-title">Application Error</h1>
        <p class="lead">Sorry, an error occurred while processing your request.</p>

        <div class="error-details">
            <strong>Error Type:</strong> <?= esc($type) ?><br>
            <strong>Error Message:</strong> <?= esc($message) ?><br>
            <strong>File:</strong> <?= esc($file) ?><br>
            <strong>Line:</strong> <?= esc($line) ?>
        </div>

        <?php if (isset($trace) && !empty($trace)): ?>
            <div class="mt-4">
                <h5>Stack Trace:</h5>
                <div class="error-details">
                    <pre><?= esc(print_r($trace, true)) ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <div class="back-button">
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
            <a href="<?= base_url() ?>" class="btn btn-primary">Home</a>
        </div>
    </div>
</body>
</html>
