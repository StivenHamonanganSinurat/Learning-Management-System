<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\TugasModel;
use App\Models\TugasSubmitModel;

class TugasController extends BaseController
{
    protected $tugasModel;
    protected $tugasSubmitModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->tugasModel = new TugasModel();
        $this->tugasSubmitModel = new TugasSubmitModel();
        $this->kelasModel = new KelasModel();
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

        $tugas = [];
        if (!empty($kelas_ids)) {
            $tugas = $this->tugasModel->select('tugas.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
                                      ->join('kelas', 'kelas.id = tugas.kelas_id')
                                      ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                      ->whereIn('tugas.kelas_id', $kelas_ids)
                                      ->orderBy('tugas.deadline', 'ASC')
                                      ->findAll();
        }

        $data = [
            'title' => 'Manajemen Tugas Kuliah',
            'tugas' => $tugas,
            'kelas' => $kelas
        ];

        return view('dosen/tugas/index', $data);
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
            'judul'     => 'required|min_length[3]',
            'deskripsi' => 'permit_empty',
            'deadline'  => 'required|valid_date[Y-m-d\TH:i]',
            'max_nilai' => 'required|integer|greater_than[0]|less_than_equal_to[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tugasModel->save([
            'kelas_id'   => $kelas_id,
            'judul'      => $this->request->getPost('judul'),
            'deskripsi'  => $this->request->getPost('deskripsi'),
            'deadline'   => $this->request->getPost('deadline'),
            'max_nilai'  => $this->request->getPost('max_nilai'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Tugas berhasil ditambahkan.');
    }

    public function delete($id)
    {
        $tugas = $this->tugasModel->find($id);
        if (!$tugas) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        // Validasi dosen
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $tugas['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $this->tugasModel->delete($id);
        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function detail($id)
    {
        $tugas = $this->tugasModel->find($id);
        if (!$tugas) {
            return redirect()->back()->with('error', 'Tugas tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $tugas['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $submissions = $this->tugasSubmitModel->getSubmissions($id);

        $data = [
            'title'       => 'Detail Tugas: ' . $tugas['judul'],
            'tugas'       => $tugas,
            'kelas'       => $kelas,
            'submissions' => $submissions
        ];

        return view('dosen/tugas/detail', $data);
    }

    public function nilai($submit_id)
    {
        $submission = $this->tugasSubmitModel->find($submit_id);
        if (!$submission) {
            return redirect()->back()->with('error', 'Pengumpulan tugas tidak ditemukan.');
        }

        $tugas = $this->tugasModel->find($submission['tugas_id']);
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $tugas['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'nilai'    => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[' . $tugas['max_nilai'] . ']',
            'feedback' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->tugasSubmitModel->update($submit_id, [
            'nilai'    => $this->request->getPost('nilai'),
            'feedback' => $this->request->getPost('feedback')
        ]);

        return redirect()->to('/dosen/tugas/detail/'.$tugas['id'])->with('success', 'Nilai berhasil disimpan.');
    }
}
