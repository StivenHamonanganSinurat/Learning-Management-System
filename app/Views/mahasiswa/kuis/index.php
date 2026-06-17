<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-question-circle mr-2"></i>Daftar <?= strtoupper($tipe) ?> Anda</h3>
            </div>
            <div class="card-body">
                <?php if (empty($kuis)): ?>
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-question mb-3" style="font-size:60px;"></i>
                        <h4>Belum Ada <?= strtoupper($tipe) ?></h4>
                        <p>Dosen belum membuat <?= $tipe ?> untuk kelas Anda.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($kuis as $k):
                        $lewatDeadline = strtotime($k['deadline']) < time();
                    ?>
                    <div class="card mb-3 <?= $k['jumlah_attempt'] >= $k['max_attempt'] ? 'border-success' : ($lewatDeadline ? 'border-danger' : 'border-warning') ?>" style="border-left: 5px solid;">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-7">
                                    <h5 class="mb-1">
                                        <?php if ($k['jumlah_attempt'] >= $k['max_attempt']): ?>
                                            <span class="badge badge-success mr-1"><i class="fas fa-check"></i> Selesai</span>
                                        <?php elseif ($lewatDeadline): ?>
                                            <span class="badge badge-danger mr-1"><i class="fas fa-times"></i> Deadline Lewat</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-white mr-1"><i class="fas fa-play"></i> Tersedia</span>
                                        <?php endif; ?>
                                        <?= esc($k['judul']) ?>
                                    </h5>
                                    <p class="text-muted mb-1"><i class="fas fa-book"></i> <strong><?= esc($k['nama_mk']) ?></strong> &bull; TA: <?= esc($k['tahun_ajaran']) ?></p>
                                    <small class="text-muted">
                                        <i class="fas fa-stopwatch"></i> Durasi: <?= $k['durasi_menit'] ?> menit &nbsp;|&nbsp;
                                        <i class="fas fa-redo"></i> Percobaan: <?= $k['jumlah_attempt'] ?>/<?= $k['max_attempt'] ?> &nbsp;|&nbsp;
                                        <i class="far fa-clock"></i> Deadline: <?= date('d M Y H:i', strtotime($k['deadline'])) ?>
                                    </small>
                                    <?php if ($k['nilai_terbaik'] !== null): ?>
                                        <br><small class="text-success"><i class="fas fa-star"></i> Nilai Terbaik: <strong><?= $k['nilai_terbaik'] ?></strong></small>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-5 text-md-right mt-3 mt-md-0">
                                    <?php if ($k['bisa_ikut']): ?>
                                        <a href="<?= base_url('mahasiswa/kuis/mulai/' . $k['id']) ?>"
                                           class="btn btn-warning text-white"
                                           onclick="return confirm('<?= strtoupper($tipe) ?> akan dimulai sekarang. Durasi: <?= $k['durasi_menit'] ?> menit. Siap?')">
                                            <i class="fas fa-play-circle"></i> Mulai <?= strtoupper($tipe) ?>
                                        </a>
                                    <?php elseif ($k['jumlah_attempt'] > 0): ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> Sudah Selesai</span>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="fas fa-lock"></i> Tidak Dapat Dikerjakan</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
