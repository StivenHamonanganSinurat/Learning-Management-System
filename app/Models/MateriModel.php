<?php

namespace App\Models;

use CodeIgniter\Model;

class MateriModel extends Model
{
    protected $table            = 'materi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['kelas_id', 'judul', 'deskripsi', 'tipe', 'file_path', 'urutan', 'created_at'];

    protected $useTimestamps    = false; 
    protected $skipValidation   = true;

    // Get materi by kelas
    public function getMateriByKelas($kelas_id)
    {
        return $this->where('kelas_id', $kelas_id)->orderBy('urutan', 'ASC')->findAll();
    }
}
