<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'course_code',
        'course_name',
        'description',
        'teacher_id',
        'school_year',
        'semester',
        'schedule',
        'status',
        'start_date',
        'end_date'
    ];

    protected $useTimestamps = false;

    /**
     * Get all available courses for enrollment (not enrolled by user)
     */
    public function getAvailableCourses($user_id)
    {
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Get courses the user is already enrolled in
        $enrolled_course_ids = $enrollmentModel->select('course_id')
            ->where('user_id', $user_id)
            ->findAll();

        $enrolled_ids = array_column($enrolled_course_ids, 'course_id');

        // Get all active courses
        $builder = $this->where('status', 'active');

        if (!empty($enrolled_ids)) {
            $builder->whereNotIn('id', $enrolled_ids);
        }

        return $builder->findAll();
    }

    /**
     * Get course by ID with teacher information
     */
    public function getCourseWithTeacher($courseId)
    {
        return $this->select('courses.*, users.name as teacher_name')
            ->join('users', 'users.id = courses.teacher_id', 'left')
            ->find($courseId);
    }
}
