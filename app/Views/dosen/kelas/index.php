<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kelas Anda Semester Ini</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Mata Kuliah</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($kelas as $row) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $row['kode_mk'] ?> - <?= $row['nama_mk'] ?></strong></td>
                        <td><?= $row['tahun_ajaran'] ?></td>
                        <td><span class="badge badge-info"><?= ucfirst($row['semester']) ?></span></td>
                        <td>
                            <a href="<?= base_url('dosen/kelas/kelola/'.$row['id']) ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-cog"></i> Kelola
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($kelas)): ?>
                    <tr>
                        <td colspan="5" class="text-center">Anda belum memiliki jadwal kelas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
