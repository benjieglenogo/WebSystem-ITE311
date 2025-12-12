<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'file_type', 'file_size', 'description', 'created_at'];

    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)->findAll();
    }

    /**
     * Get material by ID
     */
    public function getMaterialById($material_id)
    {
        return $this->find($material_id);
    }

    /**
     * Get all materials with course information
     */
    public function getAllMaterialsWithCourses()
    {
        return $this->select('materials.*, courses.course_name')
                   ->join('courses', 'courses.id = materials.course_id')
                   ->findAll();
    }

    /**
     * Get materials by course with pagination
     */
    public function getMaterialsByCoursePaginated($course_id, $limit = 10, $offset = 0)
    {
        return $this->where('course_id', $course_id)
                   ->orderBy('created_at', 'DESC')
                   ->findAll($limit, $offset);
    }
}
