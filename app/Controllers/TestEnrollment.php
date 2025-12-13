<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\UserModel;

class TestEnrollment extends BaseController
{
    public function test()
    {
        // This is a test method to verify the enrollment functionality
        // In a real application, you would remove this or protect it properly

        $session = session();

        // Simulate a logged-in student for testing
        if (!$session->get('isLoggedIn')) {
            // Create a test user if none exists
            $userModel = new UserModel();
            $testUser = $userModel->where('email', 'test@student.com')->first();

            if (!$testUser) {
                $userId = $userModel->insert([
                    'name' => 'Test Student',
                    'email' => 'test@student.com',
                    'role' => 'student',
                    'password' => password_hash('password', PASSWORD_DEFAULT),
                ]);
            } else {
                $userId = $testUser['id'];
            }

            // Set session
            $session->set([
                'isLoggedIn' => true,
                'userId' => $userId,
                'userName' => 'Test Student',
                'userEmail' => 'test@student.com',
                'userRole' => 'student',
            ]);
        }

        // Create test courses if they don't exist
        $courseModel = new CourseModel();
        $existingCourses = $courseModel->countAllResults();

        if ($existingCourses < 3) {
            // Create sample courses
            $sampleCourses = [
                [
                    'course_code' => 'CS101',
                    'course_name' => 'Introduction to Computer Science',
                    'description' => 'Fundamental concepts of computer science and programming.',
                    'teacher_id' => 1, // Assuming admin user has ID 1
                    'school_year' => '2024-2025',
                    'semester' => '1st Semester',
                    'schedule' => 'Monday-Wednesday',
                    'status' => 'active',
                    'start_date' => '2024-08-01',
                    'end_date' => '2024-12-15'
                ],
                [
                    'course_code' => 'MATH201',
                    'course_name' => 'Calculus I',
                    'description' => 'Differential and integral calculus with applications.',
                    'teacher_id' => 1,
                    'school_year' => '2024-2025',
                    'semester' => '1st Semester',
                    'schedule' => 'Tuesday-Thursday',
                    'status' => 'active',
                    'start_date' => '2024-08-01',
                    'end_date' => '2024-12-15'
                ],
                [
                    'course_code' => 'ENG102',
                    'course_name' => 'English Composition',
                    'description' => 'Advanced writing and composition skills.',
                    'teacher_id' => 1,
                    'school_year' => '2024-2025',
                    'semester' => '1st Semester',
                    'schedule' => 'Friday',
                    'status' => 'active',
                    'start_date' => '2024-08-01',
                    'end_date' => '2024-12-15'
                ]
            ];

            foreach ($sampleCourses as $course) {
                $courseModel->insert($course);
            }
        }

        return redirect()->to(base_url('student/courses'));
    }

    public function reset()
    {
        // Reset test data - be careful with this in production!
        $session = session();

        // Clear enrollments for test user
        $userId = $session->get('userId');
        if ($userId) {
            $enrollmentModel = new EnrollmentModel();
            $enrollmentModel->where('user_id', $userId)->delete();
        }

        $session->destroy();
        return redirect()->to(base_url('test/enrollment'));
    }
}
