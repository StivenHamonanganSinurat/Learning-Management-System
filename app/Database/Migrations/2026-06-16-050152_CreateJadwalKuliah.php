<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalKuliah extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'     => ['type' => 'INT'],
            'pertemuan_ke' => ['type' => 'INT'],
            'tanggal'      => ['type' => 'DATE'],
            'jam_mulai'    => ['type' => 'TIME'],
            'jam_selesai'  => ['type' => 'TIME'],
            'ruangan'      => ['type' => 'VARCHAR', 'constraint' => '100'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jadwal_kuliah');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal_kuliah', true);
    }
}
