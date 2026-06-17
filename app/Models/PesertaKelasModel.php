<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaKelasModel extends Model
{
    protected $table            = 'kelas_mahasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kelas_id', 'mahasiswa_id'];

    protected $skipValidation   = true;

    // Get all students enrolled in a class
    public function getPeserta($kelas_id)
    {
        return $this->select('kelas_mahasiswa.id as enroll_id, users.name, users.nim_nidn, users.email')
                    ->join('users', 'users.id = kelas_mahasiswa.mahasiswa_id')
                    ->where('kelas_id', $kelas_id)
                    ->findAll();
    }
}
