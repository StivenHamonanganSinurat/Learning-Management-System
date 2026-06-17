<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MateriModel;
use App\Models\PesertaKelasModel;
use App\Models\TugasModel;
use App\Models\KuisModel;
use App\Models\JadwalKuliahModel;

class KelasController extends BaseController
{
    protected $kelasModel;
    protected $materiModel;
    protected $pesertaModel;
    protected $tugasModel;
    protected $kuisModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->materiModel = new MateriModel();
        $this->pesertaModel = new PesertaKelasModel();
        $this->tugasModel = new TugasModel();
        $this->kuisModel = new KuisModel();
        $this->jadwalModel = new JadwalKuliahModel();
    }

    public function index()
    {
        // View all classes for this dosen (same as dashboard, but dedicated page)
        $dosen_id = session()->get('id');
        
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('mata_kuliah.dosen_id', $dosen_id)
                                  ->findAll();

        $data = [
            'title' => 'Kelas Saya',
            'kelas' => $kelas
        ];
        return view('dosen/kelas/index', $data);
    }

    public function kelola($kelas_id)
    {
        $dosen_id = session()->get('id');

        // Validasi apakah kelas ini benar-benar diajar oleh dosen yang sedang login
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kelas tidak ditemukan atau Anda tidak memiliki akses ke kelas ini.');
        }

        $data = [
            'title'   => 'Kelola Kelas: ' . $kelas['nama_mk'],
            'kelas'   => $kelas,
            'materi'  => $this->materiModel->getMateriByKelas($kelas_id),
            'tugas'   => $this->tugasModel->getByKelas($kelas_id),
            'kuis'    => $this->kuisModel->getByKelas($kelas_id),
            'jadwal'  => $this->jadwalModel->getJadwalByKelas($kelas_id),
            'peserta' => count($this->pesertaModel->where('kelas_id', $kelas_id)->findAll())
        ];

        return view('dosen/kelas/kelola', $data);
    }

    public function storeJadwal($kelas_id)
    {
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->find($kelas_id);

        if (!$kelas || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'ruangan'      => 'required|min_length[2]|max_length[100]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pertemuan = $this->request->getPost('pertemuan_ke');
        $tanggal = $this->request->getPost('tanggal');
        $jam_mulai = $this->request->getPost('jam_mulai');
        $jam_selesai = $this->request->getPost('jam_selesai');
        $ruangan = $this->request->getPost('ruangan');

        // Check if pertemuan already exists in this class
        $exists = $this->jadwalModel->where('kelas_id', $kelas_id)->where('pertemuan_ke', $pertemuan)->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', "Pertemuan ke-{$pertemuan} sudah terdaftar di kelas ini.");
        }

        // Check Room Conflict
        $konflik = $this->jadwalModel->cekKonflikRuangan($ruangan, $tanggal, $jam_mulai, $jam_selesai);
        if ($konflik) {
            return redirect()->back()->withInput()->with('error', "Tabrakan jadwal! Ruangan {$ruangan} sudah digunakan oleh mata kuliah \"{$konflik['nama_mk']}\" pada tanggal {$tanggal} pukul {$konflik['jam_mulai']} - {$konflik['jam_selesai']}.");
        }

        $this->jadwalModel->insert([
            'kelas_id'     => $kelas_id,
            'pertemuan_ke' => $pertemuan,
            'tanggal'      => $tanggal,
            'jam_mulai'    => $jam_mulai,
            'jam_selesai'  => $jam_selesai,
            'ruangan'      => $ruangan,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/dosen/kelas/kelola/'.$kelas_id.'?tab=jadwal')->with('success', 'Jadwal pertemuan berhasil ditambahkan.');
    }

    public function updateJadwal($id)
    {
        $dosen_id = session()->get('id');
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->find($jadwal['kelas_id']);

        if (!$kelas || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $rules = [
            'pertemuan_ke' => 'required|numeric|greater_than[0]',
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'jam_mulai'    => 'required',
            'jam_selesai'  => 'required',
            'ruangan'      => 'required|min_length[2]|max_length[100]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $pertemuan = $this->request->getPost('pertemuan_ke');
        $tanggal = $this->request->getPost('tanggal');
        $jam_mulai = $this->request->getPost('jam_mulai');
        $jam_selesai = $this->request->getPost('jam_selesai');
        $ruangan = $this->request->getPost('ruangan');

        // Check if pertemuan already exists in this class (excluding this schedule)
        $exists = $this->jadwalModel->where('kelas_id', $jadwal['kelas_id'])
                                     ->where('pertemuan_ke', $pertemuan)
                                     ->where('id !=', $id)
                                     ->first();
        if ($exists) {
            return redirect()->back()->withInput()->with('error', "Pertemuan ke-{$pertemuan} sudah terdaftar di kelas ini.");
        }

        // Check Room Conflict
        $konflik = $this->jadwalModel->cekKonflikRuangan($ruangan, $tanggal, $jam_mulai, $jam_selesai, $id);
        if ($konflik) {
            return redirect()->back()->withInput()->with('error', "Tabrakan jadwal! Ruangan {$ruangan} sudah digunakan oleh mata kuliah \"{$konflik['nama_mk']}\" pada tanggal {$tanggal} pukul {$konflik['jam_mulai']} - {$konflik['jam_selesai']}.");
        }

        $this->jadwalModel->update($id, [
            'pertemuan_ke' => $pertemuan,
            'tanggal'      => $tanggal,
            'jam_mulai'    => $jam_mulai,
            'jam_selesai'  => $jam_selesai,
            'ruangan'      => $ruangan
        ]);

        return redirect()->to('/dosen/kelas/kelola/'.$jadwal['kelas_id'].'?tab=jadwal')->with('success', 'Jadwal pertemuan berhasil diperbarui.');
    }

    public function deleteJadwal($id)
    {
        $dosen_id = session()->get('id');
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->find($jadwal['kelas_id']);

        if (!$kelas || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $this->jadwalModel->delete($id);
        return redirect()->to('/dosen/kelas/kelola/'.$jadwal['kelas_id'].'?tab=jadwal')->with('success', 'Jadwal pertemuan berhasil dihapus.');
    }
}
