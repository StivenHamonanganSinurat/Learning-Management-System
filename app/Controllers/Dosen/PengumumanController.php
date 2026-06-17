<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\PengumumanModel;

class PengumumanController extends BaseController
{
    protected $pengumumanModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->pengumumanModel = new PengumumanModel();
        $this->kelasModel      = new KelasModel();
    }

    public function index()
    {
        $dosen_id = session()->get('id');

        // Ambil semua kelas yang diajar dosen ini
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('mata_kuliah.dosen_id', $dosen_id)
                                  ->findAll();

        $kelas_ids = array_column($kelas, 'id');

        $pengumuman = [];
        if (!empty($kelas_ids)) {
            $pengumuman = $this->pengumumanModel->select('pengumuman.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
                                                ->join('kelas', 'kelas.id = pengumuman.kelas_id')
                                                ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                                ->whereIn('pengumuman.kelas_id', $kelas_ids)
                                                ->orderBy('pengumuman.created_at', 'DESC')
                                                ->findAll();
        }

        return view('dosen/pengumuman/index', [
            'title'      => 'Manajemen Pengumuman Kelas',
            'pengumuman' => $pengumuman,
            'kelas'      => $kelas,
        ]);
    }

    public function store($kelas_id = null)
    {
        $dosen_id = session()->get('id');
        if ($kelas_id === null) {
            $kelas_id = $this->request->getPost('kelas_id');
        }

        if (empty($kelas_id)) {
            return redirect()->back()->with('error', 'Kelas belum dipilih.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'isi'   => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pengumumanModel->save([
            'kelas_id'   => $kelas_id,
            'judul'      => $this->request->getPost('judul'),
            'isi'        => $this->request->getPost('isi'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil dipublikasikan.');
    }

    public function update($id)
    {
        $dosen_id = session()->get('id');
        $pengumuman = $this->pengumumanModel->find($id);

        if (!$pengumuman) {
            return redirect()->back()->with('error', 'Pengumuman tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $pengumuman['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul' => 'required|min_length[3]|max_length[255]',
            'isi'   => 'required|min_length[5]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->pengumumanModel->update($id, [
            'judul' => $this->request->getPost('judul'),
            'isi'   => $this->request->getPost('isi')
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function delete($id)
    {
        $dosen_id = session()->get('id');
        $pengumuman = $this->pengumumanModel->find($id);

        if (!$pengumuman) {
            return redirect()->back()->with('error', 'Pengumuman tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $pengumuman['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $this->pengumumanModel->delete($id);
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
