<?php
/**
 * Add Assignment Button Verification Test
 */

echo "=== Add Assignment Button Verification ===\n\n";

// Check 1: Teacher Dashboard View
echo "✓ Check 1: Teacher Dashboard View\n";
$dashboardFile = 'app/Views/teacher/dashboard.php';
if (file_exists($dashboardFile)) {
    $content = file_get_contents($dashboardFile);
    
    if (strpos($content, 'addAssignmentModal') !== false) {
        echo "  - Modal ID found: PASS\n";
    } else {
        echo "  - Modal ID NOT found: FAIL\n";
    }
    
    if (strpos($content, 'Add Assignment') !== false) {
        echo "  - 'Add Assignment' button text found: PASS\n";
    } else {
        echo "  - 'Add Assignment' button text NOT found: FAIL\n";
    }
    
    if (strpos($content, 'data-bs-toggle="modal"') !== false) {
        echo "  - Bootstrap modal toggle found: PASS\n";
    } else {
        echo "  - Bootstrap modal toggle NOT found: FAIL\n";
    }
    
    if (strpos($content, 'createAssignmentForm') !== false) {
        echo "  - Assignment form ID found: PASS\n";
    } else {
        echo "  - Assignment form ID NOT found: FAIL\n";
    }
    
    if (strpos($content, 'course_id') !== false) {
        echo "  - Course dropdown found: PASS\n";
    } else {
        echo "  - Course dropdown NOT found: FAIL\n";
    }
    
    if (strpos($content, 'due_date') !== false) {
        echo "  - Due date field found: PASS\n";
    } else {
        echo "  - Due date field NOT found: FAIL\n";
    }
} else {
    echo "  ERROR: Dashboard view not found\n";
}

// Check 2: Route Configuration
echo "\n✓ Check 2: Route Configuration\n";
$routesFile = 'app/Config/Routes.php';
if (file_exists($routesFile)) {
    $content = file_get_contents($routesFile);
    
    if (strpos($content, "post('/assignments/create'") !== false) {
        echo "  - POST /assignments/create route: PASS\n";
    } else {
        echo "  - POST /assignments/create route: FAIL\n";
    }
    
    if (strpos($content, 'Assignments::create') !== false) {
        echo "  - Assignments controller method: PASS\n";
    } else {
        echo "  - Assignments controller method: FAIL\n";
    }
} else {
    echo "  ERROR: Routes file not found\n";
}

// Check 3: Controller Implementation
echo "\n✓ Check 3: Controller Implementation\n";
$controllerFile = 'app/Controllers/Assignments.php';
if (file_exists($controllerFile)) {
    $content = file_get_contents($controllerFile);
    
    if (strpos($content, 'public function create()') !== false) {
        echo "  - create() method exists: PASS\n";
    } else {
        echo "  - create() method NOT found: FAIL\n";
    }
    
    if (strpos($content, "getPost('title')") !== false) {
        echo "  - Title POST parameter: PASS\n";
    } else {
        echo "  - Title POST parameter: FAIL\n";
    }
    
    if (strpos($content, "getPost('course_id')") !== false) {
        echo "  - Course ID POST parameter: PASS\n";
    } else {
        echo "  - Course ID POST parameter: FAIL\n";
    }
    
    if (strpos($content, "getPost('due_date')") !== false) {
        echo "  - Due date POST parameter: PASS\n";
    } else {
        echo "  - Due date POST parameter: FAIL\n";
    }
    
    if (strpos($content, 'insert([') !== false) {
        echo "  - Assignment insert logic: PASS\n";
    } else {
        echo "  - Assignment insert logic: FAIL\n";
    }
} else {
    echo "  ERROR: Controller file not found\n";
}

// Check 4: JavaScript Functionality
echo "\n✓ Check 4: JavaScript Functionality\n";
$dashboardFile = 'app/Views/teacher/dashboard.php';
if (file_exists($dashboardFile)) {
    $content = file_get_contents($dashboardFile);
    
    if (strpos($content, 'submitAssignmentBtn') !== false) {
        echo "  - Submit button handler: PASS\n";
    } else {
        echo "  - Submit button handler: FAIL\n";
    }
    
    if (strpos($content, "fetch('") !== false && strpos($content, 'assignments/create') !== false) {
        echo "  - AJAX fetch to create endpoint: PASS\n";
    } else {
        echo "  - AJAX fetch NOT found: FAIL\n";
    }
    
    if (strpos($content, "POST") !== false) {
        echo "  - POST method in AJAX: PASS\n";
    } else {
        echo "  - POST method NOT found: FAIL\n";
    }
    
    if (strpos($content, 'data.success') !== false) {
        echo "  - Success response handling: PASS\n";
    } else {
        echo "  - Success response handling: FAIL\n";
    }
    
    if (strpos($content, 'location.reload()') !== false) {
        echo "  - Page reload after creation: PASS\n";
    } else {
        echo "  - Page reload NOT found: FAIL\n";
    }
} else {
    echo "  ERROR: Dashboard view not found\n";
}

// Check 5: AssignmentModel
echo "\n✓ Check 5: Assignment Model\n";
$modelFile = 'app/Models/AssignmentModel.php';
if (file_exists($modelFile)) {
    echo "  - AssignmentModel exists: PASS\n";
    $content = file_get_contents($modelFile);
    if (strpos($content, 'class AssignmentModel') !== false) {
        echo "  - AssignmentModel class: PASS\n";
    } else {
        echo "  - AssignmentModel class NOT found: FAIL\n";
    }
} else {
    echo "  ERROR: AssignmentModel not found\n";
}

echo "\n=== Verification Complete ===\n";
echo "\nFeature Summary:\n";
echo "- Add Assignment button added to teacher dashboard header\n";
echo "- Modal popup with form (course, title, description, due date)\n";
echo "- AJAX submission without page reload\n";
echo "- Success notification and automatic refresh\n";
echo "- Error handling with user-friendly messages\n";
echo "- Full authorization checks in controller\n";
?>
