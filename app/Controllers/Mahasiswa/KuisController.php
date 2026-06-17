<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use App\Models\KuisAttemptModel;
use App\Models\JawabanKuisModel;
use App\Models\SoalModel;

class KuisController extends BaseController
{
    protected $attemptModel;
    protected $jawabanModel;
    protected $soalModel;

    public function __construct()
    {
        $this->attemptModel = new KuisAttemptModel();
        $this->jawabanModel = new JawabanKuisModel();
        $this->soalModel    = new SoalModel();
    }

    public function index()
    {
        $mahasiswaId = session()->get('id');
        $tipe = $this->request->getGet('tipe') ?? 'kuis';
        if (!in_array($tipe, ['kuis', 'uts', 'uas'])) {
            $tipe = 'kuis';
        }
        $db = \Config\Database::connect();

        $kuis = $db->table('kuis')
            ->select('kuis.*, mata_kuliah.nama as nama_mk, kelas.tahun_ajaran, kelas.semester')
            ->join('kelas', 'kelas.id = kuis.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = kuis.kelas_id')
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->where('kuis.tipe', $tipe)
            ->orderBy('kuis.deadline', 'ASC')
            ->get()->getResultArray();

        // Tambahkan info attempt per kuis
        foreach ($kuis as &$k) {
            $attempts = $this->attemptModel
                ->where('kuis_id', $k['id'])
                ->where('mahasiswa_id', $mahasiswaId)
                ->findAll();
            $k['jumlah_attempt'] = count($attempts);
            $k['nilai_terbaik']  = $attempts ? max(array_column($attempts, 'nilai')) : null;
            $k['bisa_ikut']      = (count($attempts) < $k['max_attempt']) && (strtotime($k['deadline']) > time());
        }
        unset($k);

        $titleMap = [
            'kuis' => 'Kuis Saya',
            'uts' => 'Ujian Tengah Semester (UTS) Saya',
            'uas' => 'Ujian Akhir Semester (UAS) Saya'
        ];

        return view('mahasiswa/kuis/index', [
            'title' => $titleMap[$tipe],
            'kuis'  => $kuis,
            'tipe'  => $tipe
        ]);
    }

    public function mulai($kuisId)
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        $kuis = $db->table('kuis')
            ->select('kuis.*, mata_kuliah.nama as nama_mk')
            ->join('kelas', 'kelas.id = kuis.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('kelas_mahasiswa', 'kelas_mahasiswa.kelas_id = kuis.kelas_id')
            ->where('kuis.id', $kuisId)
            ->where('kelas_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->get()->getRowArray();

        if (!$kuis) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Kuis tidak ditemukan atau Anda tidak terdaftar di kelas ini.');
        }
        if (strtotime($kuis['deadline']) < time()) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Waktu kuis sudah habis.');
        }

        $attempts = $this->attemptModel->where('kuis_id', $kuisId)->where('mahasiswa_id', $mahasiswaId)->findAll();
        if (count($attempts) >= $kuis['max_attempt']) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Anda sudah melebihi batas percobaan kuis ini.');
        }

        // Buat attempt baru
        $attemptId = $this->attemptModel->insert([
            'kuis_id'      => $kuisId,
            'mahasiswa_id' => $mahasiswaId,
            'nilai'        => 0,
            'started_at'   => date('Y-m-d H:i:s'),
        ]);

        $soal = $this->soalModel->where('kuis_id', $kuisId)->findAll();

        return view('mahasiswa/kuis/kerjakan', [
            'title'     => 'Kerjakan Kuis: ' . $kuis['judul'],
            'kuis'      => $kuis,
            'soal'      => $soal,
            'attemptId' => $attemptId,
        ]);
    }

    public function submit($kuisId)
    {
        $mahasiswaId = session()->get('id');
        $attemptId   = $this->request->getPost('attempt_id');

        $attempt = $this->attemptModel->find($attemptId);
        if (!$attempt || $attempt['mahasiswa_id'] != $mahasiswaId) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Sesi kuis tidak valid.');
        }

        // Sudah selesai sebelumnya
        if ($attempt['completed_at']) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Kuis ini sudah selesai dikerjakan.');
        }

        $soal    = $this->soalModel->where('kuis_id', $kuisId)->findAll();
        $jawaban = $this->request->getPost('jawaban') ?? [];

        $benar = 0;
        foreach ($soal as $s) {
            $pilihanMahasiswa = $jawaban[$s['id']] ?? null;
            $isCorrect = ($pilihanMahasiswa && strtoupper($pilihanMahasiswa) === strtoupper($s['jawaban_benar']));
            $this->jawabanModel->insert([
                'attempt_id' => $attemptId,
                'soal_id'    => $s['id'],
                'jawaban'    => $pilihanMahasiswa ?? '',
                'status'     => $isCorrect ? 'benar' : 'salah',
                'poin'       => $isCorrect ? ($s['poin'] ?? 10) : 0,
            ]);
            if ($isCorrect) {
                $benar++;
            }
        }

        $totalSoal = count($soal);
        $nilai = $totalSoal > 0 ? round(($benar / $totalSoal) * 100, 2) : 0;

        $this->attemptModel->update($attemptId, [
            'nilai'        => $nilai,
            'completed_at' => date('Y-m-d H:i:s'),
            'status'       => 'completed'
        ]);

        // Award points based on performance: 10 base points + score/5
        $poinDiperoleh = 10 + floor($nilai / 5);
        \App\Helpers\GamifikasiHelper::tambahPoin($mahasiswaId, $poinDiperoleh);

        return redirect()->to('mahasiswa/kuis/hasil/' . $attemptId)->with('success', 'Kuis selesai! Nilai Anda: ' . $nilai . ' (+' . $poinDiperoleh . ' Poin diperoleh)');
    }

    public function hasil($attemptId)
    {
        $mahasiswaId = session()->get('id');
        $attempt = $this->attemptModel->find($attemptId);

        if (!$attempt || $attempt['mahasiswa_id'] != $mahasiswaId) {
            return redirect()->to('mahasiswa/kuis')->with('error', 'Data tidak ditemukan.');
        }

        $db   = \Config\Database::connect();
        $kuis = $db->table('kuis')->select('kuis.*, mata_kuliah.nama as nama_mk')
                   ->join('kelas', 'kelas.id = kuis.kelas_id')
                   ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                   ->where('kuis.id', $attempt['kuis_id'])->get()->getRowArray();

        $soal    = $this->soalModel->where('kuis_id', $attempt['kuis_id'])->findAll();
        $jawaban = $this->jawabanModel->where('attempt_id', $attemptId)->findAll();

        $jawabanMap = [];
        foreach ($jawaban as $j) {
            $jawabanMap[$j['soal_id']] = $j['jawaban'];
        }

        return view('mahasiswa/kuis/hasil', [
            'title'      => 'Hasil Kuis',
            'kuis'       => $kuis,
            'attempt'    => $attempt,
            'soal'       => $soal,
            'jawabanMap' => $jawabanMap,
        ]);
    }
}
