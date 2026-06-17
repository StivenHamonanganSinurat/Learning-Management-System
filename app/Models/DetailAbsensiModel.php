<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailAbsensiModel extends Model
{
    protected $table         = 'detail_absensi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['absensi_id', 'mahasiswa_id', 'status'];
    protected $skipValidation = true;

    public function getAttendanceDetails($absensi_id)
    {
        return $this->select('detail_absensi.*, users.name as nama_mahasiswa, users.nim_nidn as nim')
                    ->join('users', 'users.id = detail_absensi.mahasiswa_id')
                    ->where('detail_absensi.absensi_id', $absensi_id)
                    ->orderBy('users.nim_nidn', 'ASC')
                    ->findAll();
    }
}
