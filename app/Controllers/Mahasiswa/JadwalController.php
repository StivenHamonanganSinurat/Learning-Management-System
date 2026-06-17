<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class JadwalController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // 1. Dapatkan daftar kelas yang diikuti mahasiswa
        $kelasList = $db->table('kelas_mahasiswa')
            ->select('kelas_id')
            ->where('mahasiswa_id', $mahasiswaId)
            ->get()->getResultArray();

        $kelasIds = array_column($kelasList, 'kelas_id');

        $jadwal = [];
        if (!empty($kelasIds)) {
            // 2. Ambil jadwal dari kelas-kelas tersebut
            $jadwal = $db->table('jadwal_kuliah')
                ->select('jadwal_kuliah.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, users.name as nama_dosen')
                ->join('kelas', 'kelas.id = jadwal_kuliah.kelas_id')
                ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                ->join('users', 'users.id = mata_kuliah.dosen_id')
                ->whereIn('jadwal_kuliah.kelas_id', $kelasIds)
                ->orderBy('jadwal_kuliah.tanggal', 'ASC')
                ->orderBy('jadwal_kuliah.jam_mulai', 'ASC')
                ->get()->getResultArray();
        }

        $data = [
            'title'  => 'Jadwal Perkuliahan Saya',
            'jadwal' => $jadwal
        ];

        return view('mahasiswa/jadwal/index', $data);
    }
}
