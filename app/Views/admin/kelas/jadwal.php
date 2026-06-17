<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Form Tambah Jadwal -->
    <div class="col-md-4">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Tambah Pertemuan Baru</h3>
            </div>
            <form action="<?= base_url('admin/kelas/jadwal/store/'.$kelas['id']) ?>" method="post">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="pertemuan_ke">Pertemuan Ke</label>
                        <input type="number" name="pertemuan_ke" id="pertemuan_ke" class="form-control" value="<?= count($jadwal) + 1 ?>" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="tanggal">Tanggal Perkulihan</label>
                        <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="jam_mulai">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="jam_mulai" class="form-control" value="08:00" required>
                    </div>

                    <div class="form-group">
                        <label for="jam_selesai">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="jam_selesai" class="form-control" value="10:00" required>
                    </div>

                    <div class="form-group">
                        <label for="ruangan">Ruangan Kelas</label>
                        <input type="text" name="ruangan" id="ruangan" class="form-control" placeholder="Contoh: Ruang 202, Laboratorium" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block"><i class="fas fa-save"></i> Simpan Jadwal</button>
                    <a href="<?= base_url('admin/kelas') ?>" class="btn btn-default btn-block">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Jadwal Terbentuk -->
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Daftar Jadwal - <strong><?= esc($kelas['nama_mk']) ?></strong></h3>
            </div>
            <div class="card-body p-0">
                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="alert alert-success m-3">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pertemuan</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Ruangan</th>
                                <th class="text-center" style="width: 150px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jadwal as $row) : ?>
                            <tr>
                                <td><strong>Pertemuan Ke-<?= $row['pertemuan_ke'] ?></strong></td>
                                <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                <td><code><?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?></code></td>
                                <td><span class="badge badge-primary"><i class="fas fa-door-open mr-1"></i> <?= esc($row['ruangan']) ?></span></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-xs text-white" onclick="editJadwal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form action="<?= base_url('admin/kelas/jadwal/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal pertemuan ini?')">
                                        <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($jadwal)): ?>
                            <tr>
                                <td colspan="5" class="text-center p-4 text-muted">Belum ada jadwal yang diatur untuk kelas ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Jadwal -->
<div class="modal fade" id="modalEditJadwal" tabindex="-1" role="dialog" aria-labelledby="modalEditJadwalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="formEditJadwal" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditJadwalLabel">Edit Jadwal Pertemuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_pertemuan_ke">Pertemuan Ke</label>
                        <input type="number" name="pertemuan_ke" id="edit_pertemuan_ke" class="form-control" required min="1">
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_tanggal">Tanggal Perkulihan</label>
                        <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_jam_mulai">Jam Mulai</label>
                        <input type="time" name="jam_mulai" id="edit_jam_mulai" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_jam_selesai">Jam Selesai</label>
                        <input type="time" name="jam_selesai" id="edit_jam_selesai" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_ruangan">Ruangan Kelas</label>
                        <input type="text" name="ruangan" id="edit_ruangan" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editJadwal(data) {
    $('#formEditJadwal').attr('action', '<?= base_url('admin/kelas/jadwal/update/') ?>' + data.id);
    $('#edit_pertemuan_ke').val(data.pertemuan_ke);
    $('#edit_tanggal').val(data.tanggal);
    
    // Format time to HH:MM (remove seconds if present)
    let start = data.jam_mulai.substring(0, 5);
    let end = data.jam_selesai.substring(0, 5);
    
    $('#edit_jam_mulai').val(start);
    $('#edit_jam_selesai').val(end);
    $('#edit_ruangan').val(data.ruangan);
    
    $('#modalEditJadwal').modal('show');
}
</script>
<?= $this->endSection() ?>
