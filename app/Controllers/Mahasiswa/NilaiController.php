<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class NilaiController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        $nilai = $db->table('nilai_akhir')
            ->select('nilai_akhir.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk,
                      mata_kuliah.sks, kelas.tahun_ajaran, kelas.semester')
            ->join('kelas', 'kelas.id = nilai_akhir.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->where('nilai_akhir.mahasiswa_id', $mahasiswaId)
            ->orderBy('kelas.tahun_ajaran', 'DESC')
            ->get()->getResultArray();

        // Hitung IP Semester (IPS) dan IP Kumulatif (IPK)
        $nilaiGradeMap = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1, 'E' => 0];
        $totalBobot    = 0;
        $totalSks      = 0;
        foreach ($nilai as $n) {
            $poin       = $nilaiGradeMap[$n['grade']] ?? 0;
            $totalBobot += $poin * $n['sks'];
            $totalSks   += $n['sks'];
        }
        $ipk = $totalSks > 0 ? round($totalBobot / $totalSks, 2) : 0;

        return view('mahasiswa/nilai/index', [
            'title' => 'Nilai & Transkrip',
            'nilai' => $nilai,
            'ipk'   => $ipk,
        ]);
    }
}
