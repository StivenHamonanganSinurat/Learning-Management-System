<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;
use App\Models\TugasModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $dosen_id = session()->get('id');
        $kelasModel = new KelasModel();
        
        // Cari kelas yang mata kuliahnya diajar oleh dosen ini
        $kelas = $kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                            ->where('mata_kuliah.dosen_id', $dosen_id)
                            ->findAll();

        $data = [
            'title'       => 'Dashboard Dosen',
            'total_kelas' => count($kelas),
            'kelas'       => $kelas
        ];
        return view('dosen/dashboard', $data);
    }
}
