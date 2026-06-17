<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-warning card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar <?= strtoupper($tipe) ?> Kuliah</h3>
                <button type="button" class="btn btn-warning btn-sm ml-auto text-white" data-toggle="modal" data-target="#modalTambahKuis">
                    <i class="fas fa-plus"></i> Buat <?= strtoupper($tipe) ?> Baru
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

                <?php if(empty($kuis)): ?>
                    <div class="alert alert-info text-center mb-0">
                        Belum ada kuis yang dibuat. Silakan klik tombol di kanan atas untuk membuat kuis baru.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah / Kelas</th>
                                    <th>Judul Kuis</th>
                                    <th>Durasi</th>
                                    <th>Deadline</th>
                                    <th>Batas Ujian</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($kuis as $k): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($k['nama_mk']) ?></strong><br>
                                        <small class="text-muted">TA: <?= esc($k['tahun_ajaran']) ?> (<?= ucfirst(esc($k['semester'])) ?>)</small>
                                    </td>
                                    <td><strong><?= esc($k['judul']) ?></strong></td>
                                    <td><?= esc($k['durasi_menit']) ?> Menit</td>
                                    <td>
                                        <span class="badge badge-info"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($k['deadline'])) ?></span>
                                    </td>
                                    <td><?= esc($k['max_attempt']) ?>x Percobaan</td>
                                    <td class="text-center">
                                        <a href="<?= base_url('dosen/kuis/detail/'.$k['id']) ?>" class="btn btn-warning btn-xs text-white"><i class="fas fa-tasks"></i> Kelola Soal</a>
                                        <button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#modalEditKuis<?= $k['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                                        <form action="<?= base_url('dosen/kuis/delete/'.$k['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kuis ini secara permanen?')">
                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Edit Kuis -->
                                <div class="modal fade" id="modalEditKuis<?= $k['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditKuisLabel<?= $k['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('dosen/kuis/update/'.$k['id']) ?>" method="post">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalEditKuisLabel<?= $k['id'] ?>">Edit <?= strtoupper($tipe) ?>: <?= esc($k['judul']) ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="form-group">
                                                        <label>Judul <?= strtoupper($tipe) ?></label>
                                                        <input type="text" name="judul" class="form-control" value="<?= esc($k['judul']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Durasi (Menit)</label>
                                                        <input type="number" name="durasi_menit" class="form-control" value="<?= esc($k['durasi_menit']) ?>" required min="1">
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Batas Waktu Pengerjaan (Deadline)</label>
                                                        <input type="datetime-local" name="deadline" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($k['deadline'])) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Maksimal Percobaan</label>
                                                        <input type="number" name="max_attempt" class="form-control" value="<?= esc($k['max_attempt']) ?>" min="1" required>
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

<!-- Modal Tambah Kuis -->
<div class="modal fade" id="modalTambahKuis" tabindex="-1" role="dialog" aria-labelledby="modalTambahKuisLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('dosen/kuis/store') ?>" method="post">
                <input type="hidden" name="tipe" value="<?= esc($tipe) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTambahKuisLabel">Buat <?= strtoupper($tipe) ?> Baru</h5>
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
                        <label>Judul <?= strtoupper($tipe) ?></label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: <?= strtoupper($tipe) ?> Semester Ganjil">
                    </div>
                    <div class="form-group">
                        <label>Durasi (Menit)</label>
                        <input type="number" name="durasi_menit" class="form-control" required placeholder="Contoh: 90" min="1">
                    </div>
                    <div class="form-group">
                        <label>Batas Waktu Pengerjaan (Deadline)</label>
                        <input type="datetime-local" name="deadline" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Maksimal Percobaan</label>
                        <input type="number" name="max_attempt" class="form-control" value="1" min="1" required>
                        <small class="text-muted">Berapa kali mahasiswa dapat mencoba <?= $tipe ?> ini.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Buat <?= strtoupper($tipe) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
