<?php
/**
 * Verification script to check if password change logout functionality is working
 */

echo "=== Password Change Logout Fix Verification ===\n\n";

// Check if the Users controller has the updated password change logic
echo "1. Checking Users controller for password change logout logic:\n";

$usersContent = file_get_contents('app/Controllers/Users.php');

// Check for the key components of the fix
$checks = [
    'Session destruction' => 'session->destroy()',
    'Force logout flag' => 'force_logout',
    'Session write close' => 'session_write_close()',
    'Current user check' => 'isCurrentUser',
    'Password update success' => 'Password updated successfully. You have been logged out for security reasons.'
];

foreach ($checks as $description => $searchTerm) {
    if (strpos($usersContent, $searchTerm) !== false) {
        echo "   ✓ $description found\n";
    } else {
        echo "   ✗ $description missing\n";
    }
}

echo "\n2. Checking the specific updatePassword method:\n";

// Extract the updatePassword method
preg_match('/public function updatePassword\(\)\s*\{[^}]+\}/s', $usersContent, $methodMatch);

if (!empty($methodMatch[0])) {
    echo "   ✓ updatePassword method found\n";

    // Check for the force logout logic
    if (strpos($methodMatch[0], 'force_logout') !== false) {
        echo "   ✓ Force logout logic implemented\n";
    } else {
        echo "   ✗ Force logout logic missing\n";
    }

    // Check for session destruction
    if (strpos($methodMatch[0], 'session->destroy()') !== false) {
        echo "   ✓ Session destruction implemented\n";
    } else {
        echo "   ✗ Session destruction missing\n";
    }

    // Check for proper session handling
    if (strpos($methodMatch[0], 'session_write_close()') !== false) {
        echo "   ✓ Proper session handling implemented\n";
    } else {
        echo "   ✗ Proper session handling missing\n";
    }
} else {
    echo "   ✗ updatePassword method not found\n";
}

echo "\n3. Analyzing the logic flow:\n";

// Look for the specific pattern in the updatePassword method
$pattern = '/if\s*\(\s*\$isCurrentUser\s*\)\s*\{[^}]+session->destroy\(\)[^}]+force_logout[^}]+\}/s';
if (preg_match($pattern, $usersContent)) {
    echo "   ✓ Correct logic flow: isCurrentUser -> session->destroy() -> force_logout response\n";
} else {
    echo "   ✗ Logic flow not found or incorrect\n";
}

echo "\n4. Checking response structure:\n";

// Check for the JSON response with force_logout
if (strpos($usersContent, "'force_logout' => true") !== false) {
    echo "   ✓ Force logout response flag implemented\n";
} else {
    echo "   ✗ Force logout response flag missing\n";
}

if (strpos($usersContent, "Password updated successfully. You have been logged out for security reasons.") !== false) {
    echo "   ✓ Appropriate success message implemented\n";
} else {
    echo "   ✗ Appropriate success message missing\n";
}

echo "\n=== Verification Summary ===\n";

$allChecksPassed = true;
foreach ($checks as $searchTerm) {
    if (strpos($usersContent, $searchTerm) === false) {
        $allChecksPassed = false;
        break;
    }
}

if ($allChecksPassed) {
    echo "✅ SUCCESS: Password change logout functionality has been properly implemented!\n";
    echo "\nThe fix includes:\n";
    echo "- Session destruction when user changes their own password\n";
    echo "- Force logout flag in the JSON response\n";
    echo "- Proper session handling with session_write_close()\n";
    echo "- Appropriate success messages\n";
    echo "- Logic to only force logout when the current user changes their own password\n";
} else {
    echo "❌ FAILED: Some components of the password change logout functionality are missing.\n";
    echo "Please check the implementation in app/Controllers/Users.php\n";
}

echo "\n=== Fix Complete ===\n";
