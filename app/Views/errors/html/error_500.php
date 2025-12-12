<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Internal Server Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { max-width: 600px; margin: 0 auto; }
        .error-code { color: #e74c3c; font-size: 72px; font-weight: bold; }
        .error-message { color: #666; }
        .error-details { background: #f8f8f8; padding: 20px; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <h1>Internal Server Error</h1>
        <p class="error-message">Something went wrong on our end. Please try again later or contact support.</p>

        <div class="error-details">
            <h3>What happened?</h3>
            <p>A server error occurred while processing your request.</p>

            <h3>What can you do?</h3>
            <ul>
                <li>Try refreshing the page</li>
                <li>Go back and try again</li>
                <li>Contact support if the problem persists</li>
            </ul>
        </div>

        <a href="<?= base_url() ?>" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">Return to Home</a>
    </div>
</body>
</html>
