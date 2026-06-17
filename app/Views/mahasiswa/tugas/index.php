<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-tasks mr-2"></i>Daftar Tugas Anda</h3>
            </div>
            <div class="card-body">
                <?php if (empty($tugas)): ?>
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-clipboard-check mb-3" style="font-size:60px;"></i>
                        <h4>Tidak Ada Tugas</h4>
                        <p>Belum ada tugas yang diberikan untuk kelas Anda.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tugas as $t):
                        $sudahKumpul  = !empty($t['submit_id']);
                        $lewatDeadline = strtotime($t['deadline']) < time();
                        $sisaSec       = strtotime($t['deadline']) - time();
                        $sisaJam       = round($sisaSec / 3600);
                    ?>
                    <div class="card mb-3 <?= $sudahKumpul ? 'border-success' : ($lewatDeadline ? 'border-danger' : 'border-warning') ?>" style="border-left: 5px solid;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h5 class="mb-1">
                                        <?php if ($sudahKumpul): ?>
                                            <span class="badge badge-success mr-2"><i class="fas fa-check"></i> Sudah Dikumpulkan</span>
                                        <?php elseif ($lewatDeadline): ?>
                                            <span class="badge badge-danger mr-2"><i class="fas fa-times"></i> Deadline Lewat</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-white mr-2"><i class="fas fa-clock"></i> Belum Dikumpulkan</span>
                                        <?php endif; ?>
                                        <?= esc($t['judul']) ?>
                                    </h5>
                                    <p class="text-muted mb-1"><i class="fas fa-book"></i> <strong><?= esc($t['nama_mk']) ?></strong> &bull; TA: <?= esc($t['tahun_ajaran']) ?> (<?= ucfirst($t['semester']) ?>)</p>
                                    <?php if (!empty($t['deskripsi'])): ?>
                                        <p class="text-muted small mb-1"><?= esc($t['deskripsi']) ?></p>
                                    <?php endif; ?>
                                    <small class="text-muted">
                                        <i class="far fa-clock"></i> Deadline: <strong><?= date('d M Y H:i', strtotime($t['deadline'])) ?></strong>
                                        <?php if (!$lewatDeadline && !$sudahKumpul): ?>
                                            <span class="badge badge-<?= $sisaJam < 24 ? 'danger' : ($sisaJam < 72 ? 'warning' : 'info') ?> ml-1">
                                                Sisa <?= $sisaJam < 24 ? $sisaJam . ' jam' : round($sisaJam/24) . ' hari' ?>
                                            </span>
                                        <?php endif; ?>
                                    </small>
                                    <br><small class="text-muted">Nilai Maksimal: <?= esc($t['max_nilai']) ?></small>
                                </div>
                                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                                    <?php if ($sudahKumpul): ?>
                                        <div class="text-success mb-2">
                                            <i class="fas fa-check-circle"></i> Dikumpulkan: <?= date('d M Y H:i', strtotime($t['submitted_at'])) ?>
                                        </div>
                                        <?php if ($t['nilai'] !== null): ?>
                                            <div class="mb-2">
                                                <span class="h4 text-success font-weight-bold"><?= $t['nilai'] ?></span><span class="text-muted"> / <?= $t['max_nilai'] ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Menunggu Penilaian</span>
                                        <?php endif; ?>
                                        <?php if ($t['feedback']): ?>
                                            <p class="text-muted small mt-1"><i class="fas fa-comment"></i> <?= esc($t['feedback']) ?></p>
                                        <?php endif; ?>
                                        <a href="<?= base_url($t['file_submit']) ?>" class="btn btn-outline-primary btn-sm mt-1" download>
                                            <i class="fas fa-download"></i> File Saya
                                        </a>
                                    <?php elseif (!$lewatDeadline): ?>
                                        <button class="btn btn-success" data-toggle="modal" data-target="#modalKumpul<?= $t['id'] ?>">
                                            <i class="fas fa-upload"></i> Kumpulkan Tugas
                                        </button>
                                    <?php else: ?>
                                        <span class="text-danger"><i class="fas fa-times-circle"></i> Tidak dapat dikumpulkan</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!$sudahKumpul && !$lewatDeadline): ?>
                    <!-- Modal Kumpulkan Tugas -->
                    <div class="modal fade" id="modalKumpul<?= $t['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="<?= base_url('mahasiswa/tugas/submit/' . $t['id']) ?>" method="post" enctype="multipart/form-data">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Kumpulkan: <?= esc($t['judul']) ?></h5>
                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info small">
                                            <i class="fas fa-info-circle"></i> Deadline: <strong><?= date('d M Y H:i', strtotime($t['deadline'])) ?></strong>
                                        </div>
                                        <div class="form-group">
                                            <label>Upload File Tugas <span class="text-danger">*</span></label>
                                            <input type="file" name="file_tugas" class="form-control-file" required>
                                            <small class="text-muted">Format: PDF, Word, PPT, ZIP, RAR. Maks. 10MB.</small>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Kumpulkan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
