<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
    protected $useTimestamps = false;

    /**
     * Enroll a user in a course
     */
    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    /**
     * Get all enrollments for a specific user
     */
    public function getUserEnrollments($user_id)
    {
        return $this->select('enrollments.*, courses.course_name, courses.description')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
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
}
