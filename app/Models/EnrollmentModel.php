<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date', 'status', 'approved_by', 'approved_at', 'rejection_reason'];
    protected $useTimestamps = false;

    /**
     * Enroll a user in a course
     */
    public function enrollUser($data)
    {
        // Check if enrollment_date column exists, if not remove it from data
        if (isset($data['enrollment_date']) && !$this->db->fieldExists('enrollment_date', 'enrollments')) {
            unset($data['enrollment_date']);
        }
        return $this->insert($data);
    }

    /**
     * Get all enrollments for a specific user
     */
    public function getUserEnrollments($user_id)
    {
        // Check if teacher_id column exists before trying to join
        if ($this->db->fieldExists('teacher_id', 'courses')) {
            $select = 'enrollments.*, courses.id as course_id, courses.course_name, courses.course_code, courses.description, courses.teacher_id, users.name as teacher_name';

            $query = $this->select($select)
                          ->join('courses', 'courses.id = enrollments.course_id')
                          ->join('users', 'users.id = courses.teacher_id', 'left');

            return $query->where('enrollments.user_id', $user_id)
                         ->findAll();
        } else {
            // Fallback query without teacher information
            $select = 'enrollments.*, courses.id as course_id, courses.course_name, courses.course_code, courses.description';

            $query = $this->select($select)
                          ->join('courses', 'courses.id = enrollments.course_id');

            return $query->where('enrollments.user_id', $user_id)
                         ->findAll();
        }
    }

    /**
     * Check if a user is already enrolled in a specific course
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    /**
     * Get all available courses (not enrolled by user)
     */
    public function getAvailableCourses($user_id)
    {
        // For now, return a simple query - you can expand this when CourseModel is created
        $db = \Config\Database::connect();
        $builder = $db->table('courses');

        $enrolled_course_ids = $this->select('course_id')
                                   ->where('user_id', $user_id)
                                   ->findAll();

        $enrolled_ids = array_column($enrolled_course_ids, 'course_id');

        if (empty($enrolled_ids)) {
            return $builder->get()->getResultArray();
        }

        return $builder->whereNotIn('id', $enrolled_ids)->get()->getResultArray();
    }

    /**
     * Get pending enrollment requests for a teacher
     */
    public function getPendingRequests($teacher_id = null, $course_id = null)
    {
        $query = $this->select('enrollments.*, users.name as student_name, users.email as student_email, courses.course_name, courses.course_code')
                      ->join('users', 'users.id = enrollments.user_id')
                      ->join('courses', 'courses.id = enrollments.course_id')
                      ->where('enrollments.status', 'pending');

        if ($course_id) {
            $query->where('enrollments.course_id', $course_id);
        }

        if ($teacher_id) {
            $query->where('courses.teacher_id', $teacher_id);
        }

        return $query->orderBy('enrollments.enrollment_date', 'DESC')->findAll();
    }

    /**
     * Get approved enrollments for a teacher
     */
    public function getApprovedEnrollments($teacher_id = null, $course_id = null)
    {
        $query = $this->select('enrollments.*, users.name as student_name, users.email as student_email, courses.course_name, courses.course_code, courses.id as course_id')
                      ->join('users', 'users.id = enrollments.user_id')
                      ->join('courses', 'courses.id = enrollments.course_id')
                      ->where('enrollments.status', 'approved');

        if ($course_id) {
            $query->where('enrollments.course_id', $course_id);
        }

        if ($teacher_id) {
            $query->where('courses.teacher_id', $teacher_id);
        }

        return $query->orderBy('enrollments.approved_at', 'DESC')->findAll();
    }

    /**
     * Approve an enrollment request
     */
    public function approveEnrollment($enrollment_id, $approved_by)
    {
        return $this->update($enrollment_id, [
            'status' => 'approved',
            'approved_by' => $approved_by,
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Reject an enrollment request
     */
    public function rejectEnrollment($enrollment_id, $rejection_reason = null)
    {
        return $this->update($enrollment_id, [
            'status' => 'rejected',
            'rejection_reason' => $rejection_reason
        ]);
    }

    /**
     * Unenroll a student from a course
     */
    public function unenrollStudent($enrollment_id)
    {
        return $this->delete($enrollment_id);
    }

    /**
     * Get enrollment status for a user in a course
     */
    public function getEnrollmentStatus($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->first();
    }
}
