<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item">
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>Manajemen User</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="<?= base_url('admin/prodi') ?>" class="nav-link active">
            <i class="nav-icon fas fa-university"></i>
            <p>Program Studi</p>
        </a>
    </li>
    <li class="nav-item">
        <a href="#" class="nav-link">
            <i class="nav-icon fas fa-book"></i>
            <p>Mata Kuliah</p>
        </a>
    </li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Program Studi</h3>
            <div class="card-tools">
                <a href="<?= base_url('admin/prodi/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Prodi
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success m-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Kode</th>
                        <th>Nama Program Studi</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($prodi as $row) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['kode'] ?></td>
                        <td><?= $row['nama_prodi'] ?></td>
                        <td>
                            <a href="<?= base_url('admin/prodi/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= base_url('admin/prodi/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($prodi)): ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data Program Studi.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
