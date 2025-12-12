<?php

use CodeIgniter\Boot;
use Config\Paths;

/*
 *---------------------------------------------------------------
 * CHECK PHP VERSION
 *---------------------------------------------------------------
 */

$minPhpVersion = '8.1'; // If you update this, don't forget to update `spark`.
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION,
    );

    header('HTTP/1.1 503 Service Unavailable.', true, 503);
    echo $message;

    exit(1);
}

// Debug mode: enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

/*
 *---------------------------------------------------------------
 * SET THE CURRENT DIRECTORY
 *---------------------------------------------------------------
 */

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Ensure the current directory is pointing to the front controller's directory
if (getcwd() . DIRECTORY_SEPARATOR !== FCPATH) {
    chdir(FCPATH);
}

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// LOAD OUR PATHS CONFIG FILE
// This is the line that might need to be changed, depending on your folder structure.
require FCPATH . '/app/Config/Paths.php';
// ^^^ Change this line if you move your application folder

$paths = new Paths();

// LOAD THE FRAMEWORK BOOTSTRAP FILE
require $paths->systemDirectory . '/Boot.php';

// Check for debugbar requests and handle them before normal bootstrapping
if (isset($_GET['debugbar'])) {
    // Serve the toolbarloader.js directly for ?debugbar requests
    header('Content-Type: application/javascript');

    // Read the toolbarloader.js file and replace the placeholder
    $toolbarLoaderPath = $paths->systemDirectory . '/Debug/Toolbar/Views/toolbarloader.js';
    if (file_exists($toolbarLoaderPath)) {
        $content = file_get_contents($toolbarLoaderPath);
        $content = str_replace('{url}', rtrim((isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']), '/'), $content);
        echo $content;
        exit;
    } else {
        // Fallback if file not found
        echo "console.error('Debugbar loader not found');";
        exit;
    }
}

if (isset($_GET['debugbar_time'])) {
    // For debugbar_time requests, we need to serve the debugbar HTML
    // This is more complex, so let's handle it through the normal flow
    // but we'll set a flag to indicate it's a debugbar request
    define('DEBUGBAR_TIME_REQUEST', true);
}

exit(Boot::bootWeb($paths));
