<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'post') {
            $email = $this->request->getPost('email');
            $password = $this->request->getPost('password');

            // Find user by email
            $user = $model->where('email', $email)->first();

            if ($user && password_verify($password, $user['password'])) {
                // Store session data
                $session->set([
                    'id'         => $user['id'],
                    'email'   => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true,
                ]);

                // Redirect to unified dashboard
                return redirect()->to('/dashboard');
            } else {
                $session->setFlashdata('error', 'Invalid login credentials');
                return redirect()->back();
            }
        }

        return view('login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function dashboard()
    {
        if (! session('isLoggedIn')) {
            return redirect()->to('/login');
        }

        return view('auth/dashboard');
    }
}