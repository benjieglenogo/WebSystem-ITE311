<?php

namespace App\Controllers;

class Auth extends BaseController
{
    
     // Handles registration 
    
    public function register()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // Process form submission (POST)
        if ($this->request->getMethod() === 'POST') {
            $name = trim((string) $this->request->getPost('name'));
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');
            $passwordConfirm = (string) $this->request->getPost('password_confirm');

            if ($name === '' || $email === '' || $password === '' || $passwordConfirm === '') {
                return redirect()->back()->withInput()->with('register_error', 'All fields are required.');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->withInput()->with('register_error', 'Invalid email address.');
            }

            if ($password !== $passwordConfirm) {
                return redirect()->back()->withInput()->with('register_error', 'Passwords do not match.');
            }

            $userModel = new \App\Models\UserModel();

            if ($userModel->where('email', $email)->first()) {
                return redirect()->back()->withInput()->with('register_error', 'Email is already registered.');
            }

            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Default new users to 'student' to match migration ENUM
            $userId = $userModel->insert([
                'name' => $name,
                'email' => $email,
                'role' => 'student',
                'password' => $passwordHash,
            ], true);

            if (! $userId) {
                return redirect()->back()->withInput()->with('register_error', 'Registration failed.');
            }

            return redirect()
                ->to(base_url('login'))
                ->with('register_success', 'Account created successfully. Please log in.');
        }

        // Display form (GET)
        return view('register');
    }

    // Login 
    public function login()
    {
        $session = session();
        if ($session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        // Process form submission (POST)
        if ($this->request->getMethod() === 'POST') {
            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');

            $userModel = new \App\Models\UserModel();
            $user = $userModel->where('email', $email)->first();
            
            if ($user && password_verify($password, $user['password'])) {
                $session->set([
                    'isLoggedIn' => true,
                    'userId' => $user['id'] ?? null,
                    'userName' => $user['name'] ?? null,
                    'userEmail' => $user['email'] ?? $email,
                    'userRole' => $user['role'] ?? 'student',
                ]);
                return redirect()->to(base_url('dashboard'));
            }

            return redirect()->back()->with('login_error', 'Invalid credentials');
        }

        return view('login');
    }

 //Destroy user session
    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        $role = (string) $session->get('userRole');
        $userName = (string) $session->get('userName');
        $userEmail = (string) $session->get('userEmail');

        // Fetch role-specific data
        $data = [
            'role' => $role,
            'userName' => $userName,
            'userEmail' => $userEmail,
            'email' => $userEmail, // For backward compatibility with dashboard view
            'widgets' => [],
        ];

        if ($role === 'admin') {
            $userModel = new \App\Models\UserModel();
            $data['widgets']['users'] = $userModel->countAllResults();
            $data['widgets']['reports'] = 5; // Placeholder
        } elseif ($role === 'teacher') {
            $data['widgets']['classes'] = 3; // Placeholder
            $data['widgets']['toGrade'] = 12; // Placeholder
            $data['widgets']['announcements'] = 2; // Placeholder
        } else { // student
            $data['widgets']['courses'] = 4; // Placeholder
            $data['widgets']['assignments'] = 3; // Placeholder
            $data['widgets']['announcements'] = 1; // Placeholder
        }

        return view('auth/dashboard', $data);
    }
}