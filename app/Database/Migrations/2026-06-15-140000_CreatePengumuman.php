<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePengumuman extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'    => ['type' => 'INT'],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => '255'],
            'isi'         => ['type' => 'TEXT'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('pengumuman');
    }

    public function down()
    {
        $this->forge->dropTable('pengumuman', true);
    }
}
