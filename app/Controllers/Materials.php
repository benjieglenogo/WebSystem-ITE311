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

        // Get course_id from route or POST first
        if ($courseId) {
            $course_id = $courseId;
        } else {
            $course_id = $this->request->getPost('course_id');
        }

        if (!$course_id) {
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        // For teachers, check if they are assigned to this course
        if ($userRole === 'teacher') {
            $userId = $session->get('userId');
            $courseModel = new CourseModel();
            $course = $courseModel->find($course_id);

            if (!$course || $course['teacher_id'] != $userId) {
                return redirect()->to(base_url('dashboard'))->with('error', 'You can only upload materials to your assigned courses.');
            }
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
            $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'mpg', 'mpeg'];
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
                'text/plain',
                // Video MIME types
                'video/mp4',
                'video/x-msvideo',
                'video/quicktime',
                'video/x-ms-wmv',
                'video/x-flv',
                'video/x-matroska',
                'video/mpeg',
                'video/mpg'
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
        if (!$this->request->isAJAX() && !$session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        if (!$courseId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Course ID is required.']);
            }
            return redirect()->back()->with('error', 'Course ID is required.');
        }

        // Verify course exists
        $courseModel = new CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Course not found.']);
            }
            return redirect()->back()->with('error', 'Course not found.');
        }

        // Check if user has access to this course
        $userRole = $session->get('userRole');
        $userId = $session->get('userId');

        if ($userRole === 'student') {
            // Check if student is enrolled in the course
            $enrollmentModel = new EnrollmentModel();
            if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'You must be enrolled in this course to view materials.']);
                }
                return redirect()->back()->with('error', 'You must be enrolled in this course to view materials.');
            }
        } elseif ($userRole !== 'admin' && $userRole !== 'teacher') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied.']);
            }
            return redirect()->back()->with('error', 'Access denied.');
        }

        // Get materials for this course
        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);

        // Check if this is an AJAX request for modal
        if ($this->request->isAJAX()) {
            // Return only the materials list HTML
            return view('materials/modal_content', ['materials' => $materials]);
        }

        // Load full view for regular page request
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
     * AJAX upload material for a course
     */
    public function ajaxUpload()
    {
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to upload materials.'
            ]);
        }

        // Check if user is admin or teacher
        $userRole = $session->get('userRole');
        if ($userRole !== 'admin' && $userRole !== 'teacher') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You do not have permission to upload materials.'
            ]);
        }

        // Get course_id from POST
        $course_id = $this->request->getPost('course_id');
        if (!$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course ID is required.'
            ]);
        }

        // For teachers, check if they are assigned to this course
        if ($userRole === 'teacher') {
            $userId = $session->get('userId');
            $courseModel = new CourseModel();
            $course = $courseModel->find($course_id);

            if (!$course || $course['teacher_id'] != $userId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'You can only upload materials to your assigned courses.'
                ]);
            }
        }

        // Verify course exists
        $courseModel = new CourseModel();
        $course = $courseModel->find($course_id);
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ]);
        }

        // Process file upload
        $file = $this->request->getFile('material_file');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No file was uploaded or file is invalid.'
            ]);
        }

        // Validate file
        if ($file->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File has already been moved.'
            ]);
        }

        // 1. Check file size (max 50MB for videos)
        $maxSize = 50 * 1024 * 1024; // 50MB
        if ($file->getSize() > $maxSize) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File size exceeds maximum limit of 50MB.'
            ]);
        }

        // 2. Check file extension
        $allowedExtensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar', 'jpg', 'jpeg', 'png', 'txt', 'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'mpg', 'mpeg'];
        $fileExtension = strtolower($file->getClientExtension());

        if (!in_array($fileExtension, $allowedExtensions)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedExtensions)
            ]);
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
            'text/plain',
            // Video MIME types
            'video/mp4',
            'video/x-msvideo',
            'video/quicktime',
            'video/x-ms-wmv',
            'video/x-flv',
            'video/x-matroska',
            'video/mpeg',
            'video/mpg'
        ];

        $fileMimeType = $file->getMimeType();
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File type not allowed for security reasons.'
            ]);
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
        if ($file->move($uploadPath, $newName)) {
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

            $materialId = $materialModel->insert($data);
            if ($materialId) {
                // Get the inserted material for the response
                $newMaterial = $materialModel->find($materialId);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Material uploaded successfully!',
                    'material' => $newMaterial
                ]);
            } else {
                // Delete uploaded file if database insert failed
                unlink($uploadPath . $newName);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to save material to database.'
                ]);
            }
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload file: ' . implode(', ', $file->getErrors())
            ]);
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
