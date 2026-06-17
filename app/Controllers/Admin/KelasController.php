<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MataKuliahModel;
use App\Models\PesertaKelasModel;
use App\Models\UserModel;
use App\Models\JadwalKuliahModel;

class KelasController extends BaseController
{
    protected $kelasModel;
    protected $mkModel;
    protected $pesertaModel;
    protected $userModel;
    protected $jadwalModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->mkModel = new MataKuliahModel();
        $this->pesertaModel = new PesertaKelasModel();
        $this->userModel = new UserModel();
        $this->jadwalModel = new JadwalKuliahModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas',
            'kelas' => $this->kelasModel->getAllKelas()
        ];
        return view('admin/kelas/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas',
            'matakuliah' => $this->mkModel->getAllMataKuliah()
        ];
        return view('admin/kelas/create', $data);
    }

    public function store()
    {
        $rules = [
            'mata_kuliah_id' => 'required|numeric',
            'tahun_ajaran'   => 'required|min_length[4]',
            'semester'       => 'required|in_list[ganjil,genap]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->save([
            'mata_kuliah_id' => $this->request->getPost('mata_kuliah_id'),
            'tahun_ajaran'   => $this->request->getPost('tahun_ajaran'),
            'semester'       => $this->request->getPost('semester')
        ]);

        return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title'      => 'Edit Kelas',
            'kelas'      => $this->kelasModel->find($id),
            'matakuliah' => $this->mkModel->getAllMataKuliah()
        ];

        if (empty($data['kelas'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kelas tidak ditemukan');
        }

        return view('admin/kelas/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'mata_kuliah_id' => 'required|numeric',
            'tahun_ajaran'   => 'required|min_length[4]',
            'semester'       => 'required|in_list[ganjil,genap]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->kelasModel->save([
            'id'             => $id,
            'mata_kuliah_id' => $this->request->getPost('mata_kuliah_id'),
            'tahun_ajaran'   => $this->request->getPost('tahun_ajaran'),
            'semester'       => $this->request->getPost('semester')
        ]);

        return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil diupdate.');
    }

    public function delete($id)
    {
        // Delete all peserta linked to this class first
        $this->pesertaModel->where('kelas_id', $id)->delete();
        // Delete the class
        $this->kelasModel->delete($id);
        return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil dihapus.');
    }

    // --- PESERTA KELAS (ENROLL) --- //

    public function peserta($kelas_id)
    {
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->find($kelas_id);

        if (empty($kelas)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kelas tidak ditemukan');
        }

        $data = [
            'title'     => 'Peserta Kelas: ' . $kelas['nama_mk'] . ' (' . $kelas['tahun_ajaran'] . ')',
            'kelas'     => $kelas,
            'peserta'   => $this->pesertaModel->getPeserta($kelas_id),
            'mahasiswa' => $this->userModel->where('role', 'mahasiswa')->findAll()
        ];
        return view('admin/kelas/peserta', $data);
    }

    public function addPeserta($kelas_id)
    {
        $mahasiswa_id = $this->request->getPost('mahasiswa_id');

        // Check if already enrolled
        $exists = $this->pesertaModel->where('kelas_id', $kelas_id)
                                     ->where('mahasiswa_id', $mahasiswa_id)
                                     ->first();
        if ($exists) {
            return redirect()->back()->with('error', 'Mahasiswa tersebut sudah ada di kelas ini.');
        }

        $this->pesertaModel->insert([
            'kelas_id'     => $kelas_id,
            'mahasiswa_id' => $mahasiswa_id
        ]);

        return redirect()->back()->with('success', 'Mahasiswa berhasil ditambahkan ke kelas.');
    }

    public function removePeserta($enroll_id)
    {
        $this->pesertaModel->delete($enroll_id);
        return redirect()->back()->with('success', 'Mahasiswa berhasil dikeluarkan dari kelas.');
    }

    // --- KELOLA JADWAL KELAS --- //

    public function jadwal($kelas_id)
    {
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->find($kelas_id);

        if (empty($kelas)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Kelas tidak ditemukan');
        }

        $data = [
            'title'   => 'Jadwal Kuliah: ' . $kelas['nama_mk'] . ' (' . $kelas['tahun_ajaran'] . ')',
            'kelas'   => $kelas,
            'jadwal'  => $this->jadwalModel->getJadwalByKelas($kelas_id)
        ];
        return view('admin/kelas/jadwal', $data);
    }

    public function storeJadwal($kelas_id)
    {
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

        return redirect()->back()->with('success', 'Jadwal pertemuan berhasil ditambahkan.');
    }

    public function updateJadwal($id)
    {
        $jadwal = $this->jadwalModel->find($id);
        if (!$jadwal) {
            return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
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

        return redirect()->back()->with('success', 'Jadwal pertemuan berhasil diperbarui.');
    }

    public function deleteJadwal($id)
    {
        $this->jadwalModel->delete($id);
        return redirect()->back()->with('success', 'Jadwal pertemuan berhasil dihapus.');
    }
}
