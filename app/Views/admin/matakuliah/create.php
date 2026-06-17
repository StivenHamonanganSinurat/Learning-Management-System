<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">Tambah Mata Kuliah</h3>
        </div>
        
        <form action="<?= base_url('admin/matakuliah/store') ?>" method="post">
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
                    <label for="kode_mk">Kode Mata Kuliah</label>
                    <input type="text" class="form-control" id="kode_mk" name="kode_mk" value="<?= old('kode_mk') ?>" placeholder="Contoh: MK001" required>
                </div>
                
                <div class="form-group">
                    <label for="nama">Nama Mata Kuliah</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= old('nama') ?>" placeholder="Contoh: Algoritma dan Pemrograman" required>
                </div>

                <div class="form-group">
                    <label for="sks">SKS</label>
                    <input type="number" class="form-control" id="sks" name="sks" value="<?= old('sks') ?>" placeholder="Contoh: 3" required>
                </div>

                <div class="form-group">
                    <label for="semester">Semester</label>
                    <input type="number" class="form-control" id="semester" name="semester" value="<?= old('semester') ?>" placeholder="Contoh: 1" required>
                </div>

                <div class="form-group">
                    <label for="prodi_id">Program Studi</label>
                    <select class="form-control" id="prodi_id" name="prodi_id" required>
                        <option value="">-- Pilih Program Studi --</option>
                        <?php foreach($prodi as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= old('prodi_id') == $p['id'] ? 'selected' : '' ?>><?= $p['nama_prodi'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="dosen_id">Dosen Pengampu</label>
                    <select class="form-control" id="dosen_id" name="dosen_id" required>
                        <option value="">-- Pilih Dosen --</option>
                        <?php foreach($dosen as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= old('dosen_id') == $d['id'] ? 'selected' : '' ?>><?= $d['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= base_url('admin/matakuliah') ?>" class="btn btn-default">Batal</a>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>
