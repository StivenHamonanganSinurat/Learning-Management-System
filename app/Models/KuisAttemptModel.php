<?php

namespace App\Models;

use CodeIgniter\Model;

class KuisAttemptModel extends Model
{
    protected $table         = 'kuis_attempt';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kuis_id', 'mahasiswa_id', 'nilai', 'started_at', 'completed_at', 'status'];
    protected $skipValidation = true;

    public function getAttemptByMahasiswa($kuis_id, $mahasiswa_id)
    {
        return $this->where('kuis_id', $kuis_id)
                    ->where('mahasiswa_id', $mahasiswa_id)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }

    public function getLastAttempt($kuis_id, $mahasiswa_id)
    {
        return $this->where('kuis_id', $kuis_id)
                    ->where('mahasiswa_id', $mahasiswa_id)
                    ->orderBy('id', 'DESC')
                    ->first();
    }
}
