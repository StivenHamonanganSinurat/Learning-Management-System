<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Dropdown Pilih Kelas -->
    <div class="col-md-12 mb-3">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 font-weight-bold">Pilih Kelas untuk Forum Diskusi:</h5>
                    </div>
                    <div class="col-md-6">
                        <select id="selectKelasForum" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach($kelas as $k): ?>
                                <option value="<?= $k['id'] ?>" <?= (isset($selected_kelas) && $selected_kelas['id'] == $k['id']) ? 'selected' : '' ?>>
                                    <?= esc($k['nama_mk']) ?> (TA: <?= esc($k['tahun_ajaran']) ?> - <?= ucfirst(esc($k['semester'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="col-md-12">
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('success') ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="col-md-12">
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?= session()->getFlashdata('error') ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($selected_kelas)): ?>
        <div class="col-md-12">
            <div class="card card-outline card-warning">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title font-weight-bold">Forum Diskusi Kelas: <?= esc($selected_kelas['nama_mk']) ?></h3>
                    <button type="button" class="btn btn-warning btn-sm ml-auto text-white" data-toggle="modal" data-target="#modalTambahTopik">
                        <i class="fas fa-plus"></i> Mulai Diskusi Baru
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($topik)): ?>
                        <div class="alert alert-info text-center py-5">
                            <i class="fas fa-comments mb-3" style="font-size: 60px;"></i>
                            <h4>Belum Ada Diskusi</h4>
                            <p>Silakan buat topik diskusi pertama Anda dengan tombol di kanan atas.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Topik Diskusi</th>
                                        <th>Pembuat</th>
                                        <th>Tanggal Dibuat</th>
                                        <th class="text-center">Tanggapan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topik as $t): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('dosen/forum/detail/' . $t['id']) ?>" class="font-weight-bold text-dark" style="font-size: 1.1em;">
                                                <?= esc($t['judul']) ?>
                                            </a>
                                            <p class="text-muted mb-0 small"><?= esc(substr(strip_tags($t['konten']), 0, 100)) ?>...</p>
                                        </td>
                                        <td>
                                            <strong><?= esc($t['nama_pembuat']) ?></strong><br>
                                            <span class="badge badge-<?= $t['role_pembuat'] == 'dosen' ? 'warning' : 'info' ?>"><?= ucfirst(esc($t['role_pembuat'])) ?></span>
                                        </td>
                                        <td><small class="text-muted"><?= date('d M Y H:i', strtotime($t['created_at'])) ?></small></td>
                                        <td class="text-center">
                                            <span class="badge badge-secondary" style="font-size: 14px; padding: 5px 10px;"><i class="far fa-comment-dots mr-1"></i> <?= $t['jumlah_balasan'] ?></span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('dosen/forum/detail/' . $t['id']) ?>" class="btn btn-primary btn-xs"><i class="fas fa-comments"></i> Masuk</a>
                                            <?php if(session()->get('id') == $t['pembuat_id']): ?>
                                                <form action="<?= base_url('dosen/forum/delete/' . $t['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus topik diskusi ini beserta seluruh balasannya?')">
                                                    <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                                </form>
                                            <?php endif; ?>
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

        <!-- Modal Tambah Topik -->
        <div class="modal fade" id="modalTambahTopik" tabindex="-1" role="dialog" aria-labelledby="modalTambahTopikLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="<?= base_url('dosen/forum/create/' . $selected_kelas['id']) ?>" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title font-weight-bold" id="modalTambahTopikLabel">Buat Topik Diskusi Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Judul Diskusi / Topik Utama</label>
                                <input type="text" name="judul" class="form-control" placeholder="Contoh: Diskusi Mengenai Pertemuan 3" required>
                            </div>
                            <div class="form-group">
                                <label>Isi Topik / Deskripsi Pertanyaan</label>
                                <textarea name="konten" class="form-control" rows="8" placeholder="Tulis instruksi atau bahan diskusi disini..." required></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-warning text-white">Mulai Diskusi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info text-center p-5">
                <i class="fas fa-comments mb-3" style="font-size: 60px;"></i>
                <h4>Silakan Pilih Kelas</h4>
                <p>Pilih kelas dari dropdown di atas untuk mengelola forum diskusi mahasiswa.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('selectKelasForum');
    if (select) {
        select.addEventListener('change', function() {
            var val = this.value;
            if (val) {
                window.location.href = '<?= base_url('dosen/forum?kelas_id=') ?>' + val;
            } else {
                window.location.href = '<?= base_url('dosen/forum') ?>';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
