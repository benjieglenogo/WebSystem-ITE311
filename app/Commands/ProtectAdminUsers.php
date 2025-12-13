<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ProtectAdminUsers extends BaseCommand
{
    protected $group       = 'User Management';
    protected $name        = 'users:protect-admins';
    protected $description = 'Protects all admin users by setting is_protected = 1 to prevent role editing';

    public function run(array $params)
    {
        $userModel = new \App\Models\UserModel();

        CLI::write('Starting admin user protection...', 'yellow');

        // Protect all admin users
        $result = $userModel->protectAdminUsers();

        if ($result) {
            CLI::write('All admin users have been successfully protected!', 'green');
            CLI::write('Admin roles are now locked and cannot be edited.', 'green');
        } else {
            CLI::write('Failed to protect admin users.', 'red');
        }
    }
}
