<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <a href="<?= base_url('mahasiswa/forum?kelas_id=' . $topik['kelas_id']) ?>" class="btn btn-outline-secondary mb-3">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Topik
        </a>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Kartu Topik Utama -->
        <div class="card card-outline card-warning mb-4">
            <div class="card-header bg-light">
                <div class="user-block">
                    <span class="username" style="font-size: 1.15em; font-weight: bold; margin-left: 0;">
                        <?= esc($topik['judul']) ?>
                    </span>
                    <span class="description" style="margin-left: 0;">
                        Oleh: <strong><?= esc($topik['nama_pembuat']) ?></strong> (<?= ucfirst(esc($topik['role_pembuat'])) ?>) &bull; <?= date('d M Y H:i', strtotime($topik['created_at'])) ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <p style="font-size: 1.1em; line-height: 1.6; white-space: pre-wrap;"><?= esc($topik['konten']) ?></p>
            </div>
        </div>

        <!-- Section Balasan -->
        <h4 class="mb-3 font-weight-bold"><i class="far fa-comments mr-2 text-warning"></i>Tanggapan (<?= count($balasan) ?>)</h4>
        
        <?php if (empty($balasan)): ?>
            <div class="alert alert-light text-center py-4 mb-4 border rounded">
                Belum ada tanggapan untuk diskusi ini.
            </div>
        <?php else: ?>
            <?php foreach ($balasan as $b): ?>
                <div class="card card-outline card-secondary mb-3">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong><?= esc($b['nama_pembuat']) ?></strong>
                                <span class="badge badge-<?= $b['role_pembuat'] == 'dosen' ? 'warning' : 'info' ?> ml-1" style="font-size: 0.8em;"><?= ucfirst(esc($b['role_pembuat'])) ?></span>
                            </div>
                            <small class="text-muted"><?= date('d M Y H:i', strtotime($b['created_at'])) ?></small>
                        </div>
                        <p class="mb-0 text-dark" style="line-height: 1.5; white-space: pre-wrap;"><?= esc($b['konten']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Form Tambah Balasan -->
        <div class="card card-outline card-warning mt-4">
            <div class="card-header">
                <h5 class="card-title font-weight-bold mb-0">Berikan Tanggapan Anda</h5>
            </div>
            <form action="<?= base_url('mahasiswa/forum/reply/' . $topik['id']) ?>" method="post">
                <div class="card-body">
                    <div class="form-group mb-0">
                        <textarea name="konten" class="form-control" rows="4" placeholder="Tuliskan respon atau jawaban diskusi Anda disini..." required></textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-warning text-white"><i class="fas fa-paper-plane mr-1"></i> Kirim Tanggapan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
