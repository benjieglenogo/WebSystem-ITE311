<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Icon Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            padding: 20px;
            font-family: Arial, sans-serif;
        }
        .icon-test {
            margin: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            display: inline-block;
        }
        .icon-container {
            font-size: 24px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Icon Test Page</h1>

    <h2>Font Awesome Icons (should work)</h2>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-plus-circle"></i></span>
        <span>fas fa-plus-circle</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-arrow-left"></i></span>
        <span>fas fa-arrow-left</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-search"></i></span>
        <span>fas fa-search</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-edit"></i></span>
        <span>fas fa-edit</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-key"></i></span>
        <span>fas fa-key</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-user-minus"></i></span>
        <span>fas fa-user-minus</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-user-plus"></i></span>
        <span>fas fa-user-plus</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="fas fa-shield-alt"></i></span>
        <span>fas fa-shield-alt</span>
    </div>

    <h2>Bootstrap Icons (may not work)</h2>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-plus-circle"></i></span>
        <span>bi bi-plus-circle</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-arrow-left"></i></span>
        <span>bi bi-arrow-left</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-search"></i></span>
        <span>bi bi-search</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-pencil"></i></span>
        <span>bi bi-pencil</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-key"></i></span>
        <span>bi bi-key</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-person-dash"></i></span>
        <span>bi bi-person-dash</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-person-plus"></i></span>
        <span>bi bi-person-plus</span>
    </div>
    <div class="icon-test">
        <span class="icon-container"><i class="bi bi-shield-lock"></i></span>
        <span>bi bi-shield-lock</span>
    </div>

    <h2>Test Results</h2>
    <p>If you see icons above, they are working. If you see empty boxes or question marks, the icons are not loading properly.</p>

    <h3>User Management Buttons Test</h3>
    <div style="margin: 20px 0;">
        <button class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Create New User
        </button>
        <button class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </button>
    </div>

    <div style="margin: 20px 0;">
        <button class="btn btn-action btn-edit" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; font-size: 12px; border-radius: 5px;">
            <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-action btn-password" style="background-color: #17a2b8; color: white; border: none; padding: 5px 10px; font-size: 12px; border-radius: 5px;">
            <i class="fas fa-key"></i> Change Password
        </button>
        <button class="btn btn-action btn-toggle" style="background-color: #6c757d; color: white; border: none; padding: 5px 10px; font-size: 12px; border-radius: 5px;">
            <i class="fas fa-user-minus"></i> Deactivate
        </button>
        <button class="btn btn-action btn-toggle" style="background-color: #6c757d; color: white; border: none; padding: 5px 10px; font-size: 12px; border-radius: 5px;">
            <i class="fas fa-user-plus"></i> Activate
        </button>
        <span class="badge bg-secondary" title="Protected admin account">
            <i class="fas fa-shield-alt"></i> Protected
        </span>
    </div>
</body>
</html>
