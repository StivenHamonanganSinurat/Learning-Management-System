<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manajemen Kelas</h3>
            <div class="card-tools">
                <a href="<?= base_url('admin/kelas/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Kelas
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
                        <th>Mata Kuliah</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Dosen Pengampu</th>
                        <th style="width: 200px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($kelas as $row) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= $row['kode_mk'] ?> - <?= $row['nama_mk'] ?></strong></td>
                        <td><?= $row['tahun_ajaran'] ?></td>
                        <td><span class="badge badge-info"><?= ucfirst($row['semester']) ?></span></td>
                        <td><?= $row['nama_dosen'] ?></td>
                        <td>
                            <a href="<?= base_url('admin/kelas/peserta/'.$row['id']) ?>" class="btn btn-info btn-sm" title="Kelola Peserta (Mahasiswa)">
                                <i class="fas fa-users"></i> Peserta
                            </a>
                            <a href="<?= base_url('admin/kelas/jadwal/'.$row['id']) ?>" class="btn btn-success btn-sm" title="Kelola Jadwal Kelas & Ruangan">
                                <i class="fas fa-calendar-alt"></i> Jadwal
                            </a>
                            <a href="<?= base_url('admin/kelas/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm" title="Edit Kelas">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= base_url('admin/kelas/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kelas ini? Semua peserta akan terhapus dari kelas.')">
                                <button type="submit" class="btn btn-danger btn-sm" title="Hapus Kelas">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($kelas)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data Kelas.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?= $this->endSection() ?>
