<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
                'status'   => 'active',
                'is_protected' => 1
            ],
            [
                'name'     => 'Teacher',
                'email'    => 'teacher@example.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
                'status'   => 'active',
                'is_protected' => 0
            ],
            [
                'name'     => 'Student',
                'email'    => 'student@example.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
                'status'   => 'active',
                'is_protected' => 0
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
