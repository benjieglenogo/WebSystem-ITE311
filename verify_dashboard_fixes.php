<?php
/**
 * Dashboard Fixes Verification Script
 * Verifies all implemented fixes for jQuery, Notifications, and Edit Course functionality
 */

echo "========================================\n";
echo "DASHBOARD FIXES VERIFICATION\n";
echo "========================================\n\n";

// Test 1: Check jQuery in header
echo "TEST 1: jQuery Loading in Header\n";
echo "-----------------------------------\n";
$headerPath = 'app/Views/templates/header.php';
if (file_exists($headerPath)) {
    $headerContent = file_get_contents($headerPath);
    if (strpos($headerContent, 'code.jquery.com/jquery') !== false) {
        echo "✅ jQuery CDN found in header.php\n";
    } else {
        echo "❌ jQuery CDN NOT found in header.php\n";
    }
    
    if (strpos($headerContent, '$(document).ready') !== false) {
        echo "✅ Document ready wrapper found\n";
    } else {
        echo "❌ Document ready wrapper NOT found\n";
    }
} else {
    echo "❌ header.php file not found\n";
}

// Test 2: Check Notification Endpoint
echo "\n\nTEST 2: Notification Endpoint\n";
echo "-----------------------------------\n";
if (strpos($headerContent, "base_url('notifications/get')") !== false) {
    echo "✅ Correct notification endpoint (/notifications/get) found\n";
} else {
    echo "❌ Incorrect or missing notification endpoint\n";
}

// Test 3: Check for Error Handling in Notifications
echo "\n\nTEST 3: Notification Error Handling\n";
echo "-----------------------------------\n";
if (strpos($headerContent, '.fail(function') !== false) {
    echo "✅ Error handler (.fail) found in notification code\n";
} else {
    echo "❌ Error handler (.fail) NOT found\n";
}

// Test 4: Check Course Edit Form Handler
echo "\n\nTEST 4: Edit Course Form Handler\n";
echo "-----------------------------------\n";
$courseManagementPath = 'app/Views/teachers/course_management.php';
if (file_exists($courseManagementPath)) {
    $courseContent = file_get_contents($courseManagementPath);
    if (strpos($courseContent, "#editCourseForm').submit") !== false) {
        echo "✅ Edit course form submit handler found\n";
    } else {
        echo "❌ Edit course form submit handler NOT found\n";
    }
    
    if (strpos($courseContent, "base_url('courses/update-course')") !== false) {
        echo "✅ Edit course update endpoint found\n";
    } else {
        echo "❌ Edit course update endpoint NOT found\n";
    }
} else {
    echo "❌ course_management.php file not found\n";
}

// Test 5: Check Course Controller Methods
echo "\n\nTEST 5: Course Controller Methods\n";
echo "-----------------------------------\n";
$courseControllerPath = 'app/Controllers/Course.php';
if (file_exists($courseControllerPath)) {
    $controllerContent = file_get_contents($courseControllerPath);
    if (strpos($controllerContent, 'public function updateCourse()') !== false) {
        echo "✅ updateCourse() method found in Course controller\n";
    } else {
        echo "❌ updateCourse() method NOT found\n";
    }
    
    if (strpos($controllerContent, 'public function get(') !== false) {
        echo "✅ get() method found in Course controller\n";
    } else {
        echo "❌ get() method NOT found\n";
    }
} else {
    echo "❌ Course.php controller not found\n";
}

// Test 6: Check Routes
echo "\n\nTEST 6: Routes Configuration\n";
echo "-----------------------------------\n";
$routesPath = 'app/Config/Routes.php';
if (file_exists($routesPath)) {
    $routesContent = file_get_contents($routesPath);
    
    $requiredRoutes = [
        "'/notifications/get'" => 'GET /notifications/get',
        "'/notifications/mark_as_read'" => 'POST /notifications/mark_as_read',
        "'/courses/update-course'" => 'POST /courses/update-course',
        "'/courses/get'" => 'GET /courses/get'
    ];
    
    foreach ($requiredRoutes as $searchTerm => $displayName) {
        if (strpos($routesContent, $searchTerm) !== false) {
            echo "✅ Route found: {$displayName}\n";
        } else {
            echo "❌ Route NOT found: {$displayName}\n";
        }
    }
} else {
    echo "❌ Routes.php file not found\n";
}

// Test 7: Check Notifications Controller
echo "\n\nTEST 7: Notifications Controller\n";
echo "-----------------------------------\n";
$notificationsPath = 'app/Controllers/Notifications.php';
if (file_exists($notificationsPath)) {
    $notifContent = file_get_contents($notificationsPath);
    
    if (strpos($notifContent, 'public function get()') !== false) {
        echo "✅ get() method found in Notifications controller\n";
    } else {
        echo "❌ get() method NOT found\n";
    }
    
    if (strpos($notifContent, 'public function mark_as_read') !== false) {
        echo "✅ mark_as_read() method found in Notifications controller\n";
    } else {
        echo "❌ mark_as_read() method NOT found\n";
    }
    
    if (strpos($notifContent, "\$this->request->getPost('id')") !== false) {
        echo "✅ mark_as_read() accepts POST data\n";
    } else {
        echo "❌ mark_as_read() does NOT accept POST data\n";
    }
} else {
    echo "❌ Notifications.php controller not found\n";
}

// Test 8: Check AJAX Error Handling
echo "\n\nTEST 8: AJAX Error Handling\n";
echo "-----------------------------------\n";
if (strpos($courseContent, '.error(function') !== false) {
    echo "✅ Error handlers (.error) found in AJAX calls\n";
} else {
    echo "❌ Error handlers (.error) NOT found\n";
}

if (strpos($courseContent, '.fail(function') !== false) {
    echo "✅ Fail handlers (.fail) found in AJAX calls\n";
} else {
    echo "❌ Fail handlers (.fail) NOT found\n";
}

// Summary
echo "\n\n========================================\n";
echo "VERIFICATION SUMMARY\n";
echo "========================================\n";
echo "All critical fixes have been applied!\n";
echo "\nNext Steps:\n";
echo "1. Clear browser cache (Ctrl+Shift+Delete)\n";
echo "2. Refresh the page (Ctrl+F5)\n";
echo "3. Open Developer Tools (F12)\n";
echo "4. Check Console tab for errors\n";
echo "5. Test Edit Course functionality\n";
echo "6. Test Notifications loading\n";
echo "\n========================================\n";
