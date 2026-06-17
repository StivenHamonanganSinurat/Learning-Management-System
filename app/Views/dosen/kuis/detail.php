<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Detail Kuis Info -->
    <div class="col-md-4">
        <div class="card card-warning">
            <div class="card-header text-white" style="background-color: #ffc107;">
                <h3 class="card-title font-weight-bold">Informasi Kuis</h3>
            </div>
            <div class="card-body">
                <h5><strong><?= esc($kuis['judul']) ?></strong></h5>
                <hr>
                <strong><i class="far fa-clock mr-1"></i> Durasi</strong>
                <p><?= esc($kuis['durasi_menit']) ?> Menit</p>
                <hr>
                <strong><i class="fas fa-history mr-1"></i> Batas Percobaan</strong>
                <p><?= esc($kuis['max_attempt']) ?>x Percobaan</p>
                <hr>
                <strong><i class="far fa-calendar-alt mr-1"></i> Batas Waktu (Deadline)</strong>
                <p class="text-danger"><?= date('d M Y, H:i', strtotime($kuis['deadline'])) ?></p>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('dosen/kuis') ?>" class="btn btn-default w-100"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Kuis</a>
            </div>
        </div>

        <!-- Tambah Soal Form -->
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah Soal Baru</h3>
            </div>
            <form action="<?= base_url('dosen/kuis/storeSoal/'.$kuis['id']) ?>" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label>Pertanyaan</label>
                        <textarea name="pertanyaan" class="form-control" rows="3" required placeholder="Tuliskan pertanyaan disini..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Opsi A</label>
                        <input type="text" name="opsi_a" class="form-control" required placeholder="Jawaban A">
                    </div>
                    <div class="form-group">
                        <label>Opsi B</label>
                        <input type="text" name="opsi_b" class="form-control" required placeholder="Jawaban B">
                    </div>
                    <div class="form-group">
                        <label>Opsi C</label>
                        <input type="text" name="opsi_c" class="form-control" required placeholder="Jawaban C">
                    </div>
                    <div class="form-group">
                        <label>Opsi D</label>
                        <input type="text" name="opsi_d" class="form-control" required placeholder="Jawaban D">
                    </div>
                    <div class="form-group">
                        <label>Jawaban Benar</label>
                        <select name="jawaban_benar" class="form-control" required>
                            <option value="">-- Pilih Jawaban Benar --</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Poin</label>
                        <input type="number" name="poin" class="form-control" value="10" min="1" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success w-100"><i class="fas fa-plus"></i> Simpan Pertanyaan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bagian Kanan dengan Tab Soal & Nilai Percobaan -->
    <div class="col-md-8">
        <div class="card card-outline card-warning">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#tab_soal" data-toggle="tab"><i class="fas fa-question-circle mr-1"></i> Daftar Soal</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tab_nilai_mahasiswa" data-toggle="tab"><i class="fas fa-graduation-cap mr-1"></i> Hasil & Nilai Percobaan</a></li>
                </ul>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="tab-content">
                    <!-- Tab Daftar Soal -->
                    <div class="tab-pane active" id="tab_soal">
                        <?php if (empty($soal)): ?>
                            <div class="alert alert-info text-center">
                                Belum ada soal untuk kuis ini. Silakan tambahkan melalui form di samping.
                            </div>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach($soal as $s): 
                            ?>
                            <div class="card card-outline card-secondary mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0"><strong>Soal #<?= $no++ ?></strong> <span class="badge badge-secondary ml-2"><?= $s['poin'] ?> Poin</span></h5>
                                    <form action="<?= base_url('dosen/kuis/deleteSoal/'.$s['id']) ?>" method="post" class="ml-auto" onsubmit="return confirm('Hapus pertanyaan ini?')">
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </div>
                                <div class="card-body">
                                    <p class="font-weight-bold" style="font-size: 16px;"><?= nl2br(esc($s['pertanyaan'])) ?></p>
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 border rounded <?= $s['jawaban_benar'] == 'A' ? 'bg-success border-success text-white' : 'bg-light' ?>">
                                                <strong>A.</strong> <?= esc($s['opsi_a']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 border rounded <?= $s['jawaban_benar'] == 'B' ? 'bg-success border-success text-white' : 'bg-light' ?>">
                                                <strong>B.</strong> <?= esc($s['opsi_b']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 border rounded <?= $s['jawaban_benar'] == 'C' ? 'bg-success border-success text-white' : 'bg-light' ?>">
                                                <strong>C.</strong> <?= esc($s['opsi_c']) ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <div class="p-2 border rounded <?= $s['jawaban_benar'] == 'D' ? 'bg-success border-success text-white' : 'bg-light' ?>">
                                                <strong>D.</strong> <?= esc($s['opsi_d']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Tab Hasil & Nilai Percobaan -->
                    <div class="tab-pane" id="tab_nilai_mahasiswa">
                        <?php
                        $db = \Config\Database::connect();
                        // Ambil semua attempt kuis ini
                        $attempts = $db->table('kuis_attempt')
                                       ->select('kuis_attempt.*, users.name as nama_mahasiswa, users.nim_nidn as nim')
                                       ->join('users', 'users.id = kuis_attempt.mahasiswa_id')
                                       ->where('kuis_attempt.kuis_id', $kuis['id'])
                                       ->orderBy('kuis_attempt.completed_at', 'DESC')
                                       ->get()->getResultArray();
                        ?>
                        <?php if (empty($attempts)): ?>
                            <div class="alert alert-info text-center">
                                Belum ada mahasiswa yang mengerjakan kuis ini.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Selesai</th>
                                            <th>Status</th>
                                            <th>Nilai</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($attempts as $att): ?>
                                        <tr>
                                            <td><?= esc($att['nim']) ?></td>
                                            <td><strong><?= esc($att['nama_mahasiswa']) ?></strong></td>
                                            <td><small><?= date('d M Y H:i', strtotime($att['started_at'])) ?></small></td>
                                            <td><small><?= $att['completed_at'] ? date('d M Y H:i', strtotime($att['completed_at'])) : '-' ?></small></td>
                                            <td>
                                                <span class="badge badge-<?= $att['status'] == 'completed' ? 'success' : 'warning' ?>">
                                                    <?= ucfirst($att['status']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="font-weight-bold" style="font-size: 1.1em;"><?= $att['nilai'] ?></span>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-warning btn-xs text-white" data-toggle="modal" data-target="#modalEditNilaiAttempt<?= $att['id'] ?>">
                                                    <i class="fas fa-edit"></i> Edit Nilai
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal Edit Nilai Attempt -->
                                        <div class="modal fade" id="modalEditNilaiAttempt<?= $att['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content text-left">
                                                    <form action="<?= base_url('dosen/kuis/updateNilaiAttempt/' . $att['id']) ?>" method="post">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit Nilai: <?= esc($att['nama_mahasiswa']) ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-muted">Edit nilai hasil pengerjaan kuis mahasiswa secara manual.</p>
                                                            <div class="form-group">
                                                                <label>Nilai Ujian</label>
                                                                <input type="number" step="0.01" name="nilai" class="form-control" value="<?= $att['nilai'] ?>" min="0" max="100" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning text-white">Simpan Nilai</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
