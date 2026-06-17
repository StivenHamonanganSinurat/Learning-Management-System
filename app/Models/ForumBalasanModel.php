<?php

namespace App\Models;

use CodeIgniter\Model;

class ForumBalasanModel extends Model
{
    protected $table         = 'forum_balasan';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['topik_id', 'pembuat_id', 'konten', 'created_at'];
    protected $skipValidation = true;

    public function getByTopik($topik_id)
    {
        return $this->select('forum_balasan.*, users.name as nama_pembuat, users.role as role_pembuat, users.avatar')
                    ->join('users', 'users.id = forum_balasan.pembuat_id')
                    ->where('topik_id', $topik_id)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
