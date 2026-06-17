<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Tugas Kuliah</h3>
                <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#modalTambahTugas">
                    <i class="fas fa-plus"></i> Tambah Tugas Baru
                </button>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="alert alert-danger">
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

                <?php if(empty($tugas)): ?>
                    <div class="alert alert-info text-center mb-0">
                        Belum ada tugas yang dibuat. Silakan klik tombol di kanan atas untuk membuat tugas baru.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah / Kelas</th>
                                    <th>Judul Tugas</th>
                                    <th>Deadline</th>
                                    <th>Nilai Maksimal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($tugas as $t): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($t['nama_mk']) ?></strong><br>
                                        <small class="text-muted">TA: <?= esc($t['tahun_ajaran']) ?> (<?= ucfirst(esc($t['semester'])) ?>)</small>
                                    </td>
                                    <td>
                                        <strong><?= esc($t['judul']) ?></strong>
                                        <?php if(!empty($t['deskripsi'])): ?>
                                            <br><small class="text-muted"><?= esc($t['deskripsi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($t['deadline'])) ?></span>
                                    </td>
                                    <td><?= esc($t['max_nilai']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('dosen/tugas/detail/'.$t['id']) ?>" class="btn btn-info btn-xs"><i class="fas fa-eye"></i> Detail & Nilai</a>
                                        <form action="<?= base_url('dosen/tugas/delete/'.$t['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus tugas ini secara permanen?')">
                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Tugas -->
<div class="modal fade" id="modalTambahTugas" tabindex="-1" role="dialog" aria-labelledby="modalTambahTugasLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('dosen/tugas/store') ?>" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahTugasLabel">Tambah Tugas Baru</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Pilih Kelas / Mata Kuliah</label>
                        <select name="kelas_id" class="form-control select2" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_mk']) ?> (TA: <?= esc($k['tahun_ajaran']) ?> - <?= ucfirst(esc($k['semester'])) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Judul Tugas</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Tugas 1 - Membuat Resume">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Tugas</label>
                        <textarea name="deskripsi" class="form-control" rows="4" placeholder="Petunjuk pengerjaan tugas..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Batas Waktu (Deadline)</label>
                        <input type="datetime-local" name="deadline" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nilai Maksimal</label>
                        <input type="number" name="max_nilai" class="form-control" value="100" min="1" max="100" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
