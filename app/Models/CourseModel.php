<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_name', 'description', 'teacher_id', 'created_at', 'course_code', 'school_year', 'semester', 'schedule', 'status', 'start_date', 'end_date'];

    /**
     * Search courses by name or description.
     *
     * @param string $term
     * @param int $limit
     * @return array
     */
    public function searchCourses(string $term = '', int $limit = 50): array
    {
        if (empty($term)) {
            return $this->orderBy('course_name', 'ASC')->findAll($limit);
        }

        return $this->like('course_name', $term)
                    ->orLike('description', $term)
                    ->orderBy('course_name', 'ASC')
                    ->findAll($limit);
    }
}
