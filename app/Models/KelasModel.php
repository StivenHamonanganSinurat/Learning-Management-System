<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['mata_kuliah_id', 'tahun_ajaran', 'semester'];

    protected $useTimestamps    = false; // Table doesn't have created_at and updated_at
    protected $skipValidation   = true;

    // Join with mata_kuliah and dosen
    public function getAllKelas()
    {
        return $this->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, users.name as nama_dosen')
                    ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                    ->join('users', 'users.id = mata_kuliah.dosen_id')
                    ->findAll();
    }
}
