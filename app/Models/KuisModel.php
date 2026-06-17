<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisModel extends Model
{
    protected $table         = 'kuis';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'tipe', 'judul', 'durasi_menit', 'deadline', 'max_attempt', 'created_at'];
    protected $skipValidation = true;

    public function getByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)->orderBy('deadline', 'ASC')->findAll();
    }
}
