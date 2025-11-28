<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\NotificationModel;

class Course extends BaseController
{
    /**
     * Handle AJAX enrollment request
     */
    public function enroll()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to enroll in courses.'
            ]);
        }

        // Get course_id from POST request
        $course_id = $this->request->getPost('course_id');

        if (!$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        $user_id = session()->get('userId');
        $enrollmentModel = new EnrollmentModel();

        // Check if user is already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Get course information for notification
        $courseModel = new CourseModel();
        $course = $courseModel->find($course_id);
        
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // Insert new enrollment record
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        if ($enrollmentModel->enrollUser($data)) {
            // Create notification for successful enrollment
            $notificationModel = new NotificationModel();
            $courseName = $course['course_name'] ?? 'the course';
            $message = "You have been enrolled in {$courseName}";
            
            $notificationModel->createNotification($user_id, $message);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll in the course. Please try again.'
            ]);
        }
    }

    /**
     * Server-side search endpoint for AJAX requests.
     * Accepts GET param `search_term` and returns JSON list of matching courses.
     */
    public function search()
    {
        $courseModel = new CourseModel();
        $searchTerm = $this->request->getGet('search_term');

        if (!empty($searchTerm)) {
            $courseModel->like('course_name', $searchTerm);
            $courseModel->orLike('description', $searchTerm);
        }

        $courses = $courseModel->findAll();

        return $this->response->setJSON($courses);
    }

    /**
     * Display the courses index page with search functionality.
     */
    public function index()
    {
        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();

        return view('courses/index', ['courses' => $courses]);
    }
}
