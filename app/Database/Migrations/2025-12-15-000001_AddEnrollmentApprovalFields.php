<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentApprovalFields extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
                'comment'    => 'pending, approved, rejected'
            ],
            'approved_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Admin or Teacher ID who approved'
            ],
            'approved_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'comment' => 'When the enrollment was approved'
            ],
            'rejection_reason' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Reason for rejection if rejected'
            ],
        ];

        $this->forge->addColumn('enrollments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', ['status', 'approved_by', 'approved_at', 'rejection_reason']);
    }
}
