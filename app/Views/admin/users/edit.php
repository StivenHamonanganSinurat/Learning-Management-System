<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item"><a href="<?= base_url('admin/dashboard') ?>" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
    <li class="nav-item"><a href="<?= base_url('admin/users') ?>" class="nav-link active"><i class="nav-icon fas fa-users"></i><p>Manajemen User</p></a></li>
    <li class="nav-item"><a href="<?= base_url('admin/prodi') ?>" class="nav-link"><i class="nav-icon fas fa-university"></i><p>Program Studi</p></a></li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Mata Kuliah</p></a></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Edit User</h3>
        </div>
        
        <form action="<?= base_url('admin/users/update/'.$user['id']) ?>" method="post">
            <div class="card-body">
                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="nim_nidn">NIM / NIDN (Opsional)</label>
                    <input type="text" class="form-control" id="nim_nidn" name="nim_nidn" value="<?= old('nim_nidn', $user['nim_nidn']) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password Baru (Opsional)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                </div>

                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <option value="mahasiswa" <?= old('role', $user['role']) == 'mahasiswa' ? 'selected' : '' ?>>Mahasiswa</option>
                        <option value="dosen" <?= old('role', $user['role']) == 'dosen' ? 'selected' : '' ?>>Dosen</option>
                        <option value="admin" <?= old('role', $user['role']) == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update</button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>
