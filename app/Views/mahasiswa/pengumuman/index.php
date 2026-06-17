<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-bullhorn mr-2"></i>Daftar Pengumuman Kuliah</h3>
            </div>
            <div class="card-body">
                <?php if (empty($pengumuman)): ?>
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-bullhorn mb-3" style="font-size: 60px;"></i>
                        <h4>Belum Ada Pengumuman</h4>
                        <p>Tidak ada pengumuman baru untuk kelas yang Anda ikuti.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pengumuman as $p): ?>
                        <div class="card card-outline card-warning mb-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title font-weight-bold text-dark mb-0"><?= esc($p['judul']) ?></h5>
                                <span class="float-right text-muted small"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($p['created_at'])) ?></span>
                            </div>
                            <div class="card-body">
                                <p class="mb-2" style="white-space: pre-wrap; line-height: 1.6;"><?= esc($p['isi']) ?></p>
                                <hr class="my-2">
                                <small class="text-muted"><i class="fas fa-book"></i> Mata Kuliah: <strong><?= esc($p['nama_mk']) ?></strong> &bull; TA: <?= esc($p['tahun_ajaran']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
