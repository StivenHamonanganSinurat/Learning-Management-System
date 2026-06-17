<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitTables extends Migration
{
    public function up()
    {
        // 1. users
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'email'      => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
            'password'   => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role'       => ['type' => 'VARCHAR', 'constraint' => '20', 'default' => 'mahasiswa'],
            'nim_nidn'   => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'avatar'     => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'poin'       => ['type' => 'INT', 'default' => 0],
            'level'      => ['type' => 'INT', 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');

        // 2. program_studi
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'nama_prodi' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => '10'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('program_studi');

        // 3. mata_kuliah
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'auto_increment' => true],
            'kode_mk'    => ['type' => 'VARCHAR', 'constraint' => '20'],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => '100'],
            'sks'        => ['type' => 'INT'],
            'prodi_id'   => ['type' => 'INT'],
            'dosen_id'   => ['type' => 'INT'],
            'semester'   => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('prodi_id', 'program_studi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dosen_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('mata_kuliah');

        // 4. kelas
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'mata_kuliah_id' => ['type' => 'INT'],
            'tahun_ajaran'   => ['type' => 'VARCHAR', 'constraint' => '20'],
            'semester'       => ['type' => 'VARCHAR', 'constraint' => '10'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mata_kuliah_id', 'mata_kuliah', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kelas');

        // 5. kelas_mahasiswa
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'     => ['type' => 'INT'],
            'mahasiswa_id' => ['type' => 'INT'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kelas_mahasiswa');

        // 6. materi
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'    => ['type' => 'INT'],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => '255'],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'tipe'        => ['type' => 'VARCHAR', 'constraint' => '20', 'default' => 'file'],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'urutan'      => ['type' => 'INT'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('materi');

        // 7. tugas
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'    => ['type' => 'INT'],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => '255'],
            'deskripsi'   => ['type' => 'TEXT', 'null' => true],
            'deadline'    => ['type' => 'DATETIME'],
            'max_nilai'   => ['type' => 'INT', 'default' => 100],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tugas');

        // 8. tugas_submit
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'tugas_id'     => ['type' => 'INT'],
            'mahasiswa_id' => ['type' => 'INT'],
            'file_path'    => ['type' => 'VARCHAR', 'constraint' => '255'],
            'nilai'        => ['type' => 'INT', 'null' => true],
            'feedback'     => ['type' => 'TEXT', 'null' => true],
            'submitted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tugas_id', 'tugas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tugas_submit');

        // 9. kuis
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'     => ['type' => 'INT'],
            'judul'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'durasi_menit' => ['type' => 'INT'],
            'deadline'     => ['type' => 'DATETIME', 'null' => true],
            'max_attempt'  => ['type' => 'INT', 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kuis');

        // 10. soal
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'auto_increment' => true],
            'kuis_id'       => ['type' => 'INT'],
            'pertanyaan'    => ['type' => 'TEXT'],
            'opsi_a'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'opsi_b'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'opsi_c'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'opsi_d'        => ['type' => 'VARCHAR', 'constraint' => '255'],
            'jawaban_benar' => ['type' => 'CHAR', 'constraint' => '1'],
            'poin'          => ['type' => 'INT', 'default' => 10],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kuis_id', 'kuis', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('soal');

        // 11. absensi
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'      => ['type' => 'INT'],
            'tanggal'       => ['type' => 'DATE'],
            'pertemuan_ke'  => ['type' => 'INT'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi');

        // 12. detail_absensi
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'absensi_id'   => ['type' => 'INT'],
            'mahasiswa_id' => ['type' => 'INT'],
            'status'       => ['type' => 'VARCHAR', 'constraint' => '10', 'default' => 'alpha'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('absensi_id', 'absensi', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('detail_absensi');

        // 13. nilai_akhir
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'auto_increment' => true],
            'kelas_id'     => ['type' => 'INT'],
            'mahasiswa_id' => ['type' => 'INT'],
            'nilai_tugas'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'nilai_kuis'   => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'nilai_uts'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'nilai_uas'    => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'nilai_akhir'  => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'grade'        => ['type' => 'VARCHAR', 'constraint' => '2', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mahasiswa_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nilai_akhir');
    }

    public function down()
    {
        $this->forge->dropTable('nilai_akhir', true);
        $this->forge->dropTable('detail_absensi', true);
        $this->forge->dropTable('absensi', true);
        $this->forge->dropTable('soal', true);
        $this->forge->dropTable('kuis', true);
        $this->forge->dropTable('tugas_submit', true);
        $this->forge->dropTable('tugas', true);
        $this->forge->dropTable('materi', true);
        $this->forge->dropTable('kelas_mahasiswa', true);
        $this->forge->dropTable('kelas', true);
        $this->forge->dropTable('mata_kuliah', true);
        $this->forge->dropTable('program_studi', true);
        $this->forge->dropTable('users', true);
    }
}
