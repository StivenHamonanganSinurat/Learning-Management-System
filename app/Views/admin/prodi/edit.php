<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('sidebar_menu') ?>
    <li class="nav-item"><a href="<?= base_url('admin/dashboard') ?>" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Manajemen User</p></a></li>
    <li class="nav-item"><a href="<?= base_url('admin/prodi') ?>" class="nav-link active"><i class="nav-icon fas fa-university"></i><p>Program Studi</p></a></li>
    <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-book"></i><p>Mata Kuliah</p></a></li>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Edit Program Studi</h3>
        </div>
        
        <form action="<?= base_url('admin/prodi/update/'.$prodi['id']) ?>" method="post">
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
                    <label for="kode">Kode Prodi</label>
                    <input type="text" class="form-control" id="kode" name="kode" value="<?= old('kode', $prodi['kode']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_prodi">Nama Program Studi</label>
                    <input type="text" class="form-control" id="nama_prodi" name="nama_prodi" value="<?= old('nama_prodi', $prodi['nama_prodi']) ?>" required>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update</button>
                <a href="<?= base_url('admin/prodi') ?>" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>
