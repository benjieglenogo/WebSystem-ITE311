<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['course_id', 'title', 'description', 'due_date', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $validationRules = [
        'course_id' => 'required|integer',
        'title' => 'required|string|max_length[255]',
        'description' => 'string',
        'due_date' => 'required|valid_date',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
