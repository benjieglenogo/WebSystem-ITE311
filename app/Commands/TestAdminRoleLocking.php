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

        // Test 1: Check if admin users are protected
        CLI::write('Test 1: Checking if admin users are protected...', 'blue');
        $adminUsers = $userModel->where('role', 'admin')->findAll();

        if (empty($adminUsers)) {
            CLI::write('No admin users found in the database.', 'yellow');
        } else {
            foreach ($adminUsers as $adminUser) {
                CLI::write("Admin user ID: {$adminUser['id']}, Name: {$adminUser['name']}, Protected: {$adminUser['is_protected']}", 'white');
                if ($adminUser['is_protected'] != 1) {
                    CLI::write("WARNING: Admin user is not protected!", 'red');
                } else {
                    CLI::write("✓ Admin user is properly protected", 'green');
                }
            }
        }

        // Test 2: Check canEditRole method
        CLI::newLine();
        CLI::write('Test 2: Testing canEditRole method...', 'blue');
        foreach ($adminUsers as $adminUser) {
            $canEdit = $userModel->canEditRole($adminUser['id']);
            CLI::write("User ID: {$adminUser['id']}, Can edit role: " . ($canEdit ? 'Yes' : 'No'), 'white');
            if ($canEdit) {
                CLI::write("WARNING: Admin role can be edited (should be false)!", 'red');
            } else {
                CLI::write("✓ Admin role is correctly locked", 'green');
            }
        }

        // Test 3: Check isAdmin method
        CLI::newLine();
        CLI::write('Test 3: Testing isAdmin method...', 'blue');
        foreach ($adminUsers as $adminUser) {
            $isAdmin = $userModel->isAdmin($adminUser['id']);
            CLI::write("User ID: {$adminUser['id']}, Is admin: " . ($isAdmin ? 'Yes' : 'No'), 'white');
            if (!$isAdmin) {
                CLI::write("WARNING: User should be identified as admin!", 'red');
            } else {
                CLI::write("✓ User correctly identified as admin", 'green');
            }
        }

        // Test 4: Test role update prevention
        CLI::newLine();
        CLI::write('Test 4: Testing role update prevention...', 'blue');
        if (!empty($adminUsers)) {
            $testAdmin = $adminUsers[0];
            $canEditRole = $userModel->canEditRole($testAdmin['id']);
            CLI::write("Attempting to edit role for admin user ID: {$testAdmin['id']}", 'white');
            if ($canEditRole) {
                CLI::write("ERROR: Role editing should be prevented for admin users!", 'red');
            } else {
                CLI::write("✓ Role editing correctly prevented for admin users", 'green');
            }
        }

        CLI::newLine();
        CLI::write('All tests completed!', 'green');
    }
}
