<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalModel extends Model
{
    protected $table         = 'soal';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kuis_id', 'pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'jawaban_benar', 'poin'];
    protected $skipValidation = true;

    public function getByKuis($kuis_id)
    {
        return $this->where('kuis_id', $kuis_id)->findAll();
    }
}
