<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'name'       => 'Administrator',
            'email'      => 'admin@stikes.ac.id',
            'password'   => password_hash('password123', PASSWORD_DEFAULT),
            'role'       => 'admin',
            'nim_nidn'   => 'ADMIN001',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        // Simple check to avoid duplicate admin
        if ($this->db->table('users')->where('email', 'admin@stikes.ac.id')->countAllResults() == 0) {
            $this->db->table('users')->insert($data);
        }
    }
}
