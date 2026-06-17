<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $kelasModel = new KelasModel();
        $mkModel = new MataKuliahModel();

        $data = [
            'title'            => 'Dashboard Admin',
            'total_mahasiswa'  => $userModel->where('role', 'mahasiswa')->countAllResults(),
            'total_dosen'      => $userModel->where('role', 'dosen')->countAllResults(),
            'total_kelas'      => $kelasModel->countAllResults(),
            'total_mk'         => $mkModel->countAllResults()
        ];
        return view('admin/dashboard', $data);
    }
}
