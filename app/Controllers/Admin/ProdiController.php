<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProdiModel;

class ProdiController extends BaseController
{
    protected $prodiModel;

    public function __construct()
    {
        $this->prodiModel = new ProdiModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Manajemen Program Studi',
            'prodi' => $this->prodiModel->findAll()
        ];
        return view('admin/prodi/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Program Studi'
        ];
        return view('admin/prodi/create', $data);
    }

    public function store()
    {
        $rules = [
            'nama_prodi' => 'required|min_length[3]',
            'kode'       => 'required|is_unique[program_studi.kode]'
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prodiModel->save([
            'nama_prodi' => $this->request->getPost('nama_prodi'),
            'kode'       => $this->request->getPost('kode')
        ]);

        return redirect()->to('/admin/prodi')->with('success', 'Data Program Studi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Program Studi',
            'prodi' => $this->prodiModel->find($id)
        ];

        if (empty($data['prodi'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Prodi tidak ditemukan');
        }

        return view('admin/prodi/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'nama_prodi' => 'required|min_length[3]',
            'kode'       => "required|is_unique[program_studi.kode,id,{$id}]"
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->prodiModel->save([
            'id'         => $id,
            'nama_prodi' => $this->request->getPost('nama_prodi'),
            'kode'       => $this->request->getPost('kode')
        ]);

        return redirect()->to('/admin/prodi')->with('success', 'Data Program Studi berhasil diupdate.');
    }

    public function delete($id)
    {
        $this->prodiModel->delete($id);
        return redirect()->to('/admin/prodi')->with('success', 'Data Program Studi berhasil dihapus.');
    }
}
