<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MataKuliahModel;
use App\Models\ProdiModel;
use App\Models\UserModel;

class MataKuliahController extends BaseController
{
    protected $mkModel;
    protected $prodiModel;
    protected $userModel;

    public function __construct()
    {
        $this->mkModel = new MataKuliahModel();
        $this->prodiModel = new ProdiModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Mata Kuliah',
            'matakuliah' => $this->mkModel->getAllMataKuliah()
        ];
        return view('admin/matakuliah/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Mata Kuliah',
            'prodi' => $this->prodiModel->findAll(),
            'dosen' => $this->userModel->where('role', 'dosen')->findAll()
        ];
        return view('admin/matakuliah/create', $data);
    }

    public function store()
    {
        $rules = [
            'kode_mk'  => 'required|is_unique[mata_kuliah.kode_mk]',
            'nama'     => 'required|min_length[3]',
            'sks'      => 'required|numeric|greater_than[0]',
            'prodi_id' => 'required|numeric',
            'dosen_id' => 'required|numeric',
            'semester' => 'required|numeric|greater_than[0]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->mkModel->save([
            'kode_mk'  => $this->request->getPost('kode_mk'),
            'nama'     => $this->request->getPost('nama'),
            'sks'      => $this->request->getPost('sks'),
            'prodi_id' => $this->request->getPost('prodi_id'),
            'dosen_id' => $this->request->getPost('dosen_id'),
            'semester' => $this->request->getPost('semester')
        ]);

        return redirect()->to('/admin/matakuliah')->with('success', 'Mata Kuliah berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Mata Kuliah',
            'mk'    => $this->mkModel->find($id),
            'prodi' => $this->prodiModel->findAll(),
            'dosen' => $this->userModel->where('role', 'dosen')->findAll()
        ];

        if (empty($data['mk'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata Kuliah tidak ditemukan');
        }

        return view('admin/matakuliah/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'kode_mk'  => "required|is_unique[mata_kuliah.kode_mk,id,{$id}]",
            'nama'     => 'required|min_length[3]',
            'sks'      => 'required|numeric|greater_than[0]',
            'prodi_id' => 'required|numeric',
            'dosen_id' => 'required|numeric',
            'semester' => 'required|numeric|greater_than[0]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->mkModel->save([
            'id'       => $id,
            'kode_mk'  => $this->request->getPost('kode_mk'),
            'nama'     => $this->request->getPost('nama'),
            'sks'      => $this->request->getPost('sks'),
            'prodi_id' => $this->request->getPost('prodi_id'),
            'dosen_id' => $this->request->getPost('dosen_id'),
            'semester' => $this->request->getPost('semester')
        ]);

        return redirect()->to('/admin/matakuliah')->with('success', 'Mata Kuliah berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->mkModel->delete($id);
        return redirect()->to('/admin/matakuliah')->with('success', 'Mata Kuliah berhasil dihapus.');
    }
}
