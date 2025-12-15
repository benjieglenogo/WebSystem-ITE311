<?php

namespace App\Models;

use CodeIgniter\Model;

class SubmissionModel extends Model
{
    protected $table = 'submissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['assignment_id', 'student_id', 'submission_text', 'file_path', 'submitted_at', 'grade', 'feedback', 'graded_at'];
    protected $useTimestamps = true;
    protected $createdField = 'submitted_at';
    protected $updatedField = 'graded_at';
    protected $validationRules = [
        'assignment_id' => 'required|integer',
        'student_id' => 'required|integer',
        'submission_text' => 'required|string',
        'file_path' => 'string',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
