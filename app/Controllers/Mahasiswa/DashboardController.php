<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\TugasModel;
use App\Models\TugasSubmitModel;
use App\Models\KuisModel;
use App\Models\KuisAttemptModel;
use App\Models\NilaiAkhirModel;
use App\Models\PesertaKelasModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Kelas yang diikuti mahasiswa
        $jumlahKelas = $db->table('kelas_mahasiswa')
                          ->where('mahasiswa_id', $mahasiswaId)
                          ->countAllResults();

        // Tugas yang belum dikumpulkan (belum ada di tugas_submit)
        $tugasBelumKumpul = $db->table('tugas')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = tugas.kelas_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->where('tugas.deadline >', date('Y-m-d H:i:s'))
            ->whereNotIn('tugas.id', function($q) use ($mahasiswaId) {
                return $q->select('tugas_id')->from('tugas_submit')->where('mahasiswa_id', $mahasiswaId);
            })
            ->countAllResults();

        // Kuis yang tersedia (belum habis waktu & belum dicoba / masih bisa coba lagi)
        $kuisTersedia = $db->table('kuis')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = kuis.kelas_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->where('kuis.deadline >', date('Y-m-d H:i:s'))
            ->countAllResults();

        // Daftar kelas yang diikuti (5 terbaru)
        $kelasList = $db->table('kelas_mahasiswa')
            ->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, users.name as nama_dosen')
            ->join('kelas', 'kelas.id = kelas_mahasiswa.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('users', 'users.id = mata_kuliah.dosen_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->limit(5)
            ->get()->getResultArray();

        // Tugas mendekati deadline (3 terdekat yang belum dikumpulkan)
        $tugasDeadline = [];
        $latestPengumuman = [];
        $kelasIds = array_column($kelasList, 'kelas_id');
        if (!empty($kelasIds)) {
            $tugasDeadline = $db->table('tugas')
                ->select('tugas.*, mata_kuliah.nama as nama_mk')
                ->join('kelas', 'kelas.id = tugas.kelas_id')
                ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                ->whereIn('tugas.kelas_id', $kelasIds)
                ->where('tugas.deadline >', date('Y-m-d H:i:s'))
                ->whereNotIn('tugas.id', function($q) use ($mahasiswaId) {
                    return $q->select('tugas_id')->from('tugas_submit')->where('mahasiswa_id', $mahasiswaId);
                })
                ->orderBy('tugas.deadline', 'ASC')
                ->limit(3)
                ->get()->getResultArray();

            $latestPengumuman = $db->table('pengumuman')
                ->select('pengumuman.*, mata_kuliah.nama as nama_mk')
                ->join('kelas', 'kelas.id = pengumuman.kelas_id')
                ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                ->whereIn('pengumuman.kelas_id', $kelasIds)
                ->orderBy('pengumuman.created_at', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        }

        // Ambil jadwal perkuliahan hari ini & besok (untuk reminder)
        $reminders = [];
        if (!empty($kelasIds)) {
            $reminders = $db->table('jadwal_kuliah')
                ->select('jadwal_kuliah.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                ->join('kelas', 'kelas.id = jadwal_kuliah.kelas_id')
                ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                ->whereIn('jadwal_kuliah.kelas_id', $kelasIds)
                ->where('jadwal_kuliah.tanggal >=', date('Y-m-d'))
                ->where('jadwal_kuliah.tanggal <=', date('Y-m-d', strtotime('+1 day')))
                ->orderBy('jadwal_kuliah.tanggal', 'ASC')
                ->orderBy('jadwal_kuliah.jam_mulai', 'ASC')
                ->get()->getResultArray();
        }

        // Ambil data user profil untuk gamifikasi
        $userProfile = $db->table('users')->where('id', $mahasiswaId)->get()->getRowArray();
        
        // Ambil badge yang diperoleh mahasiswa
        $userBadges = $db->table('user_badges')
                         ->select('badges.*')
                         ->join('badges', 'badges.id = user_badges.badge_id')
                         ->where('user_badges.user_id', $mahasiswaId)
                         ->get()->getResultArray();

        $data = [
            'title'              => 'Dashboard Mahasiswa',
            'jumlahKelas'        => $jumlahKelas,
            'tugasBelumKumpul'   => $tugasBelumKumpul,
            'kuisTersedia'       => $kuisTersedia,
            'kelasList'          => $kelasList,
            'tugasDeadline'      => $tugasDeadline,
            'latestPengumuman'   => $latestPengumuman,
            'reminders'          => $reminders,
            'userProfile'        => $userProfile,
            'userBadges'         => $userBadges,
        ];
        return view('mahasiswa/dashboard', $data);
    }
}
