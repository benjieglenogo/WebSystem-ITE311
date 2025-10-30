<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Upload Material for Course ID: <?= $course_id ?></h2>
        <form action="/admin/course/<?= $course_id ?>/upload" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="material_file" class="form-label">Select File</label>
                <input type="file" class="form-control" id="material_file" name="material_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
            <a href="/admin/dashboard" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
</body>
</html>
