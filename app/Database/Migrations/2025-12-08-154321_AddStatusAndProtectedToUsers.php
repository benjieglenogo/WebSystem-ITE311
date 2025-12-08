<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndProtectedToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'ENUM("active","inactive")',
                'default' => 'active',
                'after' => 'role',
            ],
            'is_protected' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'status',
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['status', 'is_protected']);
    }
}
