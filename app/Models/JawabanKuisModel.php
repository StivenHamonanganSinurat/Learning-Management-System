<?php

namespace App\Models;

use CodeIgniter\Model;

class JawabanKuisModel extends Model
{
    protected $table         = 'jawaban_kuis';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['attempt_id', 'soal_id', 'jawaban', 'status', 'poin'];
    protected $skipValidation = true;

    public function getByAttempt($attempt_id)
    {
        return $this->where('attempt_id', $attempt_id)->findAll();
    }
}
