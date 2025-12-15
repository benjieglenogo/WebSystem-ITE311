<?php

namespace App\Controllers;

class Grades extends BaseController
{
    /**
     * Display grades for the logged-in user
     */
    public function index()
    {
        $session = session();
        
        // Redirect if not logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        $data = [
            'userRole' => $userRole,
            'userId' => $userId,
            'grades' => []
        ];

        if ($userRole === 'student') {
            // Students see their grades from enrolled courses
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $courseModel = new \App\Models\CourseModel();
            
            $enrollments = $enrollmentModel->where('student_id', $userId)->findAll();
            
            $grades = [];
            if (!empty($enrollments)) {
                foreach ($enrollments as $enrollment) {
                    $course = $courseModel->find($enrollment['course_id']);
                    if ($course) {
                        $grades[] = [
                            'course_id' => $course['id'],
                            'course_code' => $course['course_code'],
                            'course_name' => $course['course_name'],
                            'teacher_name' => $course['teacher_name'] ?? 'N/A',
                            'grade' => $enrollment['grade'] ?? 'TBD',
                            'percentage' => $enrollment['grade'] ? (int)$enrollment['grade'] : 0,
                            'status' => $enrollment['status'] ?? 'active'
                        ];
                    }
                }
            }
            $data['grades'] = $grades;
        } elseif ($userRole === 'teacher') {
            // Teachers see grades from their students in their courses
            $courseModel = new \App\Models\CourseModel();
            $enrollmentModel = new \App\Models\EnrollmentModel();
            
            $courses = $courseModel->where('teacher_id', $userId)->findAll();
            $courseIds = array_column($courses ?? [], 'id');
            
            $grades = [];
            if (!empty($courseIds)) {
                foreach ($courseIds as $courseId) {
                    $enrollments = $enrollmentModel->where('course_id', $courseId)->findAll();
                    if (!empty($enrollments)) {
                        foreach ($enrollments as $enrollment) {
                            $grades[] = [
                                'course_id' => $courseId,
                                'course_name' => $courseModel->find($courseId)['course_name'] ?? 'N/A',
                                'student_id' => $enrollment['student_id'],
                                'student_name' => 'Student ' . $enrollment['student_id'],
                                'grade' => $enrollment['grade'] ?? 'TBD',
                                'percentage' => $enrollment['grade'] ? (int)$enrollment['grade'] : 0,
                                'status' => $enrollment['status'] ?? 'active'
                            ];
                        }
                    }
                }
            }
            $data['grades'] = $grades;
        } elseif ($userRole === 'admin') {
            // Admins see all grades
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $enrollments = $enrollmentModel->findAll();
            $courseModel = new \App\Models\CourseModel();
            $userModel = new \App\Models\UserModel();
            
            $grades = [];
            foreach ($enrollments ?? [] as $enrollment) {
                $course = $courseModel->find($enrollment['course_id']);
                $student = $userModel->find($enrollment['student_id']);
                
                $grades[] = [
                    'course_id' => $enrollment['course_id'],
                    'course_name' => $course['course_name'] ?? 'N/A',
                    'student_id' => $enrollment['student_id'],
                    'student_name' => $student['name'] ?? 'Unknown',
                    'grade' => $enrollment['grade'] ?? 'TBD',
                    'percentage' => $enrollment['grade'] ? (int)$enrollment['grade'] : 0,
                    'status' => $enrollment['status'] ?? 'active'
                ];
            }
            $data['grades'] = $grades;
        }

        return view('grades/index', $data);
    }

    /**
     * View grades for a specific course (Teacher/Admin)
     */
    public function view($courseId = null)
    {
        $session = session();
        $userRole = $session->get('role');

        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->to(base_url('grades'))->with('error', 'Unauthorized');
        }

        if (!$courseId) {
            return redirect()->to(base_url('grades'))->with('error', 'Invalid course ID');
        }

        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->to(base_url('grades'))->with('error', 'Course not found');
        }

        // Verify teacher ownership
        if ($userRole === 'teacher' && $course['teacher_id'] != $session->get('user_id')) {
            return redirect()->to(base_url('grades'))->with('error', 'Unauthorized');
        }

        // Get enrollments for this course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->where('course_id', $courseId)->findAll();

        $userModel = new \App\Models\UserModel();
        $grades = [];
        foreach ($enrollments ?? [] as $enrollment) {
            $student = $userModel->find($enrollment['student_id']);
            $grades[] = [
                'enrollment_id' => $enrollment['id'],
                'student_id' => $enrollment['student_id'],
                'student_name' => $student['name'] ?? 'Unknown',
                'grade' => $enrollment['grade'] ?? '',
                'status' => $enrollment['status'] ?? 'active'
            ];
        }

        return view('grades/view', [
            'course' => $course,
            'grades' => $grades,
            'userRole' => $userRole
        ]);
    }

    /**
     * Update grades (Teacher/Admin only)
     */
    public function update($courseId = null)
    {
        $session = session();
        $userRole = $session->get('role');

        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$courseId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid course ID']);
        }

        if ($this->request->getMethod() === 'POST') {
            $enrollmentId = $this->request->getPost('enrollment_id');
            $grade = $this->request->getPost('grade');

            // Validate grade
            if (!is_numeric($grade) || $grade < 0 || $grade > 100) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Grade must be between 0 and 100'
                ]);
            }

            $enrollmentModel = new \App\Models\EnrollmentModel();
            $result = $enrollmentModel->update($enrollmentId, ['grade' => $grade]);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grade updated successfully'
                ]);
            } else {
                return $this->response->setStatusCode(500)
                    ->setJSON(['success' => false, 'message' => 'Failed to update grade']);
            }
        }

        return redirect()->to(base_url('grades'));
    }
}
