<?php

namespace App\Controllers\Dosen;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\PesertaKelasModel;
use App\Models\AbsensiModel;
use App\Models\DetailAbsensiModel;
use App\Models\NilaiAkhirModel;
use App\Models\TugasModel;
use App\Models\TugasSubmitModel;
use App\Models\KuisModel;

class AbsensiNilaiController extends BaseController
{
    protected $kelasModel;
    protected $pesertaModel;
    protected $absensiModel;
    protected $detailAbsensiModel;
    protected $nilaiAkhirModel;
    protected $tugasModel;
    protected $tugasSubmitModel;
    protected $kuisModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->pesertaModel = new PesertaKelasModel();
        $this->absensiModel = new AbsensiModel();
        $this->detailAbsensiModel = new DetailAbsensiModel();
        $this->nilaiAkhirModel = new NilaiAkhirModel();
        $this->tugasModel = new TugasModel();
        $this->tugasSubmitModel = new TugasSubmitModel();
        $this->kuisModel = new KuisModel();
    }

    public function index()
    {
        $dosen_id = session()->get('id');

        // Ambil semua kelas yang diajar dosen ini
        $kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('mata_kuliah.dosen_id', $dosen_id)
                                  ->findAll();

        $selected_kelas_id = $this->request->getGet('kelas_id');
        $selected_kelas = null;
        $absensi = [];
        $mahasiswa = [];
        $nilai = [];

        if (!empty($selected_kelas_id)) {
            // Validasi kepemilikan kelas
            $selected_kelas = $this->kelasModel->select('kelas.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode_mk, mata_kuliah.dosen_id')
                                              ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                              ->where('kelas.id', $selected_kelas_id)
                                              ->first();

            if (!empty($selected_kelas) && $selected_kelas['dosen_id'] == $dosen_id) {
                // Ambil daftar absensi
                $absensi = $this->absensiModel->getByKelas($selected_kelas_id);

                // Ambil daftar mahasiswa peserta kelas
                $mahasiswa = $this->pesertaModel->select('kelas_mahasiswa.*, users.name as nama_mahasiswa, users.nim_nidn as nim')
                                                ->join('users', 'users.id = kelas_mahasiswa.mahasiswa_id')
                                                ->where('kelas_mahasiswa.kelas_id', $selected_kelas_id)
                                                ->orderBy('users.nim_nidn', 'ASC')
                                                ->findAll();

                // Pastikan setiap mahasiswa memiliki baris di tabel nilai_akhir
                foreach ($mahasiswa as $mhs) {
                    $exists = $this->nilaiAkhirModel->where([
                        'kelas_id'     => $selected_kelas_id,
                        'mahasiswa_id' => $mhs['mahasiswa_id']
                    ])->first();

                    if (!$exists) {
                        $this->nilaiAkhirModel->insert([
                            'kelas_id'     => $selected_kelas_id,
                            'mahasiswa_id' => $mhs['mahasiswa_id'],
                            'nilai_tugas'  => 0,
                            'nilai_kuis'   => 0,
                            'nilai_uts'    => 0,
                            'nilai_uas'    => 0,
                            'nilai_akhir'  => 0,
                            'grade'        => 'E'
                        ]);
                    }
                }

                // Ambil data nilai
                $nilai = $this->nilaiAkhirModel->getNilaiByKelas($selected_kelas_id);
            } else {
                $selected_kelas = null;
            }
        }

        $data = [
            'title'          => 'Absensi & Rekap Nilai',
            'kelas'          => $kelas,
            'selected_kelas' => $selected_kelas,
            'absensi'        => $absensi,
            'mahasiswa'      => $mahasiswa,
            'nilai'          => $nilai
        ];

        return view('dosen/absensi/index', $data);
    }

    public function storeAbsensi($kelas_id)
    {
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $rules = [
            'tanggal'      => 'required|valid_date[Y-m-d]',
            'pertemuan_ke' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $absensi_id = $this->absensiModel->insert([
            'kelas_id'     => $kelas_id,
            'tanggal'      => $this->request->getPost('tanggal'),
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke'),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Input detail absensi mahasiswa
        $status_mahasiswa = $this->request->getPost('status') ?? [];
        foreach ($status_mahasiswa as $mahasiswa_id => $status) {
            $this->detailAbsensiModel->insert([
                'absensi_id'   => $absensi_id,
                'mahasiswa_id' => $mahasiswa_id,
                'status'       => $status
            ]);
            // Jika hadir, berikan 5 poin
            if ($status === 'hadir') {
                \App\Helpers\GamifikasiHelper::tambahPoin($mahasiswa_id, 5);
            }
        }

        return redirect()->to('/dosen/absensi?kelas_id='.$kelas_id)->with('success', 'Absensi pertemuan baru berhasil disimpan.');
    }

    public function detailAbsensi($absensi_id)
    {
        $dosen_id = session()->get('id');
        $absensi = $this->absensiModel->find($absensi_id);

        if (!$absensi) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Absensi tidak ditemukan']);
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $absensi['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak']);
        }

        $details = $this->detailAbsensiModel->getAttendanceDetails($absensi_id);
        return $this->response->setJSON([
            'status'  => 'success',
            'absensi' => $absensi,
            'details' => $details
        ]);
    }

    public function updateAbsensi($absensi_id)
    {
        $dosen_id = session()->get('id');
        $absensi = $this->absensiModel->find($absensi_id);

        if (!$absensi) {
            return redirect()->back()->with('error', 'Absensi tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $absensi['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $this->absensiModel->update($absensi_id, [
            'tanggal'      => $this->request->getPost('tanggal'),
            'pertemuan_ke' => $this->request->getPost('pertemuan_ke')
        ]);

        $status_mahasiswa = $this->request->getPost('status') ?? [];
        foreach ($status_mahasiswa as $mahasiswa_id => $status) {
            $this->detailAbsensiModel->where([
                'absensi_id'   => $absensi_id,
                'mahasiswa_id' => $mahasiswa_id
            ])->set(['status' => $status])->update();
            
            // Re-check badges in case student reaches attendance threshold
            \App\Helpers\GamifikasiHelper::checkAndAwardBadges($mahasiswa_id);
        }

        return redirect()->to('/dosen/absensi?kelas_id='.$absensi['kelas_id'])->with('success', 'Absensi berhasil diperbarui.');
    }

    public function deleteAbsensi($absensi_id)
    {
        $dosen_id = session()->get('id');
        $absensi = $this->absensiModel->find($absensi_id);

        if (!$absensi) {
            return redirect()->back()->with('error', 'Absensi tidak ditemukan.');
        }

        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $absensi['kelas_id'])
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        // Hapus detail absensi dulu
        $this->detailAbsensiModel->where('absensi_id', $absensi_id)->delete();
        $this->absensiModel->delete($absensi_id);

        return redirect()->to('/dosen/absensi?kelas_id='.$absensi['kelas_id'])->with('success', 'Absensi berhasil dihapus.');
    }

    public function updateNilai($kelas_id)
    {
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        $nilai_data = $this->request->getPost('nilai') ?? [];
        foreach ($nilai_data as $mahasiswa_id => $n) {
            $tugas = floatval($n['tugas']);
            $kuis = floatval($n['kuis']);
            $uts = floatval($n['uts']);
            $uas = floatval($n['uas']);

            // Hitung nilai akhir: 30% tugas + 20% kuis + 25% uts + 25% uas
            $nilai_akhir = ($tugas * 0.3) + ($kuis * 0.2) + ($uts * 0.25) + ($uas * 0.25);

            // Tentukan grade
            $grade = 'E';
            if ($nilai_akhir >= 85) {
                $grade = 'A';
            } elseif ($nilai_akhir >= 70) {
                $grade = 'B';
            } elseif ($nilai_akhir >= 55) {
                $grade = 'C';
            } elseif ($nilai_akhir >= 40) {
                $grade = 'D';
            }

            $this->nilaiAkhirModel->where([
                'kelas_id'     => $kelas_id,
                'mahasiswa_id' => $mahasiswa_id
            ])->set([
                'nilai_tugas' => $tugas,
                'nilai_kuis'  => $kuis,
                'nilai_uts'   => $uts,
                'nilai_uas'   => $uas,
                'nilai_akhir' => $nilai_akhir,
                'grade'       => $grade
            ])->update();
        }

        return redirect()->to('/dosen/absensi?kelas_id='.$kelas_id.'&tab=nilai')->with('success', 'Rekap nilai mahasiswa berhasil disimpan.');
    }

    public function syncNilai($kelas_id)
    {
        $dosen_id = session()->get('id');
        $kelas = $this->kelasModel->select('kelas.id, mata_kuliah.dosen_id')
                                  ->join('mata_kuliah', 'mata_kuliah.id = kelas.mata_kuliah_id')
                                  ->where('kelas.id', $kelas_id)
                                  ->first();

        if (empty($kelas) || $kelas['dosen_id'] != $dosen_id) {
            return redirect()->to('/dosen/dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil mahasiswa di kelas
        $peserta = $this->pesertaModel->where('kelas_id', $kelas_id)->findAll();

        foreach ($peserta as $p) {
            $mahasiswa_id = $p['mahasiswa_id'];

            // Sync Rata-rata Tugas
            // Ambil semua tugas di kelas ini
            $tugas_list = $this->tugasModel->where('kelas_id', $kelas_id)->findAll();
            $tugas_ids = array_column($tugas_list, 'id');

            $avg_tugas = 0;
            if (!empty($tugas_ids)) {
                $submits = $this->tugasSubmitModel->whereIn('tugas_id', $tugas_ids)
                                                  ->where('mahasiswa_id', $mahasiswa_id)
                                                  ->where('nilai IS NOT NULL')
                                                  ->findAll();
                if (!empty($submits)) {
                    $total_nilai = array_sum(array_column($submits, 'nilai'));
                    $avg_tugas = $total_nilai / count($tugas_list); // Dibagi total seluruh tugas agar adil (jika ada tugas yang tidak dikerjakan nilainya terhitung 0)
                }
            }

            // Sync Rata-rata Kuis, UTS, dan UAS berdasarkan tipe di tabel kuis
            $db = \Config\Database::connect();
            $types = ['kuis', 'uts', 'uas'];
            $scores = ['kuis' => 0, 'uts' => 0, 'uas' => 0];

            foreach ($types as $type) {
                $kuis_list = $this->kuisModel->where('kelas_id', $kelas_id)->where('tipe', $type)->findAll();
                $kuis_ids = array_column($kuis_list, 'id');
                if (!empty($kuis_ids)) {
                    $total_nilai = 0;
                    foreach ($kuis_ids as $k_id) {
                        $max_attempt_nilai = $db->table('kuis_attempt')
                            ->where('kuis_id', $k_id)
                            ->where('mahasiswa_id', $mahasiswa_id)
                            ->where('status', 'completed')
                            ->selectMax('nilai')
                            ->get()->getRowArray();
                        $total_nilai += isset($max_attempt_nilai['nilai']) ? floatval($max_attempt_nilai['nilai']) : 0;
                    }
                    $scores[$type] = $total_nilai / count($kuis_list);
                }
            }
            
            // Update nilai tugas, kuis, UTS, dan UAS ke tabel rekap
            $rekap = $this->nilaiAkhirModel->where([
                'kelas_id'     => $kelas_id,
                'mahasiswa_id' => $mahasiswa_id
            ])->first();
 
            if ($rekap) {
                $tugas = $avg_tugas;
                $kuis  = $scores['kuis'];
                $uts   = $scores['uts'] > 0 ? $scores['uts'] : $rekap['nilai_uts']; // Jika ada ujian UTS otomatis, gunakan itu, jika tidak keep manual
                $uas   = $scores['uas'] > 0 ? $scores['uas'] : $rekap['nilai_uas']; // Jika ada ujian UAS otomatis, gunakan itu, jika tidak keep manual
 
                $nilai_akhir = ($tugas * 0.3) + ($kuis * 0.2) + ($uts * 0.25) + ($uas * 0.25);
 
                $grade = 'E';
                if ($nilai_akhir >= 85) {
                    $grade = 'A';
                } elseif ($nilai_akhir >= 70) {
                    $grade = 'B';
                } elseif ($nilai_akhir >= 55) {
                    $grade = 'C';
                } elseif ($nilai_akhir >= 40) {
                    $grade = 'D';
                }
 
                $this->nilaiAkhirModel->update($rekap['id'], [
                    'nilai_tugas' => $tugas,
                    'nilai_kuis'  => $kuis,
                    'nilai_uts'   => $uts,
                    'nilai_uas'   => $uas,
                    'nilai_akhir' => $nilai_akhir,
                    'grade'       => $grade
                ]);
            }
        }
 
        return redirect()->to('/dosen/absensi?kelas_id='.$kelas_id.'&tab=nilai')->with('success', 'Seluruh nilai tugas, kuis, UTS, dan UAS berhasil disinkronisasi.');
    }
}
