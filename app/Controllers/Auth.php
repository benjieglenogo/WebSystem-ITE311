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

            if ($user && password_verify($password, $user['password'])) {
                // Store session data
                $session->set([
                    'id'         => $user['id'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true,
                ]);

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } elseif ($user['role'] === 'teacher') {
                    return redirect()->to('/teacher/dashboard');
                } elseif ($user['role'] === 'student') {
                    return redirect()->to('/student/dashboard');
                } else {
                    return redirect()->to('/login');
                }
            } else {
                $session->setFlashdata('error', 'Invalid login credentials');
                return redirect()->back();
            }
        }

        // Show login form
        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
