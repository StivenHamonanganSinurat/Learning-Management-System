<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKuisAttemptJawaban extends Migration
{
    public function up()
    {
        // kuis_attempt
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'kuis_id'      => ['type' => 'INT'],
            'mahasiswa_id' => ['type' => 'INT'],
            'nilai'        => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'started_at'   => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'status'       => ['type' => 'VARCHAR', 'constraint' => '20', 'default' => 'in_progress'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kuis_id', 'kuis', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kuis_attempt');

        // jawaban_kuis
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'attempt_id' => ['type' => 'INT'],
            'soal_id'    => ['type' => 'INT'],
            'jawaban'    => ['type' => 'CHAR', 'constraint' => '1', 'null' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => '10', 'default' => 'salah'],
            'poin'       => ['type' => 'INT', 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('attempt_id', 'kuis_attempt', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('soal_id', 'soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jawaban_kuis');
    }

    public function down()
    {
        $this->forge->dropTable('jawaban_kuis', true);
        $this->forge->dropTable('kuis_attempt', true);
    }
}
