<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\PengumumanModel;

class PengumumanController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Ambil pengumuman dari semua kelas yang diikuti mahasiswa
        $pengumuman = $db->table('pengumuman')
            ->select('pengumuman.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
            ->join('kelas', 'kelas.id = pengumuman.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = pengumuman.kelas_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->orderBy('pengumuman.created_at', 'DESC')
            ->get()->getResultArray();

        return view('mahasiswa/pengumuman/index', [
            'title'      => 'Pengumuman Kuliah',
            'pengumuman' => $pengumuman,
        ]);
    }
}
