<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Detail Tugas Info -->
    <div class="col-md-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informasi Tugas</h3>
            </div>
            <div class="card-body">
                <h5><strong><?= $tugas['judul'] ?></strong></h5>
                <p class="text-muted"><?= nl2br(esc($tugas['deskripsi'])) ?: 'Tidak ada deskripsi.' ?></p>
                <hr>
                <strong><i class="far fa-clock mr-1"></i> Batas Waktu (Deadline)</strong>
                <p class="text-danger"><?= date('d M Y, H:i', strtotime($tugas['deadline'])) ?></p>
                <hr>
                <strong><i class="fas fa-star mr-1"></i> Nilai Maksimal</strong>
                <p class="text-success"><?= $tugas['max_nilai'] ?></p>
            </div>
            <div class="card-footer">
                <a href="<?= base_url('dosen/tugas') ?>" class="btn btn-default w-100"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Tugas</a>
            </div>
        </div>
    </div>

    <!-- Daftar Pengumpulan -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pengumpulan Mahasiswa</h3>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
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

                <?php if (empty($submissions)): ?>
                    <div class="alert alert-info text-center">
                        Belum ada mahasiswa yang mengumpulkan tugas ini.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Mahasiswa</th>
                                    <th>Tanggal Kirim</th>
                                    <th>File</th>
                                    <th>Nilai</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($submissions as $s): ?>
                                <tr>
                                    <td>
                                        <strong><?= esc($s['nama_mahasiswa']) ?></strong><br>
                                        <small class="text-muted">NIM: <?= esc($s['nim']) ?></small>
                                    </td>
                                    <td>
                                        <?= date('d M Y H:i', strtotime($s['submitted_at'])) ?>
                                        <?php if (strtotime($s['submitted_at']) > strtotime($tugas['deadline'])): ?>
                                            <span class="badge badge-danger">Terlambat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url($s['file_path']) ?>" target="_blank" class="btn btn-outline-primary btn-xs"><i class="fas fa-file-download"></i> Download</a>
                                    </td>
                                    <td>
                                        <?php if ($s['nilai'] !== null): ?>
                                            <span class="badge badge-success font-weight-bold" style="font-size: 14px;"><?= $s['nilai'] ?> / <?= $tugas['max_nilai'] ?></span>
                                            <?php if ($s['feedback']): ?>
                                                <br><small class="text-muted">Feedback: <em><?= esc($s['feedback']) ?></em></small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Belum Dinilai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalNilai<?= $s['id'] ?>">
                                            <i class="fas fa-edit"></i> <?= $s['nilai'] !== null ? 'Edit Nilai' : 'Beri Nilai' ?>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Modal Penilaian -->
                                <div class="modal fade" id="modalNilai<?= $s['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalNilaiLabel<?= $s['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('dosen/tugas/nilai/'.$s['id']) ?>" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalNilaiLabel<?= $s['id'] ?>">Penilaian: <?= esc($s['nama_mahasiswa']) ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Nilai (Maksimal: <?= $tugas['max_nilai'] ?>)</label>
                                                        <input type="number" name="nilai" class="form-control" value="<?= $s['nilai'] ?? '' ?>" min="0" max="<?= $tugas['max_nilai'] ?>" required placeholder="Masukkan nilai angka">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Feedback (Catatan untuk Mahasiswa)</label>
                                                        <textarea name="feedback" class="form-control" rows="3" placeholder="Masukkan feedback jika ada..."><?= esc($s['feedback']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success">Simpan Penilaian</button>
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
<?= $this->endSection() ?>
