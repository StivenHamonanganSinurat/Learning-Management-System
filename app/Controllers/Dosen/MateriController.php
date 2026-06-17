<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MateriModel;

class MateriController extends BaseController
{
    protected $materiModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->materiModel = new MateriModel();
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

        // Ambil semua materi dari kelas-kelas tersebut
        $materi = [];
        if (!empty($kelas_ids)) {
            $materi = $this->materiModel->select('materi.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
                                        ->join('kelas', 'kelas.id = materi.kelas_id')
                                        ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                        ->whereIn('materi.kelas_id', $kelas_ids)
                                        ->orderBy('materi.created_at', 'DESC')
                                        ->findAll();
        }

        $data = [
            'title'  => 'Manajemen Materi Kuliah',
            'materi' => $materi,
            'kelas'  => $kelas
        ];

        return view('dosen/materi/index', $data);
    }

    public function store($kelas_id = null)
    {
        $dosen_id = session()->get('id');

        // Jika dikirim dari global form, ambil kelas_id dari POST
        if ($kelas_id === null) {
            $kelas_id = $this->request->getPost('kelas_id');
        }

        if (empty($kelas_id)) {
            return redirect()->back()->with('error', 'Kelas belum dipilih.');
        }

        // Validasi akses dosen ke kelas ini
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul'     => 'required|min_length[3]',
            'tipe'      => 'required|in_list[file,video,artikel]',
            'file_materi' => 'uploaded[file_materi]|max_size[file_materi,5120]|ext_in[file_materi,pdf,doc,docx,ppt,pptx,mp4]' // max 5MB
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('file_materi');
        if ($file->isValid() && ! $file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/materi', $newName);

            // Get urutan terbesar
            $lastMateri = $this->materiModel->where('kelas_id', $kelas_id)->orderBy('urutan', 'DESC')->first();
            $urutan = $lastMateri ? $lastMateri['urutan'] + 1 : 1;

            $this->materiModel->save([
                'kelas_id'   => $kelas_id,
                'judul'      => $this->request->getPost('judul'),
                'deskripsi'  => $this->request->getPost('deskripsi'),
                'tipe'       => $this->request->getPost('tipe'),
                'file_path'  => 'uploads/materi/' . $newName,
                'urutan'     => $urutan,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with('success', 'Materi berhasil diunggah.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah file.');
    }

    public function delete($id)
    {
        $materi = $this->materiModel->find($id);
        if ($materi) {
            // Hapus file fisik
            if (file_exists($materi['file_path'])) {
                unlink($materi['file_path']);
            }
            $this->materiModel->delete($id);
            return redirect()->back()->with('success', 'Materi berhasil dihapus.');
        }
        return redirect()->back()->with('error', 'Materi tidak ditemukan.');
    }

    public function update($id)
    {
        $materi = $this->materiModel->find($id);
        if (!$materi) {
            return redirect()->back()->with('error', 'Materi tidak ditemukan.');
        }

        $dosen_id = session()->get('id');
        // Validasi akses dosen ke kelas ini
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $materi['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'judul'     => 'required|min_length[3]',
            'tipe'      => 'required|in_list[file,video,artikel]',
            'file_materi' => 'permit_empty|max_size[file_materi,5120]|ext_in[file_materi,pdf,doc,docx,ppt,pptx,mp4]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'judul'     => $this->request->getPost('judul'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'tipe'      => $this->request->getPost('tipe')
        ];

        $file = $this->request->getFile('file_materi');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Hapus file lama jika ada
            if (!empty($materi['file_path']) && file_exists($materi['file_path'])) {
                unlink($materi['file_path']);
            }

            $newName = $file->getRandomName();
            $file->move('uploads/materi', $newName);
            $data['file_path'] = 'uploads/materi/' . $newName;
        }

        $this->materiModel->update($id, $data);

        return redirect()->back()->with('success', 'Materi berhasil diperbarui.');
    }
}
