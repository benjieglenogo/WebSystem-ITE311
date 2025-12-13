<?php declare(strict_types=1);

/**
 * Test script to verify password change logout functionality
 */

require_once __DIR__ . '/system/bootstrap.php';

// Start session for testing
$session = \Config\Services::session();
$session->start();

// Simulate a logged-in admin user
$session->set([
    'isLoggedIn' => true,
    'userId' => 1, // Assuming user ID 1 is an admin
    'userName' => 'Test Admin',
    'userEmail' => 'admin@example.com',
    'userRole' => 'admin',
]);

echo "=== Password Change Logout Test ===\n\n";

// Test Case 1: Admin changes their own password (should force logout)
echo "Test Case 1: Admin changes their own password\n";
echo "Current session before password change:\n";
echo "isLoggedIn: " . ($session->get('isLoggedIn') ? 'true' : 'false') . "\n";
echo "userId: " . $session->get('userId') . "\n";
echo "userName: " . $session->get('userName') . "\n\n";

// Simulate the password change request
$_POST = [
    'user_id' => 1, // Same as current user
    'password' => 'NewPassword123!'
];

// Create controller instance
$usersController = new \App\Controllers\Users();

// Capture output
ob_start();
try {
    $response = $usersController->updatePassword();
    $output = ob_get_clean();

    echo "Password change response:\n";
    if (method_exists($response, 'getJSON')) {
        $responseData = json_decode($response->getJSON(), true);
        print_r($responseData);
    } else {
        echo "Response object: " . get_class($response) . "\n";
    }

    echo "\nSession after password change:\n";
    echo "isLoggedIn: " . ($session->get('isLoggedIn') ? 'true' : 'false') . "\n";
    echo "userId: " . ($session->get('userId') ?? 'null') . "\n";

    if (isset($responseData['force_logout']) && $responseData['force_logout']) {
        echo "\n✅ SUCCESS: Password change forced logout as expected!\n";
    } else {
        echo "\n❌ FAILED: Expected force_logout to be true\n";
    }

} catch (Exception $e) {
    ob_end_clean();
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n";

// Test Case 2: Admin changes another user's password (should NOT force logout)
echo "Test Case 2: Admin changes another user's password\n";

// Restart session and log in again
$session->destroy();
$session->start();
$session->set([
    'isLoggedIn' => true,
    'userId' => 1, // Admin user
    'userName' => 'Test Admin',
    'userEmail' => 'admin@example.com',
    'userRole' => 'admin',
]);

echo "Current session before password change:\n";
echo "isLoggedIn: " . ($session->get('isLoggedIn') ? 'true' : 'false') . "\n";
echo "userId: " . $session->get('userId') . "\n\n";

// Simulate changing another user's password
$_POST = [
    'user_id' => 2, // Different user
    'password' => 'NewPassword123!'
];

ob_start();
try {
    $response = $usersController->updatePassword();
    $output = ob_get_clean();

    echo "Password change response:\n";
    if (method_exists($response, 'getJSON')) {
        $responseData = json_decode($response->getJSON(), true);
        print_r($responseData);
    }

    echo "\nSession after password change:\n";
    echo "isLoggedIn: " . ($session->get('isLoggedIn') ? 'true' : 'false') . "\n";
    echo "userId: " . ($session->get('userId') ?? 'null') . "\n";

    if (!isset($responseData['force_logout']) || !$responseData['force_logout']) {
        echo "\n✅ SUCCESS: Password change did not force logout as expected!\n";
    } else {
        echo "\n❌ FAILED: Expected no force_logout for different user\n";
    }

} catch (Exception $e) {
    ob_end_clean();
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
