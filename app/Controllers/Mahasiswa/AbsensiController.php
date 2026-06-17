<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class AbsensiController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Semua kelas yang diikuti mahasiswa
        $kelas = $db->table('kelas_mahasiswa')
            ->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, users.name as nama_dosen')
            ->join('kelas', 'kelas.id = kelas_mahasiswa.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('users', 'users.id = mata_kuliah.dosen_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->get()->getResultArray();

        // Rekap absensi per kelas
        $rekapAbsensi = [];
        foreach ($kelas as $k) {
            $absensiList = $db->table('absensi')
                ->select('absensi.*, detail_absensi.status')
                ->join('detail_absensi', 'detail_absensi.absensi_id = absensi.id AND detail_absensi.mahasiswa_id = ' . $mahasiswaId, 'left')
                ->where('absensi.kelas_id', $k['id'])
                ->orderBy('absensi.tanggal', 'ASC')
                ->get()->getResultArray();

            $statMap = ['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0];
            foreach ($absensiList as $a) {
                $status = $a['status'] ?? 'alpha';
                if (isset($statMap[$status])) $statMap[$status]++;
            }

            $totalPertemuan  = count($absensiList);
            $pctHadir        = $totalPertemuan > 0 ? round(($statMap['hadir'] / $totalPertemuan) * 100) : 0;

            $rekapAbsensi[] = [
                'kelas'          => $k,
                'absensiList'    => $absensiList,
                'statMap'        => $statMap,
                'totalPertemuan' => $totalPertemuan,
                'pctHadir'       => $pctHadir,
            ];
        }

        return view('mahasiswa/absensi/index', [
            'title'        => 'Rekap Absensi',
            'rekapAbsensi' => $rekapAbsensi,
        ]);
    }
}
