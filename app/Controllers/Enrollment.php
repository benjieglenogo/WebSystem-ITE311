<?php

namespace App\Controllers;

class Enrollment extends BaseController
{
    /**
     * Get pending enrollment requests
     * Admin only can see pending requests
     */
    public function pendingRequests()
    {
        $session = session();

        // Redirect if not logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Only admins can view pending requests
        if ($userRole !== 'admin') {
            return redirect()->to(base_url('/'))->with('error', 'Access denied. Admin privileges required.');
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $userModel = new \App\Models\UserModel();

        $data = ['userRole' => $userRole];

        // Admins see all pending requests
        $pendingRequests = $enrollmentModel->getPendingRequests();
        $approvedEnrollments = $enrollmentModel->getApprovedEnrollments();
        $data['pendingRequests'] = $pendingRequests;
        $data['approvedEnrollments'] = $approvedEnrollments;
        $data['title'] = 'Enrollment Management';

        return view('enrollment/pending_requests', $data);
    }

    /**
     * Approve an enrollment request
     */
    public function approveRequest($enrollmentId = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$enrollmentId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid enrollment ID']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify authorization (teacher must own the course)
        if ($userRole === 'teacher') {
            $course = $courseModel->find($enrollment['course_id']);
            if ($course['teacher_id'] != $userId) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
        }

        // Approve the enrollment
        $result = $enrollmentModel->approveEnrollment($enrollmentId, $userId);

        if ($result) {
            // Send notification to student
            $course = $courseModel->find($enrollment['course_id']);
            $courseName = $course['course_name'] ?? 'the course';
            $message = "Your enrollment request for {$courseName} has been approved!";
            $notificationModel->createNotification($enrollment['user_id'], $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment approved successfully'
            ]);
        } else {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Failed to approve enrollment']);
        }
    }

    /**
     * Reject an enrollment request
     */
    public function rejectRequest($enrollmentId = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$enrollmentId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid enrollment ID']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify authorization
        if ($userRole === 'teacher') {
            $course = $courseModel->find($enrollment['course_id']);
            if ($course['teacher_id'] != $userId) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
        }

        // Get rejection reason if provided
        $rejectionReason = $this->request->getPost('rejection_reason') ?? 'No reason provided';

        // Reject the enrollment
        $result = $enrollmentModel->rejectEnrollment($enrollmentId, $rejectionReason);

        if ($result) {
            // Send notification to student
            $course = $courseModel->find($enrollment['course_id']);
            $courseName = $course['course_name'] ?? 'the course';
            $message = "Your enrollment request for {$courseName} has been rejected. Reason: {$rejectionReason}";
            $notificationModel->createNotification($enrollment['user_id'], $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment rejected successfully'
            ]);
        } else {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Failed to reject enrollment']);
        }
    }

    /**
     * Unenroll a student from a course
     */
    public function unenroll($enrollmentId = null)
    {
        $session = session();
        
        if (!$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        if (!in_array($userRole, ['admin', 'teacher'])) {
            return $this->response->setStatusCode(403)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$enrollmentId) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid enrollment ID']);
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $courseModel = new \App\Models\CourseModel();
        $notificationModel = new \App\Models\NotificationModel();

        $enrollment = $enrollmentModel->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Enrollment not found']);
        }

        // Verify authorization
        if ($userRole === 'teacher') {
            $course = $courseModel->find($enrollment['course_id']);
            if ($course['teacher_id'] != $userId) {
                return $this->response->setStatusCode(403)
                    ->setJSON(['success' => false, 'message' => 'Unauthorized']);
            }
        }

        // Unenroll the student
        $result = $enrollmentModel->unenrollStudent($enrollmentId);

        if ($result) {
            // Send notification to student
            $course = $courseModel->find($enrollment['course_id']);
            $courseName = $course['course_name'] ?? 'the course';
            $message = "You have been unenrolled from {$courseName}";
            $notificationModel->createNotification($enrollment['user_id'], $message);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student unenrolled successfully'
            ]);
        } else {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Failed to unenroll student']);
        }
    }
}
