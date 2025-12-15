<?php

namespace App\Controllers;

class Gradebook extends BaseController
{
    /**
     * Display gradebook overview for the logged-in user
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
            'courses' => []
        ];

        if ($userRole === 'teacher') {
            // Teachers see all courses they teach
            $courseModel = new \App\Models\CourseModel();
            $courses = $courseModel->where('teacher_id', $userId)->findAll();
            
            $courseData = [];
            foreach ($courses ?? [] as $course) {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $enrollments = $enrollmentModel->where('course_id', $course['id'])->findAll();
                
                $courseData[] = [
                    'id' => $course['id'],
                    'code' => $course['course_code'],
                    'name' => $course['course_name'],
                    'description' => $course['description'],
                    'school_year' => $course['school_year'],
                    'semester' => $course['semester'],
                    'student_count' => count($enrollments ?? []),
                    'graded_count' => count(array_filter($enrollments ?? [], fn($e) => !empty($e['grade']))),
                    'status' => $course['status']
                ];
            }
            $data['courses'] = $courseData;
            
        } elseif ($userRole === 'admin') {
            // Admins see all courses
            $courseModel = new \App\Models\CourseModel();
            $courses = $courseModel->findAll();
            
            $courseData = [];
            foreach ($courses ?? [] as $course) {
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $enrollments = $enrollmentModel->where('course_id', $course['id'])->findAll();
                
                $courseData[] = [
                    'id' => $course['id'],
                    'code' => $course['course_code'],
                    'name' => $course['course_name'],
                    'teacher' => $course['teacher_name'] ?? 'Unassigned',
                    'school_year' => $course['school_year'],
                    'semester' => $course['semester'],
                    'student_count' => count($enrollments ?? []),
                    'graded_count' => count(array_filter($enrollments ?? [], fn($e) => !empty($e['grade']))),
                    'status' => $course['status']
                ];
            }
            $data['courses'] = $courseData;
            
        } else {
            // Students redirect to grades page
            return redirect()->to(base_url('grades'));
        }

        return view('gradebook/index', $data);
    }

    /**
     * View and manage grades for a specific course
     */
    public function viewCourse($courseId = null)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Only teachers and admins can access gradebook
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->to(base_url('/'))->with('error', 'Unauthorized access');
        }

        if (!$courseId) {
            return redirect()->to(base_url('gradebook'))->with('error', 'Invalid course ID');
        }

        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->to(base_url('gradebook'))->with('error', 'Course not found');
        }

        // Verify teacher ownership (if not admin)
        if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
            return redirect()->to(base_url('gradebook'))->with('error', 'Unauthorized access');
        }

        // Get all enrollments for this course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->where('course_id', $courseId)->findAll();

        $userModel = new \App\Models\UserModel();
        $grades = [];
        
        foreach ($enrollments ?? [] as $enrollment) {
            $student = $userModel->find($enrollment['student_id']);
            $grades[] = [
                'enrollment_id' => $enrollment['id'],
                'student_id' => $enrollment['student_id'],
                'student_name' => $student['name'] ?? 'Unknown Student',
                'student_email' => $student['email'] ?? 'N/A',
                'current_grade' => $enrollment['grade'] ?? '',
                'status' => $enrollment['status'] ?? 'active',
                'enrolled_date' => $enrollment['created_at'] ?? 'N/A'
            ];
        }

        return view('gradebook/course', [
            'course' => $course,
            'grades' => $grades,
            'userRole' => $userRole,
            'totalStudents' => count($grades),
            'gradedStudents' => count(array_filter($grades, fn($g) => !empty($g['current_grade'])))
        ]);
    }

    /**
     * Update grades for multiple students in a course
     */
    public function updateGrades($courseId = null)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Only teachers and admins can update grades
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$courseId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid course ID']);
        }

        if ($this->request->getMethod() === 'POST') {
            // Get grade data from request
            $gradesData = $this->request->getPost('grades');
            
            if (!is_array($gradesData)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid grade data format'
                ]);
            }

            $enrollmentModel = new \App\Models\EnrollmentModel();
            $updatedCount = 0;
            $errors = [];

            foreach ($gradesData as $enrollmentId => $grade) {
                // Validate grade
                if (!is_numeric($grade) || $grade < 0 || $grade > 100) {
                    $errors[] = "Invalid grade for enrollment ID $enrollmentId";
                    continue;
                }

                // Update enrollment with grade
                $result = $enrollmentModel->update($enrollmentId, ['grade' => $grade]);
                if ($result) {
                    $updatedCount++;
                }
            }

            if ($updatedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "$updatedCount grades updated successfully",
                    'updated_count' => $updatedCount,
                    'errors' => $errors
                ]);
            } else {
                return $this->response->setStatusCode(400)
                    ->setJSON([
                        'success' => false,
                        'message' => 'No grades were updated',
                        'errors' => $errors
                    ]);
            }
        }

        return redirect()->to(base_url('gradebook/course/' . $courseId));
    }
}
