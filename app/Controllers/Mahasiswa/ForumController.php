<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\ForumTopikModel;
use App\Models\ForumBalasanModel;

class ForumController extends BaseController
{
    protected $kelasModel;
    protected $topikModel;
    protected $balasanModel;

    public function __construct()
    {
        $this->kelasModel   = new KelasModel();
        $this->topikModel   = new ForumTopikModel();
        $this->balasanModel = new ForumBalasanModel();
    }

    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Ambil kelas yang diikuti
        $kelas = $db->table('kelas_mahasiswa')
            ->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
            ->join('kelas', 'kelas.id = kelas_mahasiswa.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->get()->getResultArray();

        $selected_kelas_id = $this->request->getGet('kelas_id');
        $topik = [];
        $selected_kelas = null;

        if (!empty($selected_kelas_id)) {
            // Validasi apakah mahasiswa terdaftar di kelas tersebut
            $isEnrolled = $db->table('kelas_mahasiswa')
                ->where('kelas_id', $selected_kelas_id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->countAllResults();

            if ($isEnrolled > 0) {
                $selected_kelas = $db->table('kelas')
                    ->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                    ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                    ->where('kelas.id', $selected_kelas_id)
                    ->get()->getRowArray();

                $topik = $this->topikModel->getByKelas($selected_kelas_id);
                foreach ($topik as &$t) {
                    $t['jumlah_balasan'] = $this->balasanModel->where('topik_id', $t['id'])->countAllResults();
                }
                unset($t);
            }
        }

        return view('mahasiswa/forum/index', [
            'title'          => 'Forum Diskusi Kelas',
            'kelas'          => $kelas,
            'selected_kelas' => $selected_kelas,
            'topik'          => $topik,
        ]);
    }

    public function createTopik($kelas_id)
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // Validasi enrollment
        $isEnrolled = $db->table('kelas_mahasiswa')
            ->where('kelas_id', $kelas_id)
            ->where('mahasiswa_id', $mahasiswaId)
            ->countAllResults();

        if ($isEnrolled == 0) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul'  => 'required|min_length[3]|max_length[255]',
            'konten' => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->topikModel->save([
            'kelas_id'   => $kelas_id,
            'pembuat_id' => $mahasiswaId,
            'judul'      => $this->request->getPost('judul'),
            'konten'     => $this->request->getPost('konten'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/mahasiswa/forum?kelas_id='.$kelas_id)->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function detailTopik($topik_id)
    {
        $mahasiswaId = session()->get('id');
        $topik = $this->topikModel->select('forum_topik.*, users.name as nama_pembuat, users.role as role_pembuat, mata_kuliah.nama as nama_mk')
                                  ->join('users', 'users.id = forum_topik.pembuat_id')
                                  ->join('kelas', 'kelas.id = forum_topik.kelas_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('forum_topik.id', $topik_id)
                                  ->first();

        if (!$topik) {
            return redirect()->back()->with('error', 'Topik tidak ditemukan.');
        }

        // Cek access kelas
        $db = \Config\Database::connect();
        $isEnrolled = $db->table('kelas_mahasiswa')
            ->where('kelas_id', $topik['kelas_id'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->countAllResults();

        if ($isEnrolled == 0) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Akses ditolak.');
        }

        $balasan = $this->balasanModel->getByTopik($topik_id);

        return view('mahasiswa/forum/detail', [
            'title'   => 'Topik Diskusi: ' . $topik['judul'],
            'topik'   => $topik,
            'balasan' => $balasan,
        ]);
    }

    public function reply($topik_id)
    {
        $mahasiswaId = session()->get('id');
        $topik = $this->topikModel->find($topik_id);

        if (!$topik) {
            return redirect()->back()->with('error', 'Topik tidak ditemukan.');
        }

        // Cek access kelas
        $db = \Config\Database::connect();
        $isEnrolled = $db->table('kelas_mahasiswa')
            ->where('kelas_id', $topik['kelas_id'])
            ->where('mahasiswa_id', $mahasiswaId)
            ->countAllResults();

        if ($isEnrolled == 0) {
            return redirect()->to('/mahasiswa/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'konten' => 'required|min_length[2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->balasanModel->save([
            'topik_id'   => $topik_id,
            'pembuat_id' => $mahasiswaId,
            'konten'     => $this->request->getPost('konten'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/mahasiswa/forum/detail/' . $topik_id)->with('success', 'Balasan berhasil dikirim.');
    }
}
