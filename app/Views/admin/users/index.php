<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item"><a href="<?= base_url('admin/dashboard') ?>" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
    <li class="nav-item"><a href="<?= base_url('admin/users') ?>" class="nav-link active"><i class="nav-icon fas fa-users"></i><p>Manajemen User</p></a></li>
    <li class="nav-item"><a href="<?= base_url('admin/prodi') ?>" class="nav-link"><i class="nav-icon fas fa-university"></i><p>Program Studi</p></a></li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Mata Kuliah</p></a></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data User</h3>
            <div class="card-tools">
                <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah User
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success m-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger m-3">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <table class="table table-striped table-responsive-md">
                <thead>
                    <tr>
                        <th style="width: 50px">No</th>
                        <th>Nama Lengkap</th>
                        <th>NIM/NIDN</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($users as $row) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['nim_nidn'] ?? '-' ?></td>
                        <td><?= $row['email'] ?></td>
                        <td>
                            <?php if($row['role'] == 'admin'): ?>
                                <span class="badge badge-danger">Admin</span>
                            <?php elseif($row['role'] == 'dosen'): ?>
                                <span class="badge badge-success">Dosen</span>
                            <?php else: ?>
                                <span class="badge badge-info">Mahasiswa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?= base_url('admin/users/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($row['id'] != session()->get('id')): ?>
                            <form action="<?= base_url('admin/users/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
