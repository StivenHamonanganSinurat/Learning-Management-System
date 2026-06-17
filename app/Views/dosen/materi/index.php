<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary card-outline">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Materi Kuliah yang Anda Unggah</h3>
                <button type="button" class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalUploadMateri">
                    <i class="fas fa-upload"></i> Upload Materi Baru
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

                <?php if(empty($materi)): ?>
                    <div class="alert alert-info text-center mb-0">
                        Belum ada materi yang Anda unggah. Silakan klik tombol di kanan atas untuk mengunggah materi baru.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Kuliah / Kelas</th>
                                    <th>Judul Materi</th>
                                    <th>Tipe</th>
                                    <th>Tanggal Upload</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach($materi as $m): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= esc($m['nama_mk']) ?></strong><br>
                                        <small class="text-muted">TA: <?= esc($m['tahun_ajaran']) ?> (<?= ucfirst(esc($m['semester'])) ?>)</small>
                                    </td>
                                    <td>
                                        <?= esc($m['judul']) ?>
                                        <?php if($m['deskripsi']): ?>
                                            <br><small class="text-muted"><?= esc($m['deskripsi']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $ext = strtolower(pathinfo($m['file_path'], PATHINFO_EXTENSION));
                                        if($m['tipe'] == 'video'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-video"></i> Video (.<?= $ext ?>)</span>
                                        <?php else: ?>
                                            <?php if(in_array($ext, ['pdf'])): ?>
                                                <span class="badge badge-primary"><i class="fas fa-file-pdf"></i> PDF</span>
                                            <?php elseif(in_array($ext, ['ppt', 'pptx'])): ?>
                                                <span class="badge badge-warning text-white"><i class="fas fa-file-powerpoint"></i> PPT</span>
                                            <?php elseif(in_array($ext, ['doc', 'docx'])): ?>
                                                <span class="badge badge-info"><i class="fas fa-file-word"></i> Word</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary"><i class="fas fa-file"></i> File (.<?= $ext ?>)</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d M Y H:i', strtotime($m['created_at'])) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-info btn-xs" onclick="previewMateri('<?= base_url($m['file_path']) ?>', '<?= esc($m['judul']) ?>', '<?= $ext ?>')"><i class="fas fa-eye"></i> Lihat File</button>
                                        <a href="<?= base_url($m['file_path']) ?>" target="_blank" class="btn btn-primary btn-xs" download><i class="fas fa-download"></i> Download</a>
                                        <button type="button" class="btn btn-warning btn-xs text-white" data-toggle="modal" data-target="#modalEditMateri<?= $m['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                                        <form action="<?= base_url('dosen/materi/delete/'.$m['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus materi ini secara permanen?')">
                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Edit Materi -->
                                <div class="modal fade" id="modalEditMateri<?= $m['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditMateriLabel<?= $m['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="<?= base_url('dosen/materi/update/'.$m['id']) ?>" method="post" enctype="multipart/form-data">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalEditMateriLabel<?= $m['id'] ?>">Edit Materi: <?= esc($m['judul']) ?></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    <div class="form-group">
                                                        <label>Judul Materi</label>
                                                        <input type="text" name="judul" class="form-control" value="<?= esc($m['judul']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Tipe</label>
                                                        <select name="tipe" class="form-control" required>
                                                            <option value="file" <?= $m['tipe'] == 'file' ? 'selected' : '' ?>>File (PDF/Word/PPT)</option>
                                                            <option value="video" <?= $m['tipe'] == 'video' ? 'selected' : '' ?>>Video (MP4)</option>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Deskripsi (Opsional)</label>
                                                        <textarea name="deskripsi" class="form-control" rows="3"><?= esc($m['deskripsi']) ?></textarea>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Ganti File (Biarkan kosong jika tidak ingin mengubah file)</label>
                                                        <input type="file" name="file_materi" class="form-control-file">
                                                        <small class="text-muted">Maksimal 5MB. Ekstensi: pdf, doc, docx, ppt, pptx, mp4.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
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

<!-- Modal Upload Materi -->
<div class="modal fade" id="modalUploadMateri" tabindex="-1" role="dialog" aria-labelledby="modalUploadMateriLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= base_url('dosen/materi/store') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalUploadMateriLabel">Upload Materi Baru</h5>
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
                        <label>Judul Materi</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Pertemuan 1 - Pengantar">
                    </div>
                    <div class="form-group">
                        <label>Tipe</label>
                        <select name="tipe" class="form-control" required>
                            <option value="file">File (PDF/Word/PPT)</option>
                            <option value="video">Video (MP4)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi (Opsional)</label>
                        <textarea name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat materi..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Pilih File</label>
                        <input type="file" name="file_materi" class="form-control-file" required>
                        <small class="text-muted">Maksimal 5MB.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload"></i> Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Materi -->
<div class="modal fade" id="modalPreviewMateri" tabindex="-1" role="dialog" aria-labelledby="modalPreviewMateriLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPreviewMateriLabel">Preview Materi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" id="previewMateriBody" style="min-height: 450px;">
                <!-- Content injected by JS -->
            </div>
            <div class="modal-footer">
                <a href="#" id="btnDownloadPreview" class="btn btn-primary" download><i class="fas fa-download"></i> Download File</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
function previewMateri(fileUrl, title, ext) {
    ext = ext.toLowerCase();
    $('#modalPreviewMateriLabel').text('Preview: ' + title);
    $('#btnDownloadPreview').attr('href', fileUrl);
    
    let html = '';
    if (ext === 'pdf') {
        html = `<iframe src="${fileUrl}" style="width:100%; height:500px; border:none;"></iframe>`;
    } else if (['png', 'jpg', 'jpeg', 'gif'].includes(ext)) {
        html = `<div class="text-center p-3"><img src="${fileUrl}" class="img-fluid" style="max-height:500px;"></div>`;
    } else if (ext === 'mp4') {
        html = `<div class="text-center p-3"><video controls style="max-width:100%; height:450px;"><source src="${fileUrl}" type="video/mp4">Browser Anda tidak mendukung tag video.</video></div>`;
    } else {
        let iconClass = 'fa-file';
        let colorClass = 'text-secondary';
        let typeName = ext.toUpperCase() + ' Document';

        if (['ppt', 'pptx'].includes(ext)) {
            iconClass = 'fa-file-powerpoint';
            colorClass = 'text-warning';
            typeName = 'PowerPoint Presentation';
        } else if (['doc', 'docx'].includes(ext)) {
            iconClass = 'fa-file-word';
            colorClass = 'text-primary';
            typeName = 'Word Document';
        } else if (['xls', 'xlsx'].includes(ext)) {
            iconClass = 'fa-file-excel';
            colorClass = 'text-success';
            typeName = 'Excel Spreadsheet';
        }

        html = `
            <div class="text-center p-5">
                <i class="far ${iconClass} ${colorClass} mb-3" style="font-size: 80px;"></i>
                <h4>File ini adalah dokumen <strong>${typeName} (.${ext.toUpperCase()})</strong></h4>
                <p class="text-muted">Preview langsung untuk tipe dokumen ini tidak didukung di web browser local. Silakan unduh file menggunakan tombol di bawah ini untuk membacanya.</p>
                <a href="${fileUrl}" class="btn btn-success btn-lg mt-2" download><i class="fas fa-download"></i> Unduh File</a>
            </div>
        `;
    }
    $('#previewMateriBody').html(html);
    $('#modalPreviewMateri').modal('show');
}
</script>
<?= $this->endSection() ?>
