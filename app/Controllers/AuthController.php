<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        // Jika sudah login, redirect sesuai role
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }

        return view('auth/login');
    }

    public function processLogin()
    {
        $rules = [
            'username' => 'required', // Bisa email atau nim_nidn
            'password' => 'required'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        
        // Cari user berdasarkan email ATAU nim_nidn
        $user = $userModel->groupStart()
                          ->where('email', $username)
                          ->orWhere('nim_nidn', $username)
                          ->groupEnd()
                          ->first();

        if ($user) {
            if (password_verify($this->request->getPost('password'), $user['password'])) {
                $sessionData = [
                    'id'         => $user['id'],
                    'name'       => $user['name'],
                    'email'      => $user['email'],
                    'role'       => $user['role'],
                    'isLoggedIn' => true
                ];
                session()->set($sessionData);
                return $this->redirectBasedOnRole($user['role']);
            } else {
                return redirect()->back()->withInput()->with('error', 'Password salah.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Email tidak ditemukan.');
        }
    }



    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function redirectBasedOnRole($role)
    {
        if ($role === 'admin') {
            return redirect()->to('/admin/dashboard');
        } elseif ($role === 'dosen') {
            return redirect()->to('/dosen/dashboard');
        } else {
            return redirect()->to('/mahasiswa/dashboard');
        }
    }
}
