<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingFieldsToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'teacher_id',
            ],
            'semester' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
                'after'      => 'school_year',
            ],
            'schedule' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'semester',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
                'after'      => 'schedule',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'status',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date',
            ],
        ]);

        // Set existing courses to active status
        $this->db->query('UPDATE courses SET status = "active" WHERE status IS NULL');
    }

    public function down()
    {
        $dropColumns = [
            'school_year',
            'semester',
            'schedule',
            'status',
            'start_date',
            'end_date'
        ];

        foreach ($dropColumns as $column) {
            $this->forge->dropColumn('courses', $column);
        }
    }
}
