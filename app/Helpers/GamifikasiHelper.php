<?php

namespace App\Helpers;

class GamifikasiHelper
{
    /**
     * Menambahkan poin ke user dan memperbarui levelnya (setiap level butuh 100 poin)
     */
    public static function tambahPoin($userId, $poin)
    {
        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $userId)->get()->getRowArray();
        if (!$user) return;

        $newPoin = $user['poin'] + $poin;
        // Level naik setiap kelipatan 100 poin
        $newLevel = floor($newPoin / 100) + 1;

        $db->table('users')->where('id', $userId)->update([
            'poin'  => $newPoin,
            'level' => $newLevel
        ]);

        // Cek pencapaian badge baru
        self::checkAndAwardBadges($userId);
    }

    /**
     * Mengecek dan memberikan badge baru kepada user berdasarkan statistiknya
     */
    public static function checkAndAwardBadges($userId)
    {
        $db = \Config\Database::connect();

        // 1. Ambil semua badges
        $badges = $db->table('badges')->get()->getResultArray();

        // 2. Ambil badge yang sudah dimiliki user
        $myBadgeIds = array_column(
            $db->table('user_badges')->where('user_id', $userId)->get()->getResultArray(),
            'badge_id'
        );

        // 3. Hitung metrik user
        // Total Tugas yang sudah disubmit
        $totalTugas = $db->table('tugas_submit')->where('mahasiswa_id', $userId)->countAllResults();

        // Total Kehadiran
        $totalHadir = $db->table('detail_absensi')
                         ->where('mahasiswa_id', $userId)
                         ->where('status', 'hadir')
                         ->countAllResults();

        // Cek apakah pernah dapat nilai 100 kuis
        $perfectKuis = $db->table('kuis_attempt')
                          ->where('mahasiswa_id', $userId)
                          ->where('nilai >=', 100.0)
                          ->countAllResults();

        foreach ($badges as $badge) {
            // Jika sudah punya, skip
            if (in_array($badge['id'], $myBadgeIds)) {
                continue;
            }

            $eligible = false;
            switch ($badge['tipe']) {
                case 'tugas':
                    if ($totalTugas >= $badge['threshold']) $eligible = true;
                    break;
                case 'kehadiran':
                    if ($totalHadir >= $badge['threshold']) $eligible = true;
                    break;
                case 'kuis_perfect':
                    if ($perfectKuis >= 1) $eligible = true;
                    break;
            }

            if ($eligible) {
                $db->table('user_badges')->insert([
                    'user_id'   => $userId,
                    'badge_id'  => $badge['id'],
                    'earned_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
