<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'course_code' => 'ITE311',
                'course_name' => 'Web Systems and Technologies',
                'description' => 'Comprehensive course on web development including HTML, CSS, JavaScript, PHP, and database integration. Students will learn to build dynamic web applications and understand modern web technologies.',
            ],
            [
                'course_code' => 'ITE312',
                'course_name' => 'Systems Integration and Architecture',
                'description' => 'Focuses on integrating different systems and understanding software architecture patterns. Covers API design, microservices, and enterprise application integration.',
            ],
            [
                'course_code' => 'ITE313',
                'course_name' => 'Mobile Application Development',
                'description' => 'Learn to develop mobile applications for iOS and Android platforms. Covers native development, cross-platform frameworks, and mobile UI/UX design principles.',
            ],
            [
                'course_code' => 'ITE314',
                'course_name' => 'Database Management Systems',
                'description' => 'In-depth study of database design, implementation, and management. Covers SQL, NoSQL databases, data modeling, and database administration.',
            ],
            [
                'course_code' => 'ITE315',
                'course_name' => 'Software Engineering',
                'description' => 'Comprehensive overview of software development methodologies, project management, quality assurance, and software maintenance practices.',
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
