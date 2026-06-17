<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Tambah Kelas</h3>
        </div>
        
        <form action="<?= base_url('admin/kelas/store') ?>" method="post">
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
                    <label for="mata_kuliah_id">Mata Kuliah</label>
                    <select class="form-control" id="mata_kuliah_id" name="mata_kuliah_id" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php foreach($matakuliah as $mk): ?>
                            <option value="<?= $mk['id'] ?>" <?= old('mata_kuliah_id') == $mk['id'] ? 'selected' : '' ?>><?= $mk['kode_mk'] ?> - <?= $mk['nama'] ?> (<?= $mk['nama_dosen'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tahun_ajaran">Tahun Ajaran</label>
                    <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" value="<?= old('tahun_ajaran') ?>" placeholder="Contoh: 2026/2027" required>
                </div>
                
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select class="form-control" id="semester" name="semester" required>
                        <option value="ganjil" <?= old('semester') == 'ganjil' ? 'selected' : '' ?>>Ganjil</option>
                        <option value="genap" <?= old('semester') == 'genap' ? 'selected' : '' ?>>Genap</option>
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('admin/kelas') ?>" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>
