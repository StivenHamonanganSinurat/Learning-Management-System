<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumTables extends Migration
{
    public function up()
    {
        // forum_topik
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'    => ['type' => 'INT'],
            'pembuat_id'  => ['type' => 'INT'],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => '255'],
            'konten'      => ['type' => 'TEXT'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembuat_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('forum_topik');

        // forum_balasan
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'topik_id'    => ['type' => 'INT'],
            'pembuat_id'  => ['type' => 'INT'],
            'konten'      => ['type' => 'TEXT'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('topik_id', 'forum_topik', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('pembuat_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('forum_balasan');
    }

    public function down()
    {
        $this->forge->dropTable('forum_balasan', true);
        $this->forge->dropTable('forum_topik', true);
    }
}
