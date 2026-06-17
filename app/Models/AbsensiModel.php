<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table         = 'absensi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'tanggal', 'pertemuan_ke', 'created_at'];
    protected $skipValidation = true;

    public function getByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)->orderBy('pertemuan_ke', 'ASC')->findAll();
    }
}
