<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProtectAdminUsers extends BaseCommand
{
    protected $group       = 'User Management';
    protected $name        = 'users:protect-admins';
    protected $description = 'Protects the original admin user (ID 1) by setting is_protected = 1 to prevent role editing';

    public function run(array $params)
    {
        $userModel = new \App\Models\UserModel();

        CLI::write('Starting admin user protection...', 'yellow');

        // Protect only specific admin users (original admin with ID 1)
        $adminUser = $userModel->find(1); // Assuming the original admin has ID 1

        if ($adminUser && $adminUser['role'] === 'admin') {
            $result = $userModel->protectAccount($adminUser['id']);

            if ($result) {
                CLI::write('Original admin user (ID: 1) has been successfully protected!', 'green');
                CLI::write('This admin account is now locked and cannot be edited.', 'green');
            } else {
                CLI::write('Failed to protect original admin user.', 'red');
            }
        } else {
            CLI::write('No original admin user found with ID 1.', 'yellow');
        }
    }
}
