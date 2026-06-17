<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Kartu Nilai -->
        <div class="card card-<?= $attempt['nilai'] >= 75 ? 'success' : ($attempt['nilai'] >= 60 ? 'warning' : 'danger') ?> card-outline mb-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-<?= $attempt['nilai'] >= 75 ? 'trophy text-success' : ($attempt['nilai'] >= 60 ? 'star text-warning' : 'times-circle text-danger') ?>" style="font-size: 70px;"></i>
                <h2 class="mt-3"><?= $attempt['nilai'] >= 75 ? 'Selamat!' : ($attempt['nilai'] >= 60 ? 'Hampir!' : 'Coba Lagi!') ?></h2>
                <p class="text-muted"><?= esc($kuis['judul']) ?> &bull; <?= esc($kuis['nama_mk']) ?></p>
                <div class="display-4 font-weight-bold my-3 text-<?= $attempt['nilai'] >= 75 ? 'success' : ($attempt['nilai'] >= 60 ? 'warning' : 'danger') ?>">
                    <?= $attempt['nilai'] ?>
                    <small style="font-size: 0.4em;" class="text-muted">/ 100</small>
                </div>
                <p class="text-muted small">
                    Dikerjakan: <?= date('d M Y H:i', strtotime($attempt['started_at'])) ?> &mdash;
                    Selesai: <?= date('d M Y H:i', strtotime($attempt['completed_at'])) ?>
                </p>
                <a href="<?= base_url('mahasiswa/kuis') ?>" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Kuis
                </a>
            </div>
        </div>

        <!-- Pembahasan Soal -->
        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h4 class="card-title"><i class="fas fa-clipboard-list mr-2"></i>Pembahasan Jawaban</h4>
            </div>
            <div class="card-body">
                <?php foreach ($soal as $i => $s):
                    $jawabanKu  = $jawabanMap[$s['id']] ?? null;
                    $benar      = $jawabanKu && strtoupper($jawabanKu) === strtoupper($s['jawaban_benar']);
                ?>
                <div class="mb-4 p-3 rounded" style="background: <?= $benar ? '#f0fff4' : '#fff5f5' ?>; border-left: 4px solid <?= $benar ? '#28a745' : '#dc3545' ?>;">
                    <p class="mb-2"><strong>Soal <?= $i + 1 ?>:</strong> <?= esc($s['pertanyaan']) ?></p>
                    <div class="row">
                        <?php foreach (['a', 'b', 'c', 'd'] as $opt):
                            $key  = 'opsi_' . $opt;
                            if (empty($s[$key])) continue;
                            $isBenar    = strtoupper($opt) === strtoupper($s['jawaban_benar']);
                            $isPilihKu  = strtoupper($jawabanKu ?? '') === strtoupper($opt);
                        ?>
                        <div class="col-md-6 mb-1">
                            <span class="d-block p-2 rounded small"
                                style="background: <?= $isBenar ? '#d4edda' : ($isPilihKu && !$isBenar ? '#f8d7da' : '#fff') ?>;
                                       border: 1px solid <?= $isBenar ? '#28a745' : ($isPilihKu && !$isBenar ? '#dc3545' : '#dee2e6') ?>;">
                                <?php if ($isBenar): ?><i class="fas fa-check text-success mr-1"></i><?php endif; ?>
                                <?php if ($isPilihKu && !$isBenar): ?><i class="fas fa-times text-danger mr-1"></i><?php endif; ?>
                                <strong><?= strtoupper($opt) ?>.</strong> <?= esc($s[$key]) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <small class="mt-1 d-block">
                        <?php if ($benar): ?>
                            <span class="text-success"><i class="fas fa-check-circle"></i> Jawaban Anda benar!</span>
                        <?php elseif (!$jawabanKu): ?>
                            <span class="text-muted"><i class="fas fa-minus-circle"></i> Tidak dijawab. Jawaban benar: <strong><?= strtoupper($s['jawaban_benar']) ?></strong></span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-times-circle"></i> Jawaban Anda: <strong><?= strtoupper($jawabanKu) ?></strong> &mdash; Jawaban benar: <strong><?= strtoupper($s['jawaban_benar']) ?></strong></span>
                        <?php endif; ?>
                    </small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
