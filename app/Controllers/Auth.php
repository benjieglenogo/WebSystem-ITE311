<?php

namespace App\Controllers;

class Auth extends BaseController
{
    
     // Handles registration 
    
    public function register()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // Process form submission (POST)
        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');
            $passwordConfirm = (string) $this->request->getPost('password_confirm');

            if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
            }

            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
            }

            $userModel = new \App\Models\UserModel();

            if ($userModel->where('email', $email)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Default new users to 'student' to match migration ENUM
            $userId = $userModel->insert([
                'name' => $name,
                'email' => $email,
                'role' => 'student',
                'password' => $passwordHash,
            ], true);

            if (! $userId) {
                return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
            }

            return redirect()
                ->to(base_url('login'))
                ->with('register_success', 'Account created successfully. Please log in.');
        }

        // Display form (GET)
        return view('register');
    }

    // Login
    public function login()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // Process form submission (POST)
        if ($this->request->getMethod() === 'POST') {
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');

            try {
                $userModel = new \App\Models\UserModel();
                $user = $userModel->where('email', $email)->first();

                if ($user && password_verify($password, $user['password'])) {
                    // Check if user is active
                    if (isset($user['status']) && $user['status'] === 'inactive') {
                        return redirect()->back()->with('login_error', 'Your account has been deactivated. Please contact an administrator.');
                    }

                    $session->set([
                        'isLoggedIn' => true,
                        'userId' => $user['id'] ?? null,
                        'userName' => $user['name'] ?? null,
                        'userEmail' => $user['email'] ?? $email,
                        'userRole' => $user['role'] ?? 'student',
                    ]);

                    // Role-based redirection - all roles go to unified dashboard
                    $role = $user['role'] ?? 'student';
                    return redirect()->to(base_url('dashboard'));
                }

                return redirect()->back()->with('login_error', 'Invalid credentials');
            } catch (\Exception $e) {
                log_message('error', 'Login error: ' . $e->getMessage());
                return redirect()->back()->with('login_error', 'A system error occurred. Please try again later.');
            }
        }

        return view('login');
    }

 //Destroy user session
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        $userName = (string) $session->get('userName');
        $userEmail = (string) $session->get('userEmail');

        // Fetch role-specific data
        $data = [
            'role' => $role,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'email' => $userEmail, // For backward compatibility with dashboard view
            'widgets' => [],
        ];

        try {
            $materialModel = new \App\Models\MaterialModel();

            if ($role === 'admin') {
                $userModel = new \App\Models\UserModel();
                $courseModel = new \App\Models\CourseModel();
                $enrollmentModel = new \App\Models\EnrollmentModel();

                // Admin-specific statistics
                $data['widgets']['users'] = $userModel->countAllResults();
                $data['widgets']['courses'] = $courseModel->countAllResults();
                $data['widgets']['enrollments'] = $enrollmentModel->countAllResults();

                // Get user counts by role
                $data['widgets']['students'] = $userModel->where('role', 'student')->countAllResults();
                $data['widgets']['teachers'] = $userModel->where('role', 'teacher')->countAllResults();
                $data['widgets']['admins'] = $userModel->where('role', 'admin')->countAllResults();

                // Get all users for management (excluding inactive users by default, or show all for admin overview)
                $data['allUsers'] = $userModel->where('status !=', 'inactive')->findAll();

                // Get all courses with teacher names for course management
                if ($courseModel->db->fieldExists('teacher_id', 'courses')) {
                    $data['courses'] = $courseModel->select('courses.*, users.name as teacher_name')
                        ->join('users', 'users.id = courses.teacher_id', 'left')
                        ->findAll();
                } else {
                    $data['courses'] = $courseModel->findAll();
                    // Add empty teacher_name
                    foreach ($data['courses'] as &$course) {
                        $course['teacher_name'] = 'Unassigned';
                    }
                }

                // Get all materials for admin
                $data['allMaterials'] = $materialModel->select('materials.*, courses.course_name, courses.course_code')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->findAll();

                // Get teachers for course assignment dropdown
                $data['teachers'] = $userModel->where('role', 'teacher')->findAll();

                // Count active courses
                $data['widgets']['active_courses'] = $courseModel->where('status', 'active')->countAllResults();
            } elseif ($role === 'teacher') {
                $courseModel = new \App\Models\CourseModel();
                $userId = $session->get('userId');

                // Get teacher's courses - only show courses assigned to this teacher
                // Check if teacher_id column exists
                if ($courseModel->db->fieldExists('teacher_id', 'courses')) {
                    $data['teacherCourses'] = $courseModel->where('teacher_id', $userId)->findAll();
                } else {
                    // Temporary: no teacher courses until migration
                    $data['teacherCourses'] = [];
                }

                // Get materials for teacher's courses
                $courseIds = array_column($data['teacherCourses'], 'id');
                if (!empty($courseIds)) {
                    $data['materials'] = $materialModel->select('materials.*, courses.course_name, courses.course_code')
                        ->join('courses', 'courses.id = materials.course_id')
                        ->whereIn('materials.course_id', $courseIds)
                        ->findAll();
                } else {
                    $data['materials'] = [];
                }

                $data['widgets']['classes'] = count($data['teacherCourses']);
                $data['widgets']['toGrade'] = 12; // Placeholder
                $data['widgets']['announcements'] = 2; // Placeholder
            } else { // student
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $courseModel = new \App\Models\CourseModel();
                $userId = $session->get('userId');

                // Initialize variables
                $enrolledCourses = [];
                $availableCourses = [];
                $data['materials'] = [];

                if ($userId) {
                    try {
                        // Get enrolled courses
                        $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);
                        if (!is_array($enrolledCourses)) {
                            $enrolledCourses = [];
                        }

                        // Get available courses for enrollment (with better error handling)
                        try {
                            $availableCourses = $courseModel->getAvailableCourses($userId);
                            if (!is_array($availableCourses)) {
                                $availableCourses = [];
                            }
                        } catch (\Exception $e) {
                            $availableCourses = [];
                        }

                        // Get materials for enrolled courses
                        $courseIds = array_column($enrolledCourses, 'course_id');
                        if (!empty($courseIds) && is_array($courseIds)) {
                            try {
                                $data['materials'] = $materialModel->select('materials.*, courses.course_name, courses.course_code')
                                    ->join('courses', 'courses.id = materials.course_id')
                                    ->whereIn('materials.course_id', $courseIds)
                                    ->orderBy('materials.created_at', 'DESC')
                                    ->findAll();
                                if (!is_array($data['materials'])) {
                                    $data['materials'] = [];
                                }
                            } catch (\Exception $e) {
                                $data['materials'] = [];
                            }
                        }
                    } catch (\Exception $e) {
                        $enrolledCourses = [];
                        $availableCourses = [];
                        $data['materials'] = [];
                    }
                }

                // Set courses in data
                $data['enrolledCourses'] = $enrolledCourses;
                $data['availableCourses'] = $availableCourses;

                $data['widgets']['courses'] = count($enrolledCourses);
                $data['widgets']['assignments'] = 3; // Placeholder
                $data['widgets']['announcements'] = 1; // Placeholder
                $data['widgets']['available'] = count($availableCourses); // Available courses count
            }
        } catch (\Exception $e) {
            log_message('error', 'Dashboard error: ' . $e->getMessage());
            // Set default widgets to avoid errors
            $data['widgets'] = array_merge($data['widgets'], [
                'courses' => 0,
                'users' => 0,
                'classes' => 0,
                'enrollments' => 0,
                'students' => 0,
                'teachers' => 0,
                'admins' => 0,
                'assignments' => 0,
                'announcements' => 0,
                'active_courses' => 0,
                'toGrade' => 0,
            ]);
            $data['enrolledCourses'] = [];
            $data['materials'] = [];
            $data['allUsers'] = [];
            $data['courses'] = [];
            $data['allMaterials'] = [];
            $data['teachers'] = [];
            $data['teacherCourses'] = [];
        }

        return view('auth/dashboard', $data);
    }

    /**
     * Student Courses Dashboard - Show enrolled courses
     */
    public function studentCourses()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'student') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Student privileges required.');
        }

        $studentId = $session->get('userId');

        $enrollModel = new \App\Models\EnrollmentModel();
        $coursesModel = new \App\Models\CourseModel();

        try {
            // Get enrolled courses with details
            $enrolledCourses = $enrollModel->getUserEnrollments($studentId);

            // Get available courses for enrollment
            $availableCourses = $coursesModel->getAvailableCourses($studentId);

            return view('students/dashboard', [
                'enrolledCourses' => $enrolledCourses ?? [],
                'availableCourses' => $availableCourses ?? []
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error in studentCourses: ' . $e->getMessage());
            return view('students/dashboard', [
                'enrolledCourses' => [],
                'availableCourses' => [],
                'error' => 'An error occurred while loading your courses. Please try again later.'
            ]);
        }
    }

    /**
     * User Management Dashboard (Admin only)
     */
    public function userManagement()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Admin privileges required.');
        }

        $userModel = new \App\Models\UserModel();

        // Get user statistics
        $data = [
            'widgets' => [
                'users' => $userModel->countAllResults(),
                'active_users' => $userModel->where('status', 'active')->countAllResults(),
                'inactive_users' => $userModel->where('status', 'inactive')->countAllResults(),
            ],
            'allUsers' => $userModel->findAll(),
        ];

        return view('users/management', $data);
    }

    /**
     * Manage Students Dashboard (Teacher only)
     */
    public function manageStudents()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Teacher privileges required.');
        }

        $userId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();

        // Get teacher's assigned courses
        if ($courseModel->db->fieldExists('teacher_id', 'courses')) {
            $teacherCourses = $courseModel->where('teacher_id', $userId)->findAll();
        } else {
            $teacherCourses = [];
        }

        $data = [
            'teacherCourses' => $teacherCourses,
            'selectedCourseId' => $teacherCourses[0]['id'] ?? null,
            'selectedCourseName' => $teacherCourses[0]['course_name'] ?? 'No course selected',
            'selectedCourseCode' => $teacherCourses[0]['course_code'] ?? '',
        ];

        return view('students/manage', $data);
    }

    /**
     * Get students for a specific course (AJAX)
     */
    public function getStudentsForCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Teacher privileges required.'
            ]);
        }

        $courseId = $this->request->getGet('course_id');
        if (!$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userModel = new \App\Models\UserModel();

        // Get students enrolled in this course
        $enrollments = $enrollmentModel->where('course_id', $courseId)->findAll();
        $studentIds = array_column($enrollments, 'user_id');

        if (empty($studentIds)) {
            return $this->response->setJSON([
                'success' => true,
                'students' => []
            ]);
        }

        // Get student details
        $students = $userModel->whereIn('id', $studentIds)
                              ->where('role', 'student')
                              ->findAll();

        // Add enrollment date to each student (if column exists)
        $enrollmentMap = [];
        foreach ($enrollments as $enrollment) {
            $enrollmentMap[$enrollment['user_id']] = $enrollment['enrollment_date'] ?? 'N/A';
        }

        foreach ($students as &$student) {
            $student['enrollment_date'] = $enrollmentMap[$student['id']] ?? 'N/A';
            $student['status'] = $student['status'] ?? 'active';
        }

        return $this->response->setJSON([
            'success' => true,
            'students' => $students
        ]);
    }

    /**
     * Update student status (AJAX)
     */
    public function updateStudentStatus()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Teacher privileges required.'
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $newStatus = $this->request->getPost('new_status');
        $remarks = $this->request->getPost('remarks');

        if (!$userId || !$newStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and new status are required.'
            ]);
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ]);
        }

        // Update user status
        $updateData = ['status' => $newStatus];
        if ($remarks) {
            $updateData['status_remarks'] = $remarks;
        }

        if ($userModel->update($userId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student status updated successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update student status.'
            ]);
        }
    }

    /**
     * Remove student from course (AJAX)
     */
    public function removeStudentFromCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied. Teacher privileges required.'
            ]);
        }

        $userId = $this->request->getPost('user_id');
        $courseId = $this->request->getPost('course_id');

        if (!$userId || !$courseId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and course ID are required.'
            ]);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('user_id', $userId)
                                      ->where('course_id', $courseId)
                                      ->first();

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Enrollment record not found.'
            ]);
        }

        if ($enrollmentModel->delete($enrollment['id'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student removed from course successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to remove student from course.'
            ]);
        }
    }

    /**
     * Course Management Dashboard (Teacher only)
     */
    public function courseManagement()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied. Teacher privileges required.');
        }

        $userId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();

        // Get teacher's assigned courses
        if ($courseModel->db->fieldExists('teacher_id', 'courses')) {
            $teacherCourses = $courseModel->where('teacher_id', $userId)->where('status', 'active')->findAll();
        } else {
            $teacherCourses = [];
        }

        $data = [
            'teacherCourses' => $teacherCourses,
        ];

        return view('teachers/course_management', $data);
    }

    /**
     * Get students for a course (AJAX for teachers)
     */
    public function getStudents()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseId = $this->request->getGet('course_id');
        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID required']);
        }

        // Verify teacher owns this course
        $userId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $userId)->first();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $students = $enrollmentModel->select('users.id, users.name, users.email, enrollments.enrollment_date, enrollments.user_id')
            ->join('users', 'users.id = enrollments.user_id')
            ->where('enrollments.course_id', $courseId)
            ->findAll();

        return $this->response->setJSON(['success' => true, 'students' => $students]);
    }

    /**
     * Update student status in course
     */
    public function updateStatus()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userId = $this->request->getPost('user_id');
        $newStatus = $this->request->getPost('new_status');
        $courseId = $this->request->getPost('course_id');

        if (!$userId || !$newStatus || !$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Required fields missing']);
        }

        // Verify teacher owns this course
        $teacherId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->update($userId, ['status' => $newStatus])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Student status updated']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Remove student from course
     */
    public function remove()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $userId = $this->request->getPost('user_id');
        $courseId = $this->request->getPost('course_id');

        if (!$userId || !$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Required fields missing']);
        }

        // Verify teacher owns this course
        $teacherId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollment = $enrollmentModel->where('user_id', $userId)->where('course_id', $courseId)->first();
        if (!$enrollment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Student not enrolled']);
        }

        if ($enrollmentModel->delete($enrollment['id'])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Student removed from course']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove student']);
        }
    }

    /**
     * Lock a user account (Admin only)
     */
    public function lockAccount()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required.']);
        }

        $userModel = new \App\Models\UserModel();

        // Check if the user is an admin and protected
        $user = $userModel->find($userId);
        if ($user && $user['role'] === 'admin' && isset($user['is_protected']) && $user['is_protected'] == 1) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot lock protected admin accounts.']);
        }

        if ($userModel->lockAccount($userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account locked successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to lock account.']);
        }
    }

    /**
     * Unlock a user account (Admin only)
     */
    public function unlockAccount()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required.']);
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->unlockAccount($userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account unlocked successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to unlock account.']);
        }
    }

    /**
     * Protect an admin account from being locked (Admin only)
     */
    public function protectAccount()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required.']);
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->protectAccount($userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account protected successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to protect account.']);
        }
    }

    /**
     * Unprotect an account (Admin only)
     */
    public function unprotectAccount()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied. Admin privileges required.']);
        }

        $userId = $this->request->getPost('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'User ID is required.']);
        }

        $userModel = new \App\Models\UserModel();
        if ($userModel->unprotectAccount($userId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Account unprotected successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to unprotect account.']);
        }
    }

    /**
     * Create a new course (Teacher only)
     */
    public function teacherCreateCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseModel = new \App\Models\CourseModel();

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'teacher_id' => $session->get('userId'),
            'status' => 'active',
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date')
        ];

        // Validate required fields
        $requiredFields = ['course_code', 'course_name', 'description', 'school_year', 'semester', 'schedule'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            }
        }

        if ($courseModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course created successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to create course']);
        }
    }

    /**
     * Update teacher's course
     */
    public function teacherUpdateCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID required']);
        }

        // Verify teacher owns this course
        $teacherId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'description' => $this->request->getPost('description'),
            'school_year' => $this->request->getPost('school_year'),
            'semester' => $this->request->getPost('semester'),
            'schedule' => $this->request->getPost('schedule'),
            'status' => $this->request->getPost('status') ?: 'active',
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date')
        ];

        // Validate required fields
        $requiredFields = ['course_code', 'course_name', 'description', 'school_year', 'semester', 'schedule'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return $this->response->setJSON(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            }
        }

        if ($courseModel->update($courseId, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course updated successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update course']);
        }
    }

    /**
     * Delete teacher's course
     */
    public function teacherDeleteCourse()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $courseId = $this->request->getPost('course_id');
        if (!$courseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course ID required']);
        }

        // Verify teacher owns this course
        $teacherId = $session->get('userId');
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->where('id', $courseId)->where('teacher_id', $teacherId)->first();
        if (!$course) {
            return $this->response->setJSON(['success' => false, 'message' => 'Course not found or access denied']);
        }

        // Check if there are enrolled students
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrolledCount = $enrollmentModel->where('course_id', $courseId)->countAllResults();
        if ($enrolledCount > 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete course with enrolled students']);
        }

        if ($courseModel->delete($courseId)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Course deleted successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete course']);
        }
    }
}
