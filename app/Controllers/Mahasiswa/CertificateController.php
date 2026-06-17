<?php

namespace App\Controllers\Mahasiswa;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateController extends BaseController
{
    public function generate($nilai_id)
    {
        $mahasiswaId = session()->get('id');
        $db = \Config\Database::connect();

        // 1. Ambil detail nilai akhir
        $nilai = $db->table('nilai_akhir')
            ->select('nilai_akhir.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.sks,
                      kelas.tahun_ajaran, kelas.semester, users.name as nama_mahasiswa, users.nim_nidn as nim')
            ->join('kelas', 'kelas.id = nilai_akhir.kelas_id')
            ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
            ->join('users', 'users.id = nilai_akhir.mahasiswa_id')
            ->where('nilai_akhir.id', $nilai_id)
            ->where('nilai_akhir.mahasiswa_id', $mahasiswaId)
            ->get()->getRowArray();

        if (!$nilai) {
            return redirect()->to('mahasiswa/nilai')->with('error', 'Data nilai tidak ditemukan atau akses ditolak.');
        }

        // Cek kelayakan: hanya mendapat sertifikat jika lulus (Grade A, B, atau C)
        if (in_array($nilai['grade'], ['D', 'E', 'F', ''])) {
            return redirect()->to('mahasiswa/nilai')->with('error', 'Maaf, sertifikat kelulusan mata kuliah hanya diterbitkan untuk minimal Grade C.');
        }

        // 2. Load View Sertifikat HTML
        $html = view('mahasiswa/nilai/sertifikat_pdf', [
            'nilai' => $nilai,
            'tanggal' => date('d F Y')
        ]);

        // 3. Konfigurasi Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Diperlukan jika ingin memuat image dari URL luar

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Sertifikat biasanya Landscape
        $dompdf->render();

        // 4. Output PDF ke Browser (Download/Stream)
        return $this->response->setHeader('Content-Type', 'application/pdf')
                              ->setBody($dompdf->output());
    }
}
