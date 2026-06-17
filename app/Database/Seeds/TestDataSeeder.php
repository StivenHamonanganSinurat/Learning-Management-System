<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // ============================
        // 1. Users (Admin, Dosen, Mahasiswa)
        // ============================
        $users = [
            [
                'name'       => 'Administrator',
                'email'      => 'admin@lms.test',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'admin',
                'nim_nidn'   => 'ADM001',
                'poin'       => 0,
                'level'      => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Dr. Budi Santoso, M.Kom',
                'email'      => 'dosen@lms.test',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'dosen',
                'nim_nidn'   => '0115068901',
                'poin'       => 0,
                'level'      => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Sarah Napitupulu',
                'email'      => 'mahasiswa@lms.test',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'mahasiswa',
                'nim_nidn'   => '2021001',
                'poin'       => 0,
                'level'      => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Rizky Harahap',
                'email'      => 'mahasiswa2@lms.test',
                'password'   => password_hash('password123', PASSWORD_DEFAULT),
                'role'       => 'mahasiswa',
                'nim_nidn'   => '2021002',
                'poin'       => 0,
                'level'      => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);

        // ============================
        // 2. Program Studi
        // ============================
        $prodi = [
            ['nama_prodi' => 'S1 Keperawatan', 'kode' => 'KEP'],
            ['nama_prodi' => 'D3 Kebidanan', 'kode' => 'KEB'],
            ['nama_prodi' => 'S1 Farmasi', 'kode' => 'FAR'],
        ];
        $this->db->table('program_studi')->insertBatch($prodi);

        // ============================
        // 3. Mata Kuliah
        // ============================
        $mataKuliah = [
            [
                'kode_mk'  => 'KEP101',
                'nama'     => 'Anatomi Dasar',
                'sks'      => 3,
                'prodi_id' => 1,
                'dosen_id' => 2,
                'semester' => 1,
            ],
            [
                'kode_mk'  => 'KEP102',
                'nama'     => 'Fisiologi Manusia',
                'sks'      => 3,
                'prodi_id' => 1,
                'dosen_id' => 2,
                'semester' => 1,
            ],
        ];
        $this->db->table('mata_kuliah')->insertBatch($mataKuliah);

        // ============================
        // 4. Kelas
        // ============================
        $kelas = [
            [
                'mata_kuliah_id' => 1,
                'tahun_ajaran'   => '2025/2026',
                'semester'       => 'genap',
            ],
            [
                'mata_kuliah_id' => 2,
                'tahun_ajaran'   => '2025/2026',
                'semester'       => 'genap',
            ],
        ];
        $this->db->table('kelas')->insertBatch($kelas);

        // ============================
        // 5. Enroll Mahasiswa ke Kelas
        // ============================
        $kelasMhs = [
            ['kelas_id' => 1, 'mahasiswa_id' => 3],
            ['kelas_id' => 1, 'mahasiswa_id' => 4],
            ['kelas_id' => 2, 'mahasiswa_id' => 3],
        ];
        $this->db->table('kelas_mahasiswa')->insertBatch($kelasMhs);

        // ============================
        // 6. Badges
        // ============================
        $badges = [
            ['nama' => 'Rajin Tugas',     'deskripsi' => 'Menyelesaikan 5 tugas',     'icon' => 'fa-edit',   'tipe' => 'tugas',        'threshold' => 5],
            ['nama' => 'Si Rajin',         'deskripsi' => 'Hadir 10 kali',             'icon' => 'fa-check',  'tipe' => 'kehadiran',    'threshold' => 10],
            ['nama' => 'Kuis Master',      'deskripsi' => 'Mendapat nilai sempurna',   'icon' => 'fa-star',   'tipe' => 'kuis_perfect', 'threshold' => 1],
            ['nama' => 'Pejuang Tugas',    'deskripsi' => 'Menyelesaikan 15 tugas',    'icon' => 'fa-trophy', 'tipe' => 'tugas',        'threshold' => 15],
            ['nama' => 'Absen Sempurna',   'deskripsi' => 'Hadir 30 kali',             'icon' => 'fa-medal',  'tipe' => 'kehadiran',    'threshold' => 30],
        ];
        $this->db->table('badges')->insertBatch($badges);

        // ============================
        // 7. Jadwal Kuliah Sample
        // ============================
        $jadwal = [
            [
                'kelas_id'     => 1,
                'pertemuan_ke' => 1,
                'tanggal'      => date('Y-m-d', strtotime('+1 day')),
                'jam_mulai'    => '08:00:00',
                'jam_selesai'  => '10:00:00',
                'ruangan'      => 'Ruang 101',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kelas_id'     => 1,
                'pertemuan_ke' => 2,
                'tanggal'      => date('Y-m-d', strtotime('+8 days')),
                'jam_mulai'    => '08:00:00',
                'jam_selesai'  => '10:00:00',
                'ruangan'      => 'Ruang 101',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'kelas_id'     => 2,
                'pertemuan_ke' => 1,
                'tanggal'      => date('Y-m-d', strtotime('+2 days')),
                'jam_mulai'    => '10:00:00',
                'jam_selesai'  => '12:00:00',
                'ruangan'      => 'Ruang 202',
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('jadwal_kuliah')->insertBatch($jadwal);

        echo "✅ Test data seeded successfully!\n";
        echo "📧 Login accounts:\n";
        echo "   Admin:     admin@lms.test / password123\n";
        echo "   Dosen:     dosen@lms.test / password123\n";
        echo "   Mahasiswa: mahasiswa@lms.test / password123\n";
    }
}
