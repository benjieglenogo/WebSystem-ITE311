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

            $assignmentModel = new \App\Models\AssignmentModel();
            $assignmentModel->insert([
                'course_id' => $courseId,
                'title' => $title,
                'description' => $description,
                'due_date' => $dueDate,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Assignment created successfully'
            ]);
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
}
