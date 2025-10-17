<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Welcome to the New Semester',
                'content' => 'Dear students and faculty, welcome to the new semester. We are excited to have you back and look forward to a productive year ahead.',
            ],
            [
                'title' => 'Important Registration Deadline',
                'content' => 'Please ensure all course registrations are completed by the end of this week. Late registrations may not be accepted.',
            ],
        ];

        $this->db->table('announcements')->insertBatch($data);
    }
}
