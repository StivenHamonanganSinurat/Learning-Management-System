<?php

namespace App\Models;

use CodeIgniter\Model;

class PengumumanModel extends Model
{
    protected $table         = 'pengumuman';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'judul', 'isi', 'created_at'];
    protected $skipValidation = true;

    public function getByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)->orderBy('created_at', 'DESC')->findAll();
    }
}
