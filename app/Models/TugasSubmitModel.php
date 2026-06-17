<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasSubmitModel extends Model
{
    protected $table         = 'tugas_submit';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['tugas_id', 'mahasiswa_id', 'file_path', 'nilai', 'feedback', 'submitted_at'];
    protected $skipValidation = true;

    public function getSubmissions($tugas_id)
    {
        return $this->select('tugas_submit.*, users.name as nama_mahasiswa, users.nim_nidn as nim')
                    ->join('users', 'users.id = tugas_submit.mahasiswa_id')
                    ->where('tugas_submit.tugas_id', $tugas_id)
                    ->orderBy('tugas_submit.submitted_at', 'DESC')
                    ->findAll();
    }
}
