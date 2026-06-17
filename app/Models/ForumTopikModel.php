<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumTopikModel extends Model
{
    protected $table         = 'forum_topik';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['kelas_id', 'pembuat_id', 'judul', 'konten', 'created_at'];
    protected $skipValidation = true;

    public function getByKelas($kelas_id)
    {
        return $this->select('forum_topik.*, users.name as nama_pembuat, users.role as role_pembuat')
                    ->join('users', 'users.id = forum_topik.pembuat_id')
                    ->where('kelas_id', $kelas_id)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
}
