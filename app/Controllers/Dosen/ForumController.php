<?php

namespace App\Controllers\Dosen;

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
        $dosen_id = session()->get('id');

        // Ambil semua kelas yang diajar dosen ini
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('mata_kuliah.dosen_id', $dosen_id)
                                  ->findAll();

        $selected_kelas_id = $this->request->getGet('kelas_id');
        $topik = [];
        $selected_kelas = null;

        if (!empty($selected_kelas_id)) {
            // Validasi kepemilikan kelas
            $selected_kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.dosen_id')
                                              ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                              ->where('kelas.id', $selected_kelas_id)
                                              ->first();

            if (!empty($selected_kelas) && $selected_kelas['dosen_id'] == $dosen_id) {
                // Ambil daftar topik
                $topik = $this->topikModel->getByKelas($selected_kelas_id);
                // Tambah jumlah reply per topik
                foreach ($topik as &$t) {
                    $t['jumlah_balasan'] = $this->balasanModel->where('topik_id', $t['id'])->countAllResults();
                }
                unset($t);
            } else {
                $selected_kelas = null;
            }
        }

        return view('dosen/forum/index', [
            'title'          => 'Forum Diskusi Kelas',
            'kelas'          => $kelas,
            'selected_kelas' => $selected_kelas,
            'topik'          => $topik,
        ]);
    }

    public function createTopik($kelas_id)
    {
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
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
            'pembuat_id' => $dosen_id,
            'judul'      => $this->request->getPost('judul'),
            'konten'     => $this->request->getPost('konten'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/dosen/forum?kelas_id='.$kelas_id)->with('success', 'Topik diskusi baru berhasil dibuat.');
    }

    public function detailTopik($topik_id)
    {
        $dosen_id = session()->get('id');
        $topik = $this->topikModel->select('forum_topik.*, users.name as nama_pembuat, users.role as role_pembuat, mata_kuliah.nama as nama_mk')
                                  ->join('users', 'users.id = forum_topik.pembuat_id')
                                  ->join('kelas', 'kelas.id = forum_topik.kelas_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('forum_topik.id', $topik_id)
                                  ->first();

        if (!$topik) {
            return redirect()->back()->with('error', 'Topik tidak ditemukan.');
        }

        // Ambil daftar balasan
        $balasan = $this->balasanModel->getByTopik($topik_id);

        return view('dosen/forum/detail', [
            'title'   => 'Topik Diskusi: ' . $topik['judul'],
            'topik'   => $topik,
            'balasan' => $balasan,
        ]);
    }

    public function reply($topik_id)
    {
        $dosen_id = session()->get('id');
        $topik = $this->topikModel->find($topik_id);

        if (!$topik) {
            return redirect()->back()->with('error', 'Topik tidak ditemukan.');
        }

        $rules = [
            'konten' => 'required|min_length[2]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->balasanModel->save([
            'topik_id'   => $topik_id,
            'pembuat_id' => $dosen_id,
            'konten'     => $this->request->getPost('konten'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/dosen/forum/detail/' . $topik_id)->with('success', 'Balasan berhasil dikirim.');
    }

    public function deleteTopik($id)
    {
        $dosen_id = session()->get('id');
        $topik = $this->topikModel->find($id);

        if (!$topik || $topik['pembuat_id'] != $dosen_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        // Hapus semua replies terlebih dahulu
        $this->balasanModel->where('topik_id', $id)->delete();
        $this->topikModel->delete($id);

        return redirect()->to('/dosen/forum?kelas_id=' . $topik['kelas_id'])->with('success', 'Topik diskusi berhasil dihapus.');
    }
}
