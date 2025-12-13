<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Get all users for management
     */
    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return redirect()->to(base_url('login'))->with('error', 'Access denied.');
        }

        $users = $this->userModel->findAll();
        return $this->response->setJSON(['success' => true, 'users' => $users]);
    }

    /**
     * Create a new user
     */
    public function create()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
            } else {
                return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
            }
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/]',
            'role' => 'required|in_list[student,teacher,admin]',
        ], [
            'password' => [
                'regex_match' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $validation->getErrors()
                ])->setStatusCode(400);
            } else {
                return redirect()->back()->withInput()->with('error', 'Validation failed: ' . implode(', ', $validation->getErrors()));
            }
        }

        $name = trim($this->request->getPost('name'));
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        $role = $this->request->getPost('role');

        // Check for duplicate email
        if ($this->userModel->where('email', $email)->first()) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email already exists.'
                ])->setStatusCode(400);
            } else {
                return redirect()->back()->withInput()->with('error', 'Email already exists.');
            }
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $data = [
            'name' => $name,
            'email' => $email,
            'password' => $passwordHash,
            'role' => $role,
            'status' => 'active',
            'is_protected' => 0,
        ];

        $userId = $this->userModel->insert($data);
        if ($userId) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'User created successfully.',
                    'user_id' => $userId
                ]);
            } else {
                return redirect()->to(base_url('dashboard'))->with('success', 'User created successfully.');
            }
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to create user.'
            ])->setStatusCode(500);
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user.');
        }
    }

    /**
     * Update user (name, role, and status)
     */
    public function update()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
        }

        $userId = (int) $this->request->getPost('user_id');
        $name = trim($this->request->getPost('name'));
        $role = $this->request->getPost('role');
        $status = $this->request->getPost('status');

        if (!$userId || empty($name) || !in_array($role, ['student', 'teacher', 'admin']) || !in_array($status, ['active', 'inactive'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user data.'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Prevent modifying protected admin
        if ($user['is_protected'] == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot modify protected admin account.'
            ])->setStatusCode(403);
        }

        $data = [
            'name' => $name,
            'role' => $role,
            'status' => $status
        ];

        if ($this->userModel->update($userId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update user.'
        ])->setStatusCode(500);
    }

    /**
     * Update user role
     */
    public function updateRole()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
        }

        $userId = (int) $this->request->getPost('user_id');
        $newRole = $this->request->getPost('role');

        if (!$userId || !in_array($newRole, ['student', 'teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid user ID or role.'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Check if role can be edited using the new method
        if (!$this->userModel->canEditRole($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Admin user role is locked and cannot be edited.'
            ])->setStatusCode(403);
        }

        // Prevent demoting protected admin
        if ($user['is_protected'] == 1 && $newRole !== 'admin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot change role of protected admin account.'
            ])->setStatusCode(403);
        }

        if ($this->userModel->update($userId, ['role' => $newRole])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User role updated successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update user role.'
        ])->setStatusCode(500);
    }

    /**
     * Update user password
     */
    public function updatePassword()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
        }

        $userId = (int) $this->request->getPost('user_id');
        $newPassword = $this->request->getPost('password');

        if (!$userId || empty($newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID and password are required.'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Prevent changing password for protected admin accounts
        if ($user['is_protected'] == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot change password for protected admin account.'
            ])->setStatusCode(403);
        }

        // Validate password strength
        if (strlen($newPassword) < 8 || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $newPassword)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.'
            ])->setStatusCode(400);
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        if ($this->userModel->update($userId, ['password' => $passwordHash])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password updated successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update password.'
        ])->setStatusCode(500);
    }

    /**
     * Deactivate/Activate user
     */
    public function toggleStatus()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
        }

        $userId = (int) $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID is required.'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Prevent deactivating protected admin
        if ($user['is_protected'] == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot deactivate protected admin account.'
            ])->setStatusCode(403);
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';

        if ($this->userModel->update($userId, ['status' => $newStatus])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User status updated successfully.',
                'status' => $newStatus
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update user status.'
        ])->setStatusCode(500);
    }

    /**
     * Delete user (soft delete - set status to inactive)
     */
    public function delete()
    {
        $session = session();
        if (!$session->get('isLoggedIn') || $session->get('userRole') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied.'])->setStatusCode(403);
        }

        $userId = (int) $this->request->getPost('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User ID is required.'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not found.'
            ])->setStatusCode(404);
        }

        // Prevent deleting protected admin
        if ($user['is_protected'] == 1) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete protected admin account.'
            ])->setStatusCode(403);
        }

        // Soft delete - set status to inactive instead of actually deleting
        if ($this->userModel->update($userId, ['status' => 'inactive'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deactivated successfully.'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to deactivate user.'
        ])->setStatusCode(500);
    }
}
