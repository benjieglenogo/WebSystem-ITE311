<?php
/**
 * Test script to verify file upload functionality
 */

// Include necessary files
require_once 'app/Config/Paths.php';
require_once 'system/bootstrap.php';

// Set up CodeIgniter
$app = new \CodeIgniter\CodeIgniter();
$app->initialize();

// Test the materials controller
$materialsController = new \App\Controllers\Materials();

// Test data
$testData = [
    'course_id' => 1, // Assuming course ID 1 exists
    'material_file' => [
        'name' => 'test_file.pdf',
        'type' => 'application/pdf',
        'tmp_name' => 'test_file.pdf',
        'error' => 0,
        'size' => 1024
    ],
    'description' => 'Test file upload'
];

echo "Testing file upload functionality...\n";

// Check if the upload method exists and is accessible
if (method_exists($materialsController, 'ajaxUpload')) {
    echo "✓ ajaxUpload method exists\n";
} else {
    echo "✗ ajaxUpload method missing\n";
}

// Check if the display method exists
if (method_exists($materialsController, 'display')) {
    echo "✓ display method exists\n";
} else {
    echo "✗ display method missing\n";
}

// Check if the download method exists
if (method_exists($materialsController, 'download')) {
    echo "✓ download method exists\n";
} else {
    echo "✗ download method missing\n";
}

// Test database connection
try {
    $db = \Config\Database::connect();
    if ($db->connect()) {
        echo "✓ Database connection successful\n";

        // Check if materials table exists
        if ($db->tableExists('materials')) {
            echo "✓ Materials table exists\n";
        } else {
            echo "✗ Materials table missing\n";
        }

        // Check if courses table exists
        if ($db->tableExists('courses')) {
            echo "✓ Courses table exists\n";
        } else {
            echo "✗ Courses table missing\n";
        }
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

// Test file upload directory
$uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials' . DIRECTORY_SEPARATOR;
if (is_dir($uploadPath) || mkdir($uploadPath, 0755, true)) {
    echo "✓ Upload directory accessible\n";
} else {
    echo "✗ Upload directory not accessible\n";
}

echo "Test completed.\n";
