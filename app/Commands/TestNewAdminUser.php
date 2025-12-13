<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestNewAdminUser extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:new-admin-user';
    protected $description = 'Tests that new admin users are not protected and can be edited';

    public function run(array $params)
    {
        $userModel = new \App\Models\UserModel();

        CLI::write('Testing New Admin User Protection', 'yellow');
        CLI::newLine();

        // Test 1: Check current admin users
        CLI::write('Test 1: Current admin users...', 'blue');
        $adminUsers = $userModel->where('role', 'admin')->findAll();
        foreach ($adminUsers as $adminUser) {
            CLI::write("Admin ID: {$adminUser['id']}, Name: {$adminUser['name']}, Protected: {$adminUser['is_protected']}", 'white');
        }

        // Test 2: Create a new admin user
        CLI::newLine();
        CLI::write('Test 2: Creating new admin user...', 'blue');

        $newAdminData = [
            'name' => 'New Admin',
            'email' => 'newadmin' . time() . '@example.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'status' => 'active',
            'is_protected' => 0, // This should be 0 for new admin users
        ];

        $newAdminId = $userModel->insert($newAdminData);
        CLI::write("Created new admin user with ID: $newAdminId", 'white');

        // Test 3: Check the new admin user
        CLI::newLine();
        CLI::write('Test 3: Checking new admin user protection...', 'blue');
        $newAdmin = $userModel->find($newAdminId);
        CLI::write("New admin user - ID: {$newAdmin['id']}, Name: {$newAdmin['name']}, Role: {$newAdmin['role']}, Protected: {$newAdmin['is_protected']}", 'white');

        if ($newAdmin['is_protected'] == 1) {
            CLI::write("✗ ERROR: New admin user should not be protected!", 'red');
        } else {
            CLI::write("✓ New admin user is correctly not protected", 'green');
        }

        // Test 4: Test if we can edit the new admin's role
        CLI::newLine();
        CLI::write('Test 4: Testing role edit permission for new admin...', 'blue');
        $canEditRole = $userModel->canEditRole($newAdminId);
        CLI::write("Can edit new admin's role: " . ($canEditRole ? 'YES' : 'NO'), 'white');

        if (!$canEditRole) {
            CLI::write("✗ ERROR: Should be able to edit new admin's role!", 'red');
        } else {
            CLI::write("✓ Can correctly edit new admin's role", 'green');
        }

        // Test 5: Test if we can edit the original admin's role
        CLI::newLine();
        CLI::write('Test 5: Testing role edit permission for original admin...', 'blue');
        $originalAdmin = $userModel->find(1);
        $canEditOriginalRole = $userModel->canEditRole(1);
        CLI::write("Can edit original admin's role: " . ($canEditOriginalRole ? 'YES' : 'NO'), 'white');

        if ($canEditOriginalRole) {
            CLI::write("✗ ERROR: Should not be able to edit original admin's role!", 'red');
        } else {
            CLI::write("✓ Correctly cannot edit original admin's role", 'green');
        }

        // Test 6: Test updating the new admin's role (should work)
        CLI::newLine();
        CLI::write('Test 6: Testing actual role update for new admin...', 'blue');
        $updateResult = $userModel->update($newAdminId, ['role' => 'teacher']);
        if ($updateResult) {
            CLI::write("✓ Successfully updated new admin's role to teacher", 'green');
            // Change back to admin for cleanup
            $userModel->update($newAdminId, ['role' => 'admin']);
        } else {
            CLI::write("✗ Failed to update new admin's role", 'red');
        }

        // Test 7: Test updating the original admin's role (should fail)
        CLI::newLine();
        CLI::write('Test 7: Testing actual role update for original admin...', 'blue');
        $updateOriginalResult = $userModel->update(1, ['role' => 'teacher']);
        if ($updateOriginalResult) {
            CLI::write("✗ ERROR: Should not be able to update original admin's role", 'red');
            // Revert the change if it somehow worked
            $userModel->update(1, ['role' => 'admin']);
        } else {
            CLI::write("✓ Correctly prevented updating original admin's role", 'green');
        }

        CLI::newLine();
        CLI::write('All tests completed!', 'green');
    }
}
