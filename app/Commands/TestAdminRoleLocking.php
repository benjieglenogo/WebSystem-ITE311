<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAdminRoleLocking extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:admin-role-locking';
    protected $description = 'Tests the admin role locking functionality';

    public function run(array $params)
    {
        $userModel = new \App\Models\UserModel();

        CLI::write('Testing Admin Role Locking Functionality', 'yellow');
        CLI::newLine();

        // Test 1: Check if original admin user (ID 1) is protected
        CLI::write('Test 1: Checking if original admin user (ID 1) is protected...', 'blue');
        $originalAdmin = $userModel->find(1);

        if (!$originalAdmin) {
            CLI::write('No admin user found with ID 1.', 'yellow');
        } else {
            CLI::write("Original admin user ID: {$originalAdmin['id']}, Name: {$originalAdmin['name']}, Role: {$originalAdmin['role']}, Protected: {$originalAdmin['is_protected']}", 'white');
            if ($originalAdmin['role'] === 'admin' && $originalAdmin['is_protected'] != 1) {
                CLI::write("WARNING: Original admin user is not protected!", 'red');
            } else {
                CLI::write("✓ Original admin user is properly protected", 'green');
            }
        }

        // Test 1b: Check other admin users (should not be protected)
        CLI::newLine();
        CLI::write('Test 1b: Checking other admin users (should not be protected)...', 'blue');
        $otherAdminUsers = $userModel->where('role', 'admin')->where('id !=', 1)->findAll();

        if (empty($otherAdminUsers)) {
            CLI::write('No other admin users found in the database.', 'yellow');
        } else {
            foreach ($otherAdminUsers as $adminUser) {
                CLI::write("Admin user ID: {$adminUser['id']}, Name: {$adminUser['name']}, Protected: {$adminUser['is_protected']}", 'white');
                if ($adminUser['is_protected'] == 1) {
                    CLI::write("WARNING: Other admin user should not be protected!", 'red');
                } else {
                    CLI::write("✓ Other admin user is correctly not protected", 'green');
                }
            }
        }

        // Test 2: Check canEditRole method
        CLI::newLine();
        CLI::write('Test 2: Testing canEditRole method...', 'blue');

        // Test original admin
        if ($originalAdmin) {
            $canEdit = $userModel->canEditRole($originalAdmin['id']);
            CLI::write("Original admin (ID: {$originalAdmin['id']}), Can edit role: " . ($canEdit ? 'Yes' : 'No'), 'white');
            if ($canEdit) {
                CLI::write("WARNING: Original admin role can be edited (should be false)!", 'red');
            } else {
                CLI::write("✓ Original admin role is correctly locked", 'green');
            }
        }

        // Test other admin users
        foreach ($otherAdminUsers as $adminUser) {
            $canEdit = $userModel->canEditRole($adminUser['id']);
            CLI::write("Admin user ID: {$adminUser['id']}, Can edit role: " . ($canEdit ? 'Yes' : 'No'), 'white');
            if (!$canEdit) {
                CLI::write("WARNING: Other admin user role should be editable!", 'red');
            } else {
                CLI::write("✓ Other admin user role is correctly editable", 'green');
            }
        }

        // Test 3: Check isAdmin method
        CLI::newLine();
        CLI::write('Test 3: Testing isAdmin method...', 'blue');

        // Test original admin
        if ($originalAdmin) {
            $isAdmin = $userModel->isAdmin($originalAdmin['id']);
            CLI::write("Original admin (ID: {$originalAdmin['id']}), Is admin: " . ($isAdmin ? 'Yes' : 'No'), 'white');
            if (!$isAdmin) {
                CLI::write("WARNING: Original user should be identified as admin!", 'red');
            } else {
                CLI::write("✓ Original user correctly identified as admin", 'green');
            }
        }

        // Test other admin users
        foreach ($otherAdminUsers as $adminUser) {
            $isAdmin = $userModel->isAdmin($adminUser['id']);
            CLI::write("Admin user ID: {$adminUser['id']}, Is admin: " . ($isAdmin ? 'Yes' : 'No'), 'white');
            if (!$isAdmin) {
                CLI::write("WARNING: User should be identified as admin!", 'red');
            } else {
                CLI::write("✓ User correctly identified as admin", 'green');
            }
        }

        // Test 4: Test role update prevention
        CLI::newLine();
        CLI::write('Test 4: Testing role update prevention...', 'blue');

        // Test original admin
        if ($originalAdmin) {
            $canEditRole = $userModel->canEditRole($originalAdmin['id']);
            CLI::write("Attempting to edit role for original admin user ID: {$originalAdmin['id']}", 'white');
            if ($canEditRole) {
                CLI::write("ERROR: Role editing should be prevented for original admin user!", 'red');
            } else {
                CLI::write("✓ Role editing correctly prevented for original admin user", 'green');
            }
        }

        // Test other admin users
        foreach ($otherAdminUsers as $adminUser) {
            $canEditRole = $userModel->canEditRole($adminUser['id']);
            CLI::write("Attempting to edit role for admin user ID: {$adminUser['id']}", 'white');
            if (!$canEditRole) {
                CLI::write("ERROR: Role editing should be allowed for other admin users!", 'red');
            } else {
                CLI::write("✓ Role editing correctly allowed for other admin users", 'green');
            }
        }

        CLI::newLine();
        CLI::write('All tests completed!', 'green');
    }
}
