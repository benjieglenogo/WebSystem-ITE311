<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // First, get a teacher ID (assuming at least one teacher exists)
        $teacher = $this->db->table('users')->where('role', 'teacher')->first();
        $teacher_id = $teacher ? $teacher['id'] : 2; // Default to ID 2 if no teacher found (assuming admin creates teacher with ID 2)

        $data = [
            [
                'course_code' => 'ITE311',
                'course_name' => 'Web Systems and Technologies',
                'description' => 'Comprehensive course on web development including HTML, CSS, JavaScript, PHP, and database integration. Students will learn to build dynamic web applications and understand modern web technologies.',
                'school_year' => '2024-2025',
                'semester' => '1st Semester',
                'schedule' => 'Monday-Wednesday',
                'teacher_id' => $teacher_id,
                'status' => 'active',
                'start_date' => '2024-09-01',
                'end_date' => '2024-10-31'
            ],
            [
                'course_code' => 'ITE312',
                'course_name' => 'Systems Integration and Architecture',
                'description' => 'Focuses on integrating different systems and understanding software architecture patterns. Covers API design, microservices, and enterprise application integration.',
                'school_year' => '2024-2025',
                'semester' => '1st Semester',
                'schedule' => 'Tuesday-Thursday',
                'teacher_id' => $teacher_id,
                'status' => 'active',
                'start_date' => '2024-09-01',
                'end_date' => '2024-10-31'
            ],
            [
                'course_code' => 'ITE313',
                'course_name' => 'Mobile Application Development',
                'description' => 'Learn to develop mobile applications for iOS and Android platforms. Covers native development, cross-platform frameworks, and mobile UI/UX design principles.',
                'school_year' => '2024-2025',
                'semester' => '2nd Semester',
                'schedule' => 'Friday-Saturday',
                'teacher_id' => $teacher_id,
                'status' => 'active',
                'start_date' => '2024-11-01',
                'end_date' => '2024-12-31'
            ],
            [
                'course_code' => 'ITE314',
                'course_name' => 'Database Management Systems',
                'description' => 'In-depth study of database design, implementation, and management. Covers SQL, NoSQL databases, data modeling, and database administration.',
                'school_year' => '2024-2025',
                'semester' => '2nd Semester',
                'schedule' => 'Monday-Wednesday',
                'teacher_id' => $teacher_id,
                'status' => 'active',
                'start_date' => '2024-11-01',
                'end_date' => '2024-12-31'
            ],
            [
                'course_code' => 'ITE315',
                'course_name' => 'Software Engineering',
                'description' => 'Comprehensive overview of software development methodologies, project management, quality assurance, and software maintenance practices.',
                'school_year' => '2025-2026',
                'semester' => '1st Semester',
                'schedule' => 'Tuesday-Thursday',
                'teacher_id' => $teacher_id,
                'status' => 'active',
                'start_date' => '2025-01-01',
                'end_date' => '2025-02-28'
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
