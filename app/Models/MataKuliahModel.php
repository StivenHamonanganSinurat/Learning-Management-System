<?php

namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table            = 'mata_kuliah';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kode_mk', 'nama', 'sks', 'prodi_id', 'dosen_id', 'semester'];

    // Validation handled in Controller
    protected $skipValidation       = true;

    // Join with prodi and dosen for easy fetching
    public function getAllMataKuliah()
    {
        return $this->select('mata_kuliah.*, program_studi.nama_prodi, users.name as nama_dosen')
                    ->join('program_studi', 'program_studi.id = mata_kuliah.prodi_id')
                    ->join('users', 'users.id = mata_kuliah.dosen_id')
                    ->findAll();
    }
}
