<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class MateriController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Semua materi dari semua kelas yang diikuti mahasiswa
        $materi = $db->table('materi')
            ->select('materi.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
            ->join('kelas', 'kelas.id = materi.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = materi.kelas_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->orderBy('materi.created_at', 'DESC')
            ->get()->getResultArray();

        // Daftar kelas untuk filter
        $kelas = $db->table('kelas_mahasiswa')
            ->select('kelas.*, mata_kuliah.nama as nama_mk')
            ->join('kelas', 'kelas.id = kelas_mahasiswa.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->get()->getResultArray();

        return view('mahasiswa/materi/index', [
            'title'  => 'Materi Kuliah',
            'materi' => $materi,
            'kelas'  => $kelas,
        ]);
    }
}
