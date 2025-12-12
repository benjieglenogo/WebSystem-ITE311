<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherIdToCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'description',
            ],
        ]);

        // Add foreign key constraint (optional, but recommended)
        $this->db->query('ALTER TABLE courses ADD CONSTRAINT fk_course_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        // Drop foreign key first
        $this->db->query('ALTER TABLE courses DROP FOREIGN KEY fk_course_teacher');

        $this->forge->dropColumn('courses', 'teacher_id');
    }
}
