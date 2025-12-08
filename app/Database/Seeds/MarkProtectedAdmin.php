<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MarkProtectedAdmin extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Mark the first admin user as protected
        $db->table('users')
            ->where('role', 'admin')
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->update(['is_protected' => 1]);
    }
}

