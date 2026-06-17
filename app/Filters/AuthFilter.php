<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah user sudah login
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Cek role jika ada argumen
        if ($arguments && isset($arguments[0])) {
            $expectedRole = $arguments[0];
            $currentRole = session()->get('role');

            if ($currentRole !== $expectedRole) {
                // Redirect ke dashboard masing-masing jika role tidak sesuai
                if ($currentRole === 'admin') {
                    return redirect()->to('/admin/dashboard');
                } elseif ($currentRole === 'dosen') {
                    return redirect()->to('/dosen/dashboard');
                } else {
                    return redirect()->to('/mahasiswa/dashboard');
                }
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
