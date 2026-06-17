<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;

class LeaderboardController extends BaseController
{
    public function index()
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // 1. Ambil 10 Mahasiswa dengan poin tertinggi (Global Leaderboard)
        $leaderboard = $db->table('users')
            ->where('role', 'mahasiswa')
            ->orderBy('poin', 'DESC')
            ->orderBy('name', 'ASC')
            ->limit(10)
            ->get()->getResultArray();

        // 2. Ambil peringkat mahasiswa yang sedang login saat ini
        // Kita hitung jumlah user dengan poin lebih tinggi + 1
        $myScore = $db->table('users')->select('poin')->where('id', $mahasiswaId)->get()->getRowArray();
        $myPoin = $myScore ? intval($myScore['poin']) : 0;

        $myRank = $db->table('users')
            ->where('role', 'mahasiswa')
            ->where('poin >', $myPoin)
            ->countAllResults() + 1;

        return view('mahasiswa/leaderboard/index', [
            'title'       => 'Leaderboard Poin Mahasiswa',
            'leaderboard' => $leaderboard,
            'myPoin'      => $myPoin,
            'myRank'      => $myRank,
        ]);
    }
}
