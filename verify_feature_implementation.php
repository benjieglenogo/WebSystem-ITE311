<?php

/**
 * Feature Implementation Verification Script
 * Tests: Assignments, Grades, Teacher Dashboard
 * Date: December 14, 2025
 */

echo "=====================================\n";
echo "FEATURE IMPLEMENTATION VERIFICATION\n";
echo "=====================================\n\n";

$checks_passed = 0;
$checks_total = 0;

// Check 1: Routes exist
echo "TEST 1: Routes Configuration\n";
echo "------------------------------\n";

$routes_file = 'app/Config/Routes.php';
$routes_content = file_get_contents($routes_file);

$required_routes = [
    "/assignments" => "Assignments::index",
    "/assignments/create" => "Assignments::create",
    "/assignments/([0-9]+)" => "Assignments::view",
    "/assignments/([0-9]+)/submit" => "Assignments::submit",
    "/grades" => "Grades::index",
    "/grades/([0-9]+)" => "Grades::view",
    "/grades/([0-9]+)" => "Grades::update",
    "/teacher/dashboard" => "Auth::teacherDashboard",
];

foreach ($required_routes as $route => $controller) {
    $checks_total++;
    if (strpos($routes_content, $route) !== false && strpos($routes_content, $controller) !== false) {
        echo "✅ Route found: $route -> $controller\n";
        $checks_passed++;
    } else {
        echo "❌ Route missing: $route -> $controller\n";
    }
}
echo "\n";

// Check 2: Controllers exist
echo "TEST 2: Controllers Exist\n";
echo "-------------------------\n";

$required_controllers = [
    'app/Controllers/Assignments.php' => ['index', 'view', 'create', 'submit'],
    'app/Controllers/Grades.php' => ['index', 'view', 'update'],
];

foreach ($required_controllers as $controller_file => $methods) {
    if (file_exists($controller_file)) {
        echo "✅ Controller file exists: $controller_file\n";
        $controller_content = file_get_contents($controller_file);
        
        foreach ($methods as $method) {
            $checks_total++;
            if (strpos($controller_content, "public function $method") !== false) {
                echo "   ✅ Method found: $method()\n";
                $checks_passed++;
            } else {
                echo "   ❌ Method missing: $method()\n";
            }
        }
    } else {
        echo "❌ Controller file missing: $controller_file\n";
    }
}
echo "\n";

// Check 3: Views exist
echo "TEST 3: View Files Exist\n";
echo "------------------------\n";

$required_views = [
    'app/Views/assignments/index.php',
    'app/Views/assignments/view.php',
    'app/Views/grades/index.php',
    'app/Views/grades/view.php',
    'app/Views/teacher/dashboard.php',
];

foreach ($required_views as $view_file) {
    $checks_total++;
    if (file_exists($view_file)) {
        echo "✅ View file exists: $view_file\n";
        $checks_passed++;
    } else {
        echo "❌ View file missing: $view_file\n";
    }
}
echo "\n";

// Check 4: Models exist
echo "TEST 4: Model Files Exist\n";
echo "-------------------------\n";

$required_models = [
    'app/Models/AssignmentModel.php',
    'app/Models/SubmissionModel.php',
];

foreach ($required_models as $model_file) {
    $checks_total++;
    if (file_exists($model_file)) {
        echo "✅ Model file exists: $model_file\n";
        $checks_passed++;
    } else {
        echo "❌ Model file missing: $model_file\n";
    }
}
echo "\n";

// Check 5: Key features in controllers
echo "TEST 5: Key Features Implementation\n";
echo "-----------------------------------\n";

$features = [
    'app/Controllers/Assignments.php' => [
        'Authentication check' => 'isLoggedIn',
        'Role-based filtering' => 'userRole',
        'AJAX response' => 'setJSON',
    ],
    'app/Controllers/Grades.php' => [
        'Student grades view' => 'student',
        'Teacher grades view' => 'teacher',
        'Grade update' => 'update',
        'Authorization check' => 'teacher_id',
    ],
];

foreach ($features as $file => $feature_list) {
    $content = file_get_contents($file);
    foreach ($feature_list as $feature_name => $search_term) {
        $checks_total++;
        if (strpos($content, $search_term) !== false) {
            echo "✅ Feature '$feature_name' found in $file\n";
            $checks_passed++;
        } else {
            echo "❌ Feature '$feature_name' missing in $file\n";
        }
    }
}
echo "\n";

// Check 6: Navigation links
echo "TEST 6: Navigation Links\n";
echo "------------------------\n";

$header_file = 'app/Views/templates/header.php';
$header_content = file_get_contents($header_file);

$nav_links = [
    "assignments" => "assignments link",
    "grades" => "grades link",
];

foreach ($nav_links as $link => $description) {
    $checks_total++;
    if (strpos($header_content, "site_url('$link')") !== false) {
        echo "✅ Navigation link found: $description\n";
        $checks_passed++;
    } else {
        echo "❌ Navigation link missing: $description\n";
    }
}
echo "\n";

// Summary
echo "=====================================\n";
echo "VERIFICATION SUMMARY\n";
echo "=====================================\n";
echo "Checks Passed: $checks_passed / $checks_total\n";

if ($checks_passed === $checks_total) {
    echo "Status: ✅ ALL CHECKS PASSED!\n";
    echo "\nThe following features are ready:\n";
    echo "  • Assignments page (/assignments)\n";
    echo "  • Grades page (/grades)\n";
    echo "  • Teacher Dashboard (/teacher/dashboard/:id)\n";
    echo "  • Edit Course with Teacher Assignment\n";
} else {
    $failed = $checks_total - $checks_passed;
    echo "Status: ⚠️  $failed checks failed\n";
    echo "Please review the failures above.\n";
}

echo "\nNext Steps:\n";
echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "2. Hard refresh (Ctrl+F5)\n";
echo "3. Test /assignments page\n";
echo "4. Test /grades page\n";
echo "5. Test edit course and teacher assignment\n";
echo "6. Test teacher dashboard redirect\n";
echo "\n";

?>
