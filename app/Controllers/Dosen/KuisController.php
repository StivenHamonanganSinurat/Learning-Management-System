<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\KuisModel;
use App\Models\SoalModel;

class KuisController extends BaseController
{
    protected $kuisModel;
    protected $soalModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->kuisModel = new KuisModel();
        $this->soalModel = new SoalModel();
        $this->kelasModel = new KelasModel();
    }

    public function index()
    {
        $dosen_id = session()->get('id');
        $tipe = $this->request->getGet('tipe') ?? 'kuis';
        if (!in_array($tipe, ['kuis', 'uts', 'uas'])) {
            $tipe = 'kuis';
        }

        // Ambil semua kelas yang diajar dosen ini
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('mata_kuliah.dosen_id', $dosen_id)
                                  ->findAll();

        $kelas_ids = array_column($kelas, 'id');

        $kuis = [];
        if (!empty($kelas_ids)) {
            $kuis = $this->kuisModel->select('kuis.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
                                    ->join('kelas', 'kelas.id = kuis.kelas_id')
                                    ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                    ->whereIn('kuis.kelas_id', $kelas_ids)
                                    ->where('kuis.tipe', $tipe)
                                    ->orderBy('kuis.deadline', 'ASC')
                                    ->findAll();
        }

        $titleMap = [
            'kuis' => 'Manajemen Kuis Kuliah',
            'uts' => 'Manajemen Ujian Tengah Semester (UTS)',
            'uas' => 'Manajemen Ujian Akhir Semester (UAS)'
        ];

        $data = [
            'title' => $titleMap[$tipe],
            'kuis'  => $kuis,
            'kelas' => $kelas,
            'tipe'  => $tipe
        ];

        return view('dosen/kuis/index', $data);
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
            'judul'        => 'required|min_length[3]',
            'durasi_menit' => 'required|integer|greater_than[0]',
            'deadline'     => 'required|valid_date[Y-m-d\TH:i]',
            'max_attempt'  => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $tipe = $this->request->getPost('tipe') ?? 'kuis';

        $this->kuisModel->save([
            'kelas_id'     => $kelas_id,
            'tipe'         => $tipe,
            'judul'        => $this->request->getPost('judul'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'deadline'     => $this->request->getPost('deadline'),
            'max_attempt'  => $this->request->getPost('max_attempt'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', ucfirst($tipe) . ' berhasil dibuat.');
    }

    public function update($id)
    {
        $dosen_id = session()->get('id');
        $kuis = $this->kuisModel->find($id);

        if (!$kuis) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul'        => 'required|min_length[3]',
            'durasi_menit' => 'required|integer|greater_than[0]',
            'deadline'     => 'required|valid_date[Y-m-d\TH:i]',
            'max_attempt'  => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kuisModel->update($id, [
            'judul'        => $this->request->getPost('judul'),
            'durasi_menit' => $this->request->getPost('durasi_menit'),
            'deadline'     => $this->request->getPost('deadline'),
            'max_attempt'  => $this->request->getPost('max_attempt'),
        ]);

        return redirect()->back()->with('success', 'Kuis berhasil diperbarui.');
    }

    public function delete($id)
    {
        $kuis = $this->kuisModel->find($id);
        if (!$kuis) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $this->kuisModel->delete($id);
        return redirect()->back()->with('success', 'Kuis berhasil dihapus.');
    }

    public function detail($id)
    {
        $kuis = $this->kuisModel->find($id);
        if (!$kuis) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $soal = $this->soalModel->getByKuis($id);

        $data = [
            'title' => 'Kelola Soal Kuis: ' . $kuis['judul'],
            'kuis'  => $kuis,
            'kelas' => $kelas,
            'soal'  => $soal
        ];

        return view('dosen/kuis/detail', $data);
    }

    public function storeSoal($kuis_id)
    {
        $kuis = $this->kuisModel->find($kuis_id);
        if (!$kuis) {
            return redirect()->back()->with('error', 'Kuis tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'pertanyaan'    => 'required',
            'opsi_a'        => 'required',
            'opsi_b'        => 'required',
            'opsi_c'        => 'required',
            'opsi_d'        => 'required',
            'jawaban_benar' => 'required|in_list[A,B,C,D]',
            'poin'          => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->soalModel->save([
            'kuis_id'       => $kuis_id,
            'pertanyaan'    => $this->request->getPost('pertanyaan'),
            'opsi_a'        => $this->request->getPost('opsi_a'),
            'opsi_b'        => $this->request->getPost('opsi_b'),
            'opsi_c'        => $this->request->getPost('opsi_c'),
            'opsi_d'        => $this->request->getPost('opsi_d'),
            'jawaban_benar' => $this->request->getPost('jawaban_benar'),
            'poin'          => $this->request->getPost('poin')
        ]);

        return redirect()->to('/dosen/kuis/detail/'.$kuis_id)->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function deleteSoal($id)
    {
        $soal = $this->soalModel->find($id);
        if (!$soal) {
            return redirect()->back()->with('error', 'Pertanyaan tidak ditemukan.');
        }

        $kuis = $this->kuisModel->find($soal['kuis_id']);
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $this->soalModel->delete($id);
        return redirect()->back()->with('success', 'Pertanyaan berhasil dihapus.');
    }

    public function updateNilaiAttempt($attempt_id)
    {
        $db = \Config\Database::connect();
        $attempt = $db->table('kuis_attempt')->where('id', $attempt_id)->get()->getRowArray();
        
        if (!$attempt) {
            return redirect()->back()->with('error', 'Percobaan kuis tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        $kuis = $this->kuisModel->find($attempt['kuis_id']);
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kuis['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $nilai = floatval($this->request->getPost('nilai'));
        if ($nilai < 0 || $nilai > 100) {
            return redirect()->back()->with('error', 'Nilai harus di antara 0 dan 100.');
        }

        // Update nilai attempt
        $db->table('kuis_attempt')->where('id', $attempt_id)->update([
            'nilai' => $nilai
        ]);

        return redirect()->to('/dosen/kuis/detail/' . $attempt['kuis_id'])->with('success', 'Nilai percobaan berhasil diperbarui.');
    }
}
