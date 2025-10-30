<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends BaseController
{
    public function dashboard()
    {
        // Fetch courses for admin to manage materials
        $courseModel = new \App\Models\CourseModel();
        $data['courses'] = $courseModel->findAll();

        return view('admin_dashboard', $data);
    }
}
