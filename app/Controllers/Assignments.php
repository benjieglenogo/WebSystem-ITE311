<?php

namespace App\Controllers;

class Assignments extends BaseController
{
    /**
     * Display all assignments for the logged-in user
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

        // Get assignments based on user role
        $assignmentModel = new \App\Models\AssignmentModel();
        $courseModel = new \App\Models\CourseModel();

        $data = [];

        if ($userRole === 'teacher') {
            // Teachers see assignments from their courses
            $teacherCourses = $courseModel->where('teacher_id', $userId)->findAll();
            $courseIds = array_column($teacherCourses ?? [], 'id');

            if (!empty($courseIds)) {
                $data['assignments'] = $assignmentModel->whereIn('course_id', $courseIds)->findAll();
            } else {
                $data['assignments'] = [];
            }
        } elseif ($userRole === 'student') {
            // Students see assignments from their enrolled courses
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $enrollments = $enrollmentModel->where('student_id', $userId)->findAll();
            $courseIds = array_column($enrollments ?? [], 'course_id');

            if (!empty($courseIds)) {
                $data['assignments'] = $assignmentModel->whereIn('course_id', $courseIds)->findAll();
            } else {
                $data['assignments'] = [];
            }
        } elseif ($userRole === 'admin') {
            // Admins see all assignments
            $data['assignments'] = $assignmentModel->findAll();
        } else {
            $data['assignments'] = [];
        }

        $data['userRole'] = $userRole;
        $data['userId'] = $userId;

        return view('assignments/index', $data);
    }

    /**
     * View a specific assignment
     */
    public function view($assignmentId = null)
    {
        if (!$assignmentId) {
            return redirect()->to(base_url('assignments'))->with('error', 'Invalid assignment ID.');
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $assignmentModel = new \App\Models\AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (!$assignment) {
            return redirect()->to(base_url('assignments'))->with('error', 'Assignment not found.');
        }

        return view('assignments/view', ['assignment' => $assignment]);
    }

    /**
     * Create a new assignment (Teacher/Admin only)
     */
    public function create()
    {
        $session = session();
        $userRole = $session->get('role');

        // Only teachers and admins can create assignments
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($this->request->getMethod() === 'POST') {
            $title = $this->request->getPost('title');
            $description = $this->request->getPost('description');
            $courseId = $this->request->getPost('course_id');
            $dueDate = $this->request->getPost('due_date');

            if (!$title || !$courseId || !$dueDate) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields'
                ]);
            }

            try {
                $assignmentModel = new \App\Models\AssignmentModel();
                $assignmentModel->insert([
                    'course_id' => $courseId,
                    'title' => $title,
                    'description' => $description,
                    'due_date' => $dueDate,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                // Get the course details for notification message
                $courseModel = new \App\Models\CourseModel();
                $course = $courseModel->find($courseId);
                $courseName = $course ? $course['course_name'] : 'Unknown Course';

                // Get all enrolled students in this course
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $enrolledStudents = $enrollmentModel->where('course_id', $courseId)
                                                    ->where('status', 'approved')
                                                    ->select('user_id')
                                                    ->findAll();

                // Create notifications for all enrolled students
                if (!empty($enrolledStudents)) {
                    $notificationModel = new \App\Models\NotificationModel();
                    $notificationMessage = "New assignment created in {$courseName}: {$title}";

                    foreach ($enrolledStudents as $enrollment) {
                        $notificationModel->insert([
                            'user_id' => $enrollment['user_id'],
                            'message' => $notificationMessage,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Assignment created successfully and notifications sent to ' . count($enrolledStudents) . ' student(s)'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error creating assignment: ' . $e->getMessage()
                ]);
            }
        }

        return redirect()->to(base_url('assignments'));
    }

    /**
     * Submit assignment (Student only)
     */
    public function submit($assignmentId = null)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        if ($userRole !== 'student') {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Students only']);
        }

        if (!$assignmentId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid assignment ID']);
        }

        if ($this->request->getMethod() === 'POST') {
            $submissionText = $this->request->getPost('submission');

            // Handle file upload if present
            $file = $this->request->getFile('file');
            $fileName = null;

            if ($file && $file->isValid() && !$file->hasMoved()) {
                $fileName = $file->getRandomName();
                $file->move('writable/uploads', $fileName);
            }

            $submissionModel = new \App\Models\SubmissionModel();
            $submissionModel->insert([
                'assignment_id' => $assignmentId,
                'student_id' => $userId,
                'submission_text' => $submissionText,
                'file_path' => $fileName,
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment submitted successfully'
            ]);
        }

        return redirect()->to(base_url('assignments/' . $assignmentId));
    }

    /**
     * View submissions for a specific assignment (Teacher/Admin only)
     */
    public function submissions($assignmentId = null)
    {
        if (!$assignmentId) {
            return redirect()->to(base_url('assignments'))->with('error', 'Invalid assignment ID.');
        }

        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('role');

        // Only teachers and admins can view submissions
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return redirect()->to(base_url('assignments'))->with('error', 'Unauthorized access.');
        }

        $assignmentModel = new \App\Models\AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (!$assignment) {
            return redirect()->to(base_url('assignments'))->with('error', 'Assignment not found.');
        }

        // Get submissions for this assignment
        $submissionModel = new \App\Models\SubmissionModel();
        $submissions = $submissionModel->where('assignment_id', $assignmentId)->findAll();

        // Get student names for each submission
        $userModel = new \App\Models\UserModel();
        foreach ($submissions as &$submission) {
            $student = $userModel->find($submission['student_id']);
            $submission['student_name'] = $student ? ($student['first_name'] . ' ' . $student['last_name']) : 'Unknown Student';
        }

        return view('assignments/submissions', [
            'assignment' => $assignment,
            'submissions' => $submissions
        ]);
    }

    /**
     * Save grade for a submission (Teacher/Admin only)
     */
    public function saveGrade()
    {
        $session = session();
        $userRole = $session->get('role');

        // Only teachers and admins can grade submissions
        if (!in_array($userRole, ['teacher', 'admin'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if ($this->request->getMethod() === 'POST') {
            $submissionId = $this->request->getPost('submission_id');
            $assignmentId = $this->request->getPost('assignment_id');
            $grade = $this->request->getPost('grade');
            $feedback = $this->request->getPost('feedback');

            if (!$submissionId || !$assignmentId || !$grade) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required fields'
                ]);
            }

            try {
                $submissionModel = new \App\Models\SubmissionModel();
                $submissionModel->update($submissionId, [
                    'grade' => $grade,
                    'feedback' => $feedback,
                    'graded_at' => date('Y-m-d H:i:s'),
                ]);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grade saved successfully'
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error saving grade: ' . $e->getMessage()
                ]);
            }
        }

        return $this->response->setStatusCode(405)
            ->setJSON(['success' => false, 'message' => 'Method not allowed']);
    }
}
