<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Mata Kuliah</h3>
            <div class="card-tools">
                <a href="<?= base_url('admin/matakuliah/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Mata Kuliah
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success m-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <table class="table table-striped table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <th>SKS</th>
                        <th>Semester</th>
                        <th>Program Studi</th>
                        <th>Dosen Pengampu</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($matakuliah as $row) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['kode_mk'] ?></td>
                        <td><?= $row['nama'] ?></td>
                        <td><?= $row['sks'] ?></td>
                        <td><?= $row['semester'] ?></td>
                        <td><?= $row['nama_prodi'] ?></td>
                        <td><?= $row['nama_dosen'] ?></td>
                        <td>
                            <a href="<?= base_url('admin/matakuliah/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= base_url('admin/matakuliah/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($matakuliah)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Belum ada data Mata Kuliah.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
