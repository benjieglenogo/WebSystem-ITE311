<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run()
    {
        // Get course IDs
        $courses = $this->db->table('courses')->select('id, course_code')->get()->getResultArray();

        if (empty($courses)) {
            echo "No courses found. Please run the CourseSeeder first.\n";
            return;
        }

        $materials = [];

        foreach ($courses as $course) {
            // Add a few different types of materials for each course
            $materials[] = [
                'course_id' => $course['id'],
                'file_name' => $course['course_code'] . '_Syllabus.pdf',
                'file_path' => 'materials/' . $course['course_code'] . '_Syllabus.pdf',
                'file_type' => 'pdf',
                'file_size' => 245760, // 240KB
                'description' => 'Course syllabus and outline',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $materials[] = [
                'course_id' => $course['id'],
                'file_name' => $course['course_code'] . '_Lecture_Notes.docx',
                'file_path' => 'materials/' . $course['course_code'] . '_Lecture_Notes.docx',
                'file_type' => 'docx',
                'file_size' => 184320, // 180KB
                'description' => 'Weekly lecture notes and materials',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $materials[] = [
                'course_id' => $course['id'],
                'file_name' => $course['course_code'] . '_Assignment.zip',
                'file_path' => 'materials/' . $course['course_code'] . '_Assignment.zip',
                'file_type' => 'zip',
                'file_size' => 524288, // 512KB
                'description' => 'Assignment files and resources',
                'created_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->table('materials')->insertBatch($materials);

        echo count($materials) . " materials seeded successfully.\n";
    }
}
