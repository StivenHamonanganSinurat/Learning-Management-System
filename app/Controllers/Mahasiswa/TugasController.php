<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\TugasSubmitModel;

class TugasController extends BaseController
{
    protected $submitModel;

    public function __construct()
    {
        $this->submitModel = new TugasSubmitModel();
    }

    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        $tugas = $db->table('tugas')
            ->select('tugas.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester,
                      tugas_submit.id as submit_id, tugas_submit.file_path as file_submit,
                      tugas_submit.nilai, tugas_submit.feedback, tugas_submit.submitted_at')
            ->join('kelas', 'kelas.id = tugas.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = tugas.kelas_id')
            ->join('tugas_submit', 'tugas_submit.tugas_id = tugas.id AND tugas_submit.mahasiswa_id = ' . $mahasiswaId, 'left')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->orderBy('tugas.deadline', 'ASC')
            ->get()->getResultArray();

        return view('mahasiswa/tugas/index', [
            'title' => 'Tugas Saya',
            'tugas' => $tugas,
        ]);
    }

    public function submit($tugasId)
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        $tugas = $db->table('tugas')->where('id', $tugasId)->get()->getRowArray();
        if (!$tugas) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'Tugas tidak ditemukan.');
        }

        // Cek sudah submit atau belum
        $existing = $this->submitModel->where('tugas_id', $tugasId)->where('mahasiswa_id', $mahasiswaId)->first();
        if ($existing) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        // Cek deadline
        if (strtotime($tugas['deadline']) < time()) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'Batas waktu pengumpulan sudah lewat.');
        }

        $file = $this->request->getFile('file_tugas');
        if (!$file || !$file->isValid()) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'File tidak valid. Silakan upload ulang.');
        }

        $allowedExt = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'zip', 'rar'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExt)) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'Ekstensi file tidak diizinkan. Gunakan PDF, Word, PPT, ZIP, atau RAR.');
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return redirect()->to('mahasiswa/tugas')->with('error', 'Ukuran file maksimal 10MB.');
        }

        $newName = 'tugas_' . $tugasId . '_' . $mahasiswaId . '_' . time() . '.' . $ext;
        $file->move(FCPATH . 'uploads/tugas', $newName);

        $this->submitModel->insert([
            'tugas_id'     => $tugasId,
            'mahasiswa_id' => $mahasiswaId,
            'file_path'    => 'uploads/tugas/' . $newName,
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        // Award 15 points for submitting a task
        \App\Helpers\GamifikasiHelper::tambahPoin($mahasiswaId, 15);

        return redirect()->to('mahasiswa/tugas')->with('success', 'Tugas berhasil dikumpulkan! Anda mendapatkan +15 Poin.');
    }
}
