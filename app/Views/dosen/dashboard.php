<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3><?= $total_kelas ?></h3>
                    <p>Kelas Aktif Anda</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <a href="<?= base_url('dosen/kelas') ?>" class="small-box-footer">Lihat Kelas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>0</h3>
                    <p>Tugas Perlu Dinilai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="#" class="small-box-footer">Lihat Tugas <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Kelas Anda Semester Ini</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($kelas)): ?>
                        <?php foreach($kelas as $k): ?>
                        <tr>
                            <td><strong><?= $k['kode_mk'] ?> - <?= $k['nama_mk'] ?></strong></td>
                            <td><?= $k['tahun_ajaran'] ?></td>
                            <td><span class="badge badge-info"><?= ucfirst($k['semester']) ?></span></td>
                            <td>
                                <a href="<?= base_url('dosen/kelas/kelola/'.$k['id']) ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-cog"></i> Kelola
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Anda belum memiliki jadwal kelas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
