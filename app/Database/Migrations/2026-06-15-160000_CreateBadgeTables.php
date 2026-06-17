<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBadgeTables extends Migration
{
    public function up()
    {
        // badges
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'nama'      => ['type' => 'VARCHAR', 'constraint' => '100'],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
            'icon'      => ['type' => 'VARCHAR', 'constraint' => '50', 'default' => 'fa-trophy'],
            'tipe'      => ['type' => 'VARCHAR', 'constraint' => '30'],
            'threshold' => ['type' => 'INT', 'default' => 1],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('badges');

        // user_badges
        $this->forge->addField([
            'id'        => ['type' => 'INT', 'auto_increment' => true],
            'user_id'   => ['type' => 'INT'],
            'badge_id'  => ['type' => 'INT'],
            'earned_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('badge_id', 'badges', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_badges');
    }

    public function down()
    {
        $this->forge->dropTable('user_badges', true);
        $this->forge->dropTable('badges', true);
    }
}
