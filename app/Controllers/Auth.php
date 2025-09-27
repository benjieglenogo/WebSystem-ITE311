<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form']); // make sure form helper is loaded
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'post') {
            $email    = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Find user by email
            $user = $model->where('email', $email)->first();

            $isValid = false;
            if ($user) {
                // First try bcrypt/argon hash verify
                if (password_verify($password, $user['password'])) {
                    $isValid = true;
                } else {
                    // Legacy plaintext migration: if matches exactly, rehash and save
                    if ($user['password'] === $password) {
                        $isValid = true;
                        $model->update($user['id'], [
                            'password' => password_hash($password, PASSWORD_DEFAULT),
                        ]);
                    }
                }
            }

            if ($isValid) {
                // Store session data
                $session->set([
                    'id'         => $user['id'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true,
                ]);

                // Unified redirect: everyone goes to the same dashboard URL
                return redirect()->to(site_url('dashboard'));
            } else {
                $session->setFlashdata('login_error', 'Invalid login credentials');
                return redirect()->back();
            }
        }

        // Show login form
        return view('login');
    }

    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'post') {
            $name = $this->request->getPost('name');
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');
            $passwordConfirm = $this->request->getPost('password_confirm');

            if ($password !== $passwordConfirm) {
                $session->setFlashdata('register_error', 'Passwords do not match.');
                return redirect()->back()->withInput();
            }

            // Default role for newly registered users (adjust as needed)
            $role = 'student';

            // Check if email already exists
            $existing = $model->where('email', $email)->first();
            if ($existing) {
                $session->setFlashdata('register_error', 'Email is already registered.');
                return redirect()->back()->withInput();
            }

            $model->insert([
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => $role,
            ]);

            // Auto-login after successful registration
            $newUserId = $model->getInsertID();
            $session->set([
                'id' => $newUserId,
                'email' => $email,
                'role' => $role,
                'isLoggedIn' => true,
            ]);

            return redirect()->to(site_url('dashboard'));
        }

        return view('register');
    }

    public function dashboard()
    {
        $session = session();

        // Authorization check: ensure user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Please log in to access the dashboard.');
            return redirect()->to(site_url('login'));
        }

        $role = $session->get('role');

        // Example: fetch role-specific data (stubbed here; replace with real queries)
        $dataForView = [];

        if ($role === 'admin') {
            // e.g., get counts of users, courses, reports, etc.
            $dataForView['widgets'] = [
                'users' => 120,
                'reports' => 8,
                'settings' => true,
            ];
        } elseif ($role === 'teacher') {
            // e.g., get classes, assignments to grade, notifications
            $dataForView['widgets'] = [
                'classes' => 4,
                'toGrade' => 27,
                'announcements' => 3,
            ];
        } elseif ($role === 'student') {
            // e.g., get enrolled courses, pending assignments, announcements
            $dataForView['widgets'] = [
                'courses' => 5,
                'assignments' => 2,
                'announcements' => 6,
            ];
        }

        $dataForView['role'] = $role;
        $dataForView['email'] = $session->get('email');

        return view('dashboard', $dataForView);
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'));
    }
}
