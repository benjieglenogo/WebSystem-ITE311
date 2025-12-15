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

        $user_id = (int) session()->get('userId');
        $course_id = (int) $this->request->getPost('course_id');
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

        try {
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
        } catch (\Exception $e) {
            log_message('error', 'Enrollment error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing your enrollment. Please try again.'
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
        $searchTerm = $this->request->getVar('search_term');

        $builder = $courseModel->select('courses.*, users.name as teacher_name')
                      ->join('users', 'users.id = courses.teacher_id', 'left');

        // If a student is performing the search, exclude courses they're already enrolled in
        try {
            $session = session();
            $userRole = $session->get('userRole');
            $userId = (int) $session->get('userId');

            if ($userRole === 'student' && $userId) {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $enrolledCourseRows = $enrollmentModel->select('course_id')
                    ->where('user_id', $userId)
                    ->findAll();
                $enrolledIds = array_column($enrolledCourseRows, 'course_id');
                if (!empty($enrolledIds)) {
                    $builder->whereNotIn('courses.id', $enrolledIds);
                }
            }

            // If user is a teacher, limit search to their assigned courses (allow inactive courses too)
            if ($userRole === 'teacher' && $userId) {
                $builder->where('courses.teacher_id', $userId);
            } else {
                // Non-teacher users should only see active courses by default
                $builder->where('courses.status', 'active');
            }
        } catch (\Exception $e) {
            // On any error while checking enrollments, proceed without excluding
            log_message('warning', 'Search: failed to determine enrolled courses - ' . $e->getMessage());
        }

        if (!empty($searchTerm)) {
            $builder->groupStart()
                    ->like('courses.course_name', $searchTerm)
                    ->orLike('courses.description', $searchTerm)
                    ->orLike('courses.course_code', $searchTerm)
                    ->orLike('users.name', $searchTerm)
                    ->groupEnd();
        }

        $courses = $builder->findAll() ?? [];

        return $this->response->setJSON(array_values($courses));
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

    /**
     * Get course details by ID (AJAX) - works for both admin and teacher
     */
    public function get($courseId = null)
    {
        // Support both URL param and query string
        if (!$courseId) {
            $courseId = $this->request->getVar('course_id') ?? $this->request->getPost('course_id');
        }

        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ])->setStatusCode(400);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'course' => $course
        ]);
    }

    /**
     * Update course details - works for both admin and teacher for their own courses
     */
    public function updateCourse()
    {
        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ])->setStatusCode(400);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Check permission: admin can update any, teacher can update their own
        $userRole = session()->get('userRole');
        $userId = session()->get('userId');
        
        if ($userRole === 'teacher' && (int)$course['teacher_id'] !== (int)$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to update this course.'
            ])->setStatusCode(403);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'status' => $this->request->getPost('status') ?? 'active',
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date')
        ];

        // Only admin can change teacher assignment
        if ($userRole === 'admin' && $this->request->getPost('teacher_id')) {
            $data['teacher_id'] = $this->request->getPost('teacher_id');
        }

        // Validate required fields
        $requiredFields = ['course_code', 'course_name', 'description', 'school_year', 'semester', 'schedule'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
                ])->setStatusCode(400);
            }
        }

        if ($courseModel->update($courseId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course updated successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course. Please try again.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all teachers for course assignment
     */
    public function getTeachers()
    {
        $userModel = new \App\Models\UserModel();
        $teachers = $userModel->where('role', 'teacher')->findAll();

        return $this->response->setJSON([
            'success' => true,
            'teachers' => $teachers
        ]);
    }

    /**
     * Create a new course (Admin only)
     */
    public function create()
    {
        // Check if user is admin
        if (session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin privileges required.'
            ]);
        }

        $courseModel = new CourseModel();

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'status' => $this->request->getPost('status') ?? 'active',
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date')
        ];

        // Validate required fields
        $requiredFields = ['course_code', 'course_name', 'description', 'school_year', 'semester', 'schedule'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
                ]);
            }
        }

        if ($courseModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course created successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create course. Please try again.'
            ]);
        }
    }

    /**
     * Update course details (Admin only)
     */
    public function update()
    {
        // Check if user is admin
        if (session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin privileges required.'
            ]);
        }

        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_title'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'teacher_id' => $this->request->getPost('teacher_id'),
            'status' => $this->request->getPost('status') ?? 'active',
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date')
        ];

        // Validate required fields
        $requiredFields = ['course_code', 'course_name', 'description', 'school_year', 'semester', 'schedule'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required.'
                ]);
            }
        }

        if ($courseModel->update($courseId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course updated successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course. Please try again.'
            ]);
        }
    }

    /**
     * Update course status (Admin only)
     */
    public function updateStatus()
    {
        // Check if user is admin
        if (session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin privileges required.'
            ]);
        }

        $courseId = $this->request->getPost('course_id');
        $status = $this->request->getPost('status');

        if (!$courseId || !$status) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID and status are required.'
            ]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        if ($courseModel->update($courseId, ['status' => $status])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course status updated successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update course status. Please try again.'
            ]);
        }
    }

    /**
     * Get enrolled courses for current user (AJAX)
     */
    public function getEnrolledCourses()
    {
        $userId = session()->get('userId');
        $enrollmentModel = new EnrollmentModel();
        $courseModel = new CourseModel();
        $userModel = new \App\Models\UserModel();

        $enrolledCourses = $enrollmentModel->select('enrollments.*, courses.*, users.name as teacher_name')
                                          ->join('courses', 'enrollments.course_id = courses.id')
                                          ->join('users', 'courses.teacher_id = users.id', 'left')
                                          ->where('enrollments.user_id', $userId)
                                          ->orderBy('enrollments.enrollment_date', 'DESC')
                                          ->findAll();

        return $this->response->setJSON($enrolledCourses);
    }

    /**
     * Search enrolled courses for current student (AJAX)
     */
    public function searchEnrolled()
    {
        if (!session()->get('isLoggedIn') || session()->get('userRole') !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        $userId = session()->get('userId');
        $searchTerm = $this->request->getVar('search_term');

        $enrollmentModel = new EnrollmentModel();

        $builder = $enrollmentModel->select('enrollments.*, courses.id as course_id, courses.course_name, courses.course_code, courses.description, courses.teacher_id, users.name as teacher_name')
                                   ->join('courses', 'courses.id = enrollments.course_id')
                                   ->join('users', 'users.id = courses.teacher_id', 'left')
                                   ->where('enrollments.user_id', $userId);

        if (!empty($searchTerm)) {
            $builder->groupStart()
                    ->like('courses.course_name', $searchTerm)
                    ->orLike('courses.description', $searchTerm)
                    ->orLike('courses.course_code', $searchTerm)
                    ->orLike('users.name', $searchTerm)
                    ->groupEnd();
        }

        $courses = $builder->findAll() ?? [];

        return $this->response->setJSON(array_values($courses));
    }

    /**
     * Delete a course (Admin only)
     */
    public function delete()
    {
        // Check if user is admin
        if (session()->get('userRole') !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin privileges required.'
            ]);
        }

        $courseId = $this->request->getPost('course_id');

        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        if ($courseModel->delete($courseId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course deleted successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete course. Please try again.'
            ]);
        }
    }
}
