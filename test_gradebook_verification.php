<?php
/**
 * Gradebook Feature Verification Test
 */

// Test 1: Check if controller exists and can be instantiated
echo "=== Gradebook Feature Verification ===\n\n";

// Define base path for CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('SYSPATH', __DIR__ . '/system' . DIRECTORY_SEPARATOR);
define('APPPATH', __DIR__ . '/app' . DIRECTORY_SEPARATOR);

// Try to load Composer autoloader
$composerAutoload = FCPATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (!file_exists($composerAutoload)) {
    echo "ERROR: Composer autoload not found\n";
    exit(1);
}

require_once $composerAutoload;

// Check 1: Routes file exists
echo "✓ Check 1: Routes file exists\n";
$routesFile = APPPATH . 'Config' . DIRECTORY_SEPARATOR . 'Routes.php';
if (file_exists($routesFile)) {
    echo "  Status: PASS\n";
    
    // Check if gradebook routes are defined
    $routesContent = file_get_contents($routesFile);
    if (strpos($routesContent, "Gradebook::index") !== false &&
        strpos($routesContent, "Gradebook::viewCourse") !== false &&
        strpos($routesContent, "Gradebook::updateGrades") !== false) {
        echo "  - Gradebook routes found: PASS\n";
    } else {
        echo "  - Gradebook routes not found: FAIL\n";
    }
} else {
    echo "  Status: FAIL - Routes file not found\n";
}

// Check 2: Controller exists
echo "\n✓ Check 2: Gradebook controller exists\n";
$controllerFile = APPPATH . 'Controllers' . DIRECTORY_SEPARATOR . 'Gradebook.php';
if (file_exists($controllerFile)) {
    echo "  Status: PASS\n";
    
    // Verify controller methods
    $controllerContent = file_get_contents($controllerFile);
    $methods = ['index', 'viewCourse', 'updateGrades'];
    foreach ($methods as $method) {
        if (strpos($controllerContent, "public function $method") !== false) {
            echo "  - Method '$method' found: PASS\n";
        } else {
            echo "  - Method '$method' NOT found: FAIL\n";
        }
    }
} else {
    echo "  Status: FAIL - Controller file not found\n";
}

// Check 3: Views exist
echo "\n✓ Check 3: Gradebook views exist\n";
$viewsPath = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'gradebook' . DIRECTORY_SEPARATOR;
if (is_dir($viewsPath)) {
    echo "  Directory: PASS\n";
    
    $views = ['index.php', 'course.php'];
    foreach ($views as $view) {
        if (file_exists($viewsPath . $view)) {
            echo "  - View '$view': PASS\n";
        } else {
            echo "  - View '$view': FAIL\n";
        }
    }
} else {
    echo "  Directory: FAIL - Views directory not found\n";
}

// Check 4: Models exist and are accessible
echo "\n✓ Check 4: Required models exist\n";
$models = ['CourseModel', 'EnrollmentModel', 'UserModel'];
foreach ($models as $model) {
    $modelFile = APPPATH . 'Models' . DIRECTORY_SEPARATOR . $model . '.php';
    if (file_exists($modelFile)) {
        echo "  - Model '$model': PASS\n";
    } else {
        echo "  - Model '$model': FAIL\n";
    }
}

// Check 5: Navigation links
echo "\n✓ Check 5: Navigation links in header\n";
$headerFile = APPPATH . 'Views' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'header.php';
if (file_exists($headerFile)) {
    $headerContent = file_get_contents($headerFile);
    if (strpos($headerContent, 'gradebook') !== false) {
        echo "  Gradebook link found: PASS\n";
    } else {
        echo "  Gradebook link NOT found: FAIL\n";
    }
} else {
    echo "  Header file not found: FAIL\n";
}

echo "\n=== Verification Complete ===\n";
?>
