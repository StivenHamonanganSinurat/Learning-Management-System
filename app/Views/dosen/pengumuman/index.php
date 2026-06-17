<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">Daftar Pengumuman Kuliah</h3>
                <button type="button" class="btn btn-warning btn-sm ml-auto text-white" data-toggle="modal" data-target="#modalTambahPengumuman">
                    <i class="fas fa-plus"></i> Publikasi Pengumuman Baru
                </button>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($pengumuman)): ?>
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-bullhorn mb-3" style="font-size: 60px;"></i>
                        <h4>Belum Ada Pengumuman</h4>
                        <p>Silakan buat pengumuman baru untuk kelas yang Anda ajar.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kelas / Mata Kuliah</th>
                                    <th>Pengumuman</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($pengumuman as $p): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($p['nama_mk']) ?></strong><br>
                                        <small class="text-muted">TA: <?= esc($p['tahun_ajaran']) ?> (<?= ucfirst(esc($p['semester'])) ?>)</small>
                                    </td>
                                    <td>
                                        <strong><?= esc($p['judul']) ?></strong>
                                        <p class="mb-0 text-muted small"><?= esc(substr(strip_tags($p['isi']), 0, 100)) ?>...</p>
                                    </td>
                                    <td><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-warning btn-xs text-white" data-toggle="modal" data-target="#modalEditPengumuman<?= $p['id'] ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="<?= base_url('dosen/pengumuman/delete/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pengumuman ini secara permanen?')">
                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Edit Pengumuman -->
                                <div class="modal fade" id="modalEditPengumuman<?= $p['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('dosen/pengumuman/update/' . $p['id']) ?>" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title font-weight-bold">Edit Pengumuman</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="form-group">
                                                        <label>Judul Pengumuman</label>
                                                        <input type="text" name="judul" class="form-control" value="<?= esc($p['judul']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Isi Pengumuman</label>
                                                        <textarea name="isi" class="form-control" rows="6" required><?= esc($p['isi']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengumuman -->
<div class="modal fade" id="modalTambahPengumuman" tabindex="-1" role="dialog" aria-labelledby="modalTambahPengumumanLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?= base_url('dosen/pengumuman/store') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title font-weight-bold" id="modalTambahPengumumanLabel">Publikasikan Pengumuman Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas / Mata Kuliah</label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_mk']) ?> (TA: <?= esc($k['tahun_ajaran']) ?> - <?= ucfirst(esc($k['semester'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Judul Pengumuman</label>
                        <input type="text" name="judul" class="form-control" placeholder="Contoh: Perubahan Jadwal Kuliah Pertemuan 5" required>
                    </div>
                    <div class="form-group">
                        <label>Isi Pengumuman</label>
                        <textarea name="isi" class="form-control" rows="6" placeholder="Tulis rincian pengumuman disini..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
