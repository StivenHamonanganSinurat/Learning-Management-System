<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiAkhirModel extends Model
{
    protected $table         = 'nilai_akhir';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'mahasiswa_id', 'nilai_tugas', 'nilai_kuis', 'nilai_uts', 'nilai_uas', 'nilai_akhir', 'grade'];
    protected $skipValidation = true;

    public function getNilaiByKelas($kelas_id)
    {
        return $this->select('nilai_akhir.*, users.name as nama_mahasiswa, users.nim_nidn as nim')
                    ->join('users', 'users.id = nilai_akhir.mahasiswa_id')
                    ->where('nilai_akhir.kelas_id', $kelas_id)
                    ->orderBy('users.nim_nidn', 'ASC')
                    ->findAll();
    }
}
