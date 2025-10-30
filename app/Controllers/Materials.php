<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use CodeIgniter\HTTP\ResponseInterface;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        // Check if user is logged in and is admin or teacher
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        if ($this->request->getMethod() === 'POST') {
            // Load CodeIgniter's File Uploading Library and Validation Library
            $file = $this->request->getFile('material_file');
            $validation = \Config\Services::validation();

            // Configure the upload preferences
            $uploadPath = WRITEPATH . 'uploads/materials/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Set validation rules
            $validation->setRules([
                'material_file' => [
                    'label' => 'Material File',
                    'rules' => 'uploaded[material_file]|max_size[material_file,10240]|ext_in[material_file,pdf,doc,docx,txt,jpg,jpeg,png,mp4,avi]',
                ],
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                return redirect()->back()->with('error', $validation->getError('material_file'));
            }

            // Perform the file upload
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);

                // Prepare data and save to database
                $materialModel = new MaterialModel();
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'writable/uploads/materials/' . $newName,
                    'created_at' => date('Y-m-d H:i:s'),
                ];

                if ($materialModel->insertMaterial($data)) {
                    return redirect()->back()->with('success', 'Material uploaded successfully.');
                } else {
                    return redirect()->back()->with('error', 'Failed to save material.');
                }
            } else {
                return redirect()->back()->with('error', 'File upload failed.');
            }
        }

        // Display upload form
        return view('materials/upload', ['course_id' => $course_id]);
    }

    public function delete($material_id)
    {
        // Check if user is logged in and is admin or teacher
        if (!session()->get('isLoggedIn') || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->to('/login')->with('error', 'Access denied.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            // Delete file
            if (file_exists($material['file_path'])) {
                unlink($material['file_path']);
            }

            // Delete record
            if ($materialModel->delete($material_id)) {
                return redirect()->back()->with('success', 'Material deleted successfully.');
            }
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    public function download($material_id)
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to download materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Check if user is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled(session()->get('userId'), $material['course_id'])) {
            return redirect()->back()->with('error', 'You are not enrolled in this course.');
        }

        // Force download
        return $this->response->download($material['file_path'], null, true);
    }
}
