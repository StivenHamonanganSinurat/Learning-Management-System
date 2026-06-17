<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasModel extends Model
{
    protected $table         = 'tugas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'judul', 'deskripsi', 'deadline', 'max_nilai', 'created_at'];
    protected $skipValidation = true;

    public function getByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)->orderBy('deadline', 'ASC')->findAll();
    }
}
