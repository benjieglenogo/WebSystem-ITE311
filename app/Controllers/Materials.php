<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MaterialModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    /**
     * Upload material for a course
     */
    public function upload($courseId = null)
    {
        $session = session();

        // Check if user is logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Check if user is admin or teacher
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'You do not have permission to upload materials.');
        }

        // Get course_id from route or POST
        if ($courseId) {
            $course_id = $courseId;
        } else {
            $course_id = $this->request->getPost('course_id');
        }

        if (!$course_id) {
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        // Verify course exists
        $courseModel = new CourseModel();
        $course = $courseModel->find($course_id);
        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Process file upload
        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('material_file');

            if (!$file || !$file->isValid()) {
                return redirect()->back()->with('error', 'No file was uploaded or file is invalid.');
            }

            // Validate file
            if ($file->hasMoved()) {
                return redirect()->back()->with('error', 'File has already been moved.');
            }

            // SECURE FILE VALIDATION
            // 1. Check file size (max 10MB)
            $maxSize = 10 * 1024 * 1024; // 10MB
            if ($file->getSize() > $maxSize) {
                return redirect()->back()->with('error', 'File size exceeds maximum limit of 10MB.');
            }

            // 2. Check file extension
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt'];
            $fileExtension = strtolower($file->getClientExtension());

            if (!in_array($fileExtension, $allowedExtensions)) {
                return redirect()->back()->with('error', 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions));
            }

            // 3. Check MIME type for additional security
            $allowedMimeTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/zip',
                'application/x-rar-compressed',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'text/plain'
            ];

            $fileMimeType = $file->getMimeType();
            if (!in_array($fileMimeType, $allowedMimeTypes)) {
                return redirect()->back()->with('error', 'File type not allowed for security reasons.');
            }

            // Create uploads directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials' . DIRECTORY_SEPARATOR;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename with original extension
            $newName = $file->getRandomName();
            $filePath = 'materials' . DIRECTORY_SEPARATOR . $newName;

            // Move file
            if ($file->move(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'materials', $newName)) {
                // Save to database
                $materialModel = new MaterialModel();
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => $filePath,
                    'file_type' => $fileExtension,
                    'file_size' => $file->getSize(),
                    'description' => $this->request->getPost('description') ?? '',
                    'created_at' => date('Y-m-d H:i:s')
                ];

                if ($materialModel->insert($data)) {
                    return redirect()->to(base_url('materials/course/' . $course_id))
                        ->with('success', 'Material uploaded successfully!');
                } else {
                    // Delete uploaded file if database insert failed
                    unlink(WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $filePath);
                    return redirect()->back()->with('error', 'Failed to save material to database.');
                }
            } else {
                return redirect()->back()->with('error', 'Failed to upload file: ' . implode(', ', $file->getErrors()));
            }
        }

        // GET request - show upload form
        return view('materials/upload', [
            'course_id' => $course_id,
            'course_name' => $course['course_name']
        ]);
    }

    /**
     * Display materials for a course
     */
    public function display($courseId = null)
    {
        $session = session();

        // Check if user is logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        if (!$courseId) {
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        // Verify course exists
        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Check if user has access to this course
        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        if ($userRole === 'student') {
            // Check if student is enrolled in the course
            $enrollmentModel = new EnrollmentModel();
            if (! $enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                return redirect()->back()->with('error', 'You must be enrolled in this course to view materials.');
            }
        } elseif ($userRole !== 'admin' && $userRole !== 'teacher') {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Get materials for this course
        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);

        // Load view
        return view('materials/display', [
            'course_id' => $courseId,
            'course_name' => $course['course_name'],
            'materials' => $materials
        ]);
    }

    /**
     * Download material
     */
    public function download($materialId = null)
    {
        $session = session();
        
        // Check if user is logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        if (!$materialId) {
            return redirect()->back()->with('error', 'Material ID is required.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Check if user has access (student must be enrolled in the course)
        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        if ($userRole === 'student') {
            // Check if student is enrolled in the course
            $enrollmentModel = new EnrollmentModel();
            if (! $enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
                return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
            }
        } elseif ($userRole !== 'admin' && $userRole !== 'teacher') {
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Get file path
        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $material['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'File not found on server.');
        }

        // Download file
        return $this->response->download($filePath, null)->setFileName($material['file_name']);
    }

    /**
     * Forward material to another course
     */
    public function forward()
    {
        $session = session();

        // Check if user is logged in
        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        // Check if user is admin
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Only admins can forward materials.']);
        }

        // Get POST data
        $materialId = $this->request->getPost('material_id');
        $targetCourseId = $this->request->getPost('target_course_id');

        if (!$materialId || !$targetCourseId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Material ID and target course ID are required.']);
        }

        $materialModel = new MaterialModel();
        $courseModel = new CourseModel();

        // Get the original material
        $originalMaterial = $materialModel->find($materialId);
        if (!$originalMaterial) {
            return $this->response->setJSON(['success' => false, 'message' => 'Material not found.']);
        }

        // Check if target course exists
        $targetCourse = $courseModel->find($targetCourseId);
        if (!$targetCourse) {
            return $this->response->setJSON(['success' => false, 'message' => 'Target course not found.']);
        }

        // Copy the material to the target course
        $data = [
            'course_id' => $targetCourseId,
            'file_name' => $originalMaterial['file_name'],
            'file_path' => $originalMaterial['file_path'],
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($materialModel->insert($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Material forwarded successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to forward material.']);
        }
    }

    /**
     * Delete material
     */
    public function delete($materialId = null)
    {
        $session = session();

        // Check if user is logged in
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        // Check if user is admin or teacher
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return redirect()->back()->with('error', 'You do not have permission to delete materials.');
        }

        if (!$materialId) {
            return redirect()->back()->with('error', 'Material ID is required.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($materialId);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Delete file from server
        $filePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from database
        if ($materialModel->delete($materialId)) {
            // Add cache busting timestamp to prevent cached page display
            return redirect()->to(base_url('dashboard') . '?t=' . time())->with('success', 'Material deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete material.');
        }
    }
}
