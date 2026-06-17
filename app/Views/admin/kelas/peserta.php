<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="row">
        <!-- Form Tambah Peserta -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Tambah Mahasiswa</h3>
                </div>
                <form action="<?= base_url('admin/kelas/peserta/add/'.$kelas['id']) ?>" method="post">
                    <div class="card-body">
                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label>Pilih Mahasiswa</label>
                            <select name="mahasiswa_id" class="form-control" required>
                                <option value="">-- Pilih Mahasiswa --</option>
                                <?php foreach($mahasiswa as $m): ?>
                                    <option value="<?= $m['id'] ?>"><?= $m['nim_nidn'] ?> - <?= $m['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary w-100">Tambah ke Kelas</button>
                    </div>
                </form>
            </div>
            
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">Info Kelas</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table">
                        <tr><th>Mata Kuliah</th><td><?= $kelas['nama_mk'] ?></td></tr>
                        <tr><th>Tahun Ajaran</th><td><?= $kelas['tahun_ajaran'] ?></td></tr>
                        <tr><th>Semester</th><td><?= ucfirst($kelas['semester']) ?></td></tr>
                        <tr><th>Total Peserta</th><td><?= count($peserta) ?> Mahasiswa</td></tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Daftar Peserta -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Mahasiswa Terdaftar</h3>
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
                                <th>NIM</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th style="width: 100px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($peserta as $row) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $row['nim_nidn'] ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['email'] ?></td>
                                <td>
                                    <form action="<?= base_url('admin/kelas/peserta/remove/'.$row['enroll_id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Keluarkan mahasiswa ini dari kelas?')">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Keluarkan">
                                            <i class="fas fa-times"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($peserta)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Belum ada mahasiswa di kelas ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= base_url('admin/kelas') ?>" class="btn btn-default">Kembali ke Daftar Kelas</a>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
