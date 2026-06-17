<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-book-open mr-2"></i>Materi Kuliah Anda</h3>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <?php if (empty($materi)): ?>
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-book mb-3" style="font-size:60px;"></i>
                        <h4>Belum Ada Materi</h4>
                        <p>Dosen Anda belum mengunggah materi untuk kelas yang Anda ikuti.</p>
                    </div>
                <?php else: ?>
                    <!-- Filter per Mata Kuliah -->
                    <div class="row mb-3">
                        <div class="col-md-5">
                            <select id="filterKelas" class="form-control form-control-sm">
                                <option value="">-- Semua Mata Kuliah --</option>
                                <?php foreach ($kelas as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_mk']) ?> (<?= esc($k['tahun_ajaran']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="materiContainer">
                        <?php foreach ($materi as $m):
                            $ext = strtolower(pathinfo($m['file_path'], PATHINFO_EXTENSION));
                            if ($m['tipe'] == 'video') {
                                $icon = 'fa-file-video'; $color = 'text-danger'; $badge = 'badge-danger'; $label = 'Video';
                            } elseif (in_array($ext, ['pdf'])) {
                                $icon = 'fa-file-pdf'; $color = 'text-danger'; $badge = 'badge-primary'; $label = 'PDF';
                            } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                $icon = 'fa-file-powerpoint'; $color = 'text-warning'; $badge = 'badge-warning'; $label = 'PPT';
                            } elseif (in_array($ext, ['doc', 'docx'])) {
                                $icon = 'fa-file-word'; $color = 'text-primary'; $badge = 'badge-info'; $label = 'Word';
                            } else {
                                $icon = 'fa-file'; $color = 'text-secondary'; $badge = 'badge-secondary'; $label = strtoupper($ext);
                            }
                        ?>
                        <div class="col-md-4 col-sm-6 materi-item" data-kelas="<?= $m['kelas_id'] ?>">
                            <div class="card card-outline card-secondary h-100">
                                <div class="card-body text-center py-4">
                                    <i class="far <?= $icon ?> <?= $color ?> mb-3" style="font-size: 50px;"></i>
                                    <h5 class="card-title"><?= esc($m['judul']) ?></h5>
                                    <p class="text-muted small mb-1"><strong><?= esc($m['nama_mk']) ?></strong></p>
                                    <p class="text-muted small mb-2">TA: <?= esc($m['tahun_ajaran']) ?> (<?= ucfirst($m['semester']) ?>)</p>
                                    <?php if ($m['deskripsi']): ?>
                                        <p class="text-muted small"><?= esc($m['deskripsi']) ?></p>
                                    <?php endif; ?>
                                    <span class="badge <?= $badge ?> mb-3"><?= $label ?></span>
                                </div>
                                <div class="card-footer d-flex gap-2">
                                    <?php if ($m['tipe'] == 'video' || in_array($ext, ['pdf', 'png', 'jpg', 'jpeg'])): ?>
                                        <button class="btn btn-info btn-sm flex-fill" onclick="previewMateri('<?= base_url($m['file_path']) ?>', '<?= esc($m['judul']) ?>', '<?= $ext ?>')">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?= base_url($m['file_path']) ?>" class="btn btn-primary btn-sm flex-fill" download>
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="modalPreviewMateri" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewMateriLabel">Preview Materi</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body p-0" id="previewMateriBody" style="min-height: 450px;"></div>
            <div class="modal-footer">
                <a href="#" id="btnDownloadPreview" class="btn btn-primary" download><i class="fas fa-download"></i> Download</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
// Filter materi per mata kuliah
document.getElementById('filterKelas') && document.getElementById('filterKelas').addEventListener('change', function() {
    var val = this.value;
    document.querySelectorAll('.materi-item').forEach(function(el) {
        el.style.display = (!val || el.dataset.kelas == val) ? '' : 'none';
    });
});

function previewMateri(fileUrl, title, ext) {
    ext = ext.toLowerCase();
    document.getElementById('modalPreviewMateriLabel').textContent = 'Preview: ' + title;
    document.getElementById('btnDownloadPreview').href = fileUrl;
    var body = document.getElementById('previewMateriBody');
    if (ext === 'pdf') {
        body.innerHTML = '<iframe src="' + fileUrl + '" style="width:100%;height:500px;border:none;"></iframe>';
    } else if (['png','jpg','jpeg','gif'].includes(ext)) {
        body.innerHTML = '<div class="text-center p-3"><img src="' + fileUrl + '" class="img-fluid" style="max-height:500px;"></div>';
    } else if (ext === 'mp4') {
        body.innerHTML = '<div class="text-center p-3"><video controls style="max-width:100%;height:450px;"><source src="' + fileUrl + '" type="video/mp4">Browser tidak mendukung tag video.</video></div>';
    }
    $('#modalPreviewMateri').modal('show');
}
</script>
<?= $this->endSection() ?>
