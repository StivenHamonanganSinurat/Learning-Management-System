<?php
$activeTab = service('request')->getGet('tab') ?? 'materi';
if (!in_array($activeTab, ['materi', 'tugas', 'jadwal'])) {
    $activeTab = 'materi';
}
?>

<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
    <div class="row">
        <!-- Info Kelas -->
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center"><?= $kelas['kode_mk'] ?></h3>
                    <p class="text-muted text-center"><?= $kelas['nama_mk'] ?></p>

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Tahun Ajaran</b> <a class="float-right"><?= $kelas['tahun_ajaran'] ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Semester</b> <a class="float-right"><?= ucfirst($kelas['semester']) ?></a>
                        </li>
                        <li class="list-group-item">
                            <b>Peserta</b> <a class="float-right"><?= $peserta ?> Mhs</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Upload Materi Form -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Upload Materi Baru</h3>
                </div>
                <form action="<?= base_url('dosen/materi/store/'.$kelas['id']) ?>" method="post" enctype="multipart/form-data">
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
                            <textarea name="deskripsi" class="form-control" rows="2" placeholder="Penjelasan singkat materi"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Pilih File</label>
                            <input type="file" name="file_materi" class="form-control-file" required>
                            <small class="text-muted">Maksimal 5MB.</small>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success w-100"><i class="fas fa-upload"></i> Upload Materi</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Daftar Materi & Tugas -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link <?= $activeTab == 'materi' ? 'active' : '' ?>" href="#materi" data-toggle="tab">Materi Kuliah</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeTab == 'tugas' ? 'active' : '' ?>" href="#tugas" data-toggle="tab">Tugas & Kuis</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeTab == 'jadwal' ? 'active' : '' ?>" href="#jadwal" data-toggle="tab">Jadwal Kuliah</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Materi -->
                        <div class="<?= $activeTab == 'materi' ? 'active' : '' ?> tab-pane" id="materi">
                            <?php if (session()->getFlashdata('success')) : ?>
                                <div class="alert alert-success">
                                    <?= session()->getFlashdata('success') ?>
                                </div>
                            <?php endif; ?>

                            <?php if(empty($materi)): ?>
                                <div class="alert alert-info text-center">
                                    Belum ada materi yang diunggah untuk kelas ini.
                                </div>
                            <?php else: ?>
                                <div class="timeline timeline-inverse">
                                    <?php foreach($materi as $m): ?>
                                    <div>
                                        <?php 
                                        $ext = strtolower(pathinfo($m['file_path'], PATHINFO_EXTENSION));
                                        if($m['tipe'] == 'video'): ?>
                                            <i class="fas fa-video bg-danger"></i>
                                        <?php else: ?>
                                            <?php if(in_array($ext, ['pdf'])): ?>
                                                <i class="fas fa-file-pdf bg-primary"></i>
                                            <?php elseif(in_array($ext, ['ppt', 'pptx'])): ?>
                                                <i class="fas fa-file-powerpoint bg-warning"></i>
                                            <?php elseif(in_array($ext, ['doc', 'docx'])): ?>
                                                <i class="fas fa-file-word bg-info"></i>
                                            <?php else: ?>
                                                <i class="fas fa-file bg-secondary"></i>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <div class="timeline-item">
                                            <span class="time"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($m['created_at'])) ?></span>

                                            <h3 class="timeline-header"><a href="#"><?= $m['judul'] ?></a></h3>

                                            <div class="timeline-body">
                                                <?= $m['deskripsi'] ?>
                                            </div>
                                            <div class="timeline-footer">
                                                <button type="button" class="btn btn-info btn-sm" onclick="previewMateri('<?= base_url($m['file_path']) ?>', '<?= esc($m['judul']) ?>', '<?= $ext ?>')"><i class="fas fa-eye"></i> Lihat File</button>
                                                <a href="<?= base_url($m['file_path']) ?>" target="_blank" class="btn btn-primary btn-sm" download><i class="fas fa-download"></i> Download</a>
                                                <button type="button" class="btn btn-warning btn-sm text-white" data-toggle="modal" data-target="#modalEditMateriTimeline<?= $m['id'] ?>"><i class="fas fa-edit"></i> Edit</button>
                                                <form action="<?= base_url('dosen/materi/delete/'.$m['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus materi ini secara permanen?')">
                                                    <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Edit Materi -->
                                    <div class="modal fade" id="modalEditMateriTimeline<?= $m['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="modalEditMateriTimelineLabel<?= $m['id'] ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="<?= base_url('dosen/materi/update/'.$m['id']) ?>" method="post" enctype="multipart/form-data">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="modalEditMateriTimelineLabel<?= $m['id'] ?>">Edit Materi: <?= esc($m['judul']) ?></h5>
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
                                    <div>
                                        <i class="far fa-clock bg-gray"></i>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tab Tugas & Kuis -->
                        <div class="<?= $activeTab == 'tugas' ? 'active' : '' ?> tab-pane" id="tugas">
                            <!-- Section Tugas -->
                            <div class="card card-outline card-primary mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 font-weight-bold">Daftar Tugas</h5>
                                    <button type="button" class="btn btn-primary btn-sm ml-auto" data-toggle="modal" data-target="#modalTambahTugas">
                                        <i class="fas fa-plus"></i> Tambah Tugas
                                    </button>
                                </div>
                                <div class="card-body">
                                    <?php if(empty($tugas)): ?>
                                        <div class="alert alert-info text-center mb-0">
                                            Belum ada tugas yang dibuat untuk kelas ini.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Judul Tugas</th>
                                                        <th>Deadline</th>
                                                        <th>Nilai Maksimal</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($tugas as $t): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= esc($t['judul']) ?></strong>
                                                            <?php if(!empty($t['deskripsi'])): ?>
                                                                <br><small class="text-muted"><?= esc($t['deskripsi']) ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-info"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($t['deadline'])) ?></span>
                                                        </td>
                                                        <td><?= $t['max_nilai'] ?></td>
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

                            <!-- Section Kuis -->
                            <div class="card card-outline card-warning">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 font-weight-bold">Daftar Kuis</h5>
                                    <button type="button" class="btn btn-warning btn-sm ml-auto text-white" data-toggle="modal" data-target="#modalTambahKuis">
                                        <i class="fas fa-plus"></i> Buat Kuis Baru
                                    </button>
                                </div>
                                <div class="card-body">
                                    <?php if(empty($kuis)): ?>
                                        <div class="alert alert-info text-center mb-0">
                                            Belum ada kuis yang dibuat untuk kelas ini.
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Judul Kuis</th>
                                                        <th>Durasi</th>
                                                        <th>Deadline</th>
                                                        <th>Batas Ujian</th>
                                                        <th class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach($kuis as $k): ?>
                                                    <tr>
                                                        <td>
                                                            <strong><?= esc($k['judul']) ?></strong>
                                                        </td>
                                                        <td><?= $k['durasi_menit'] ?> Menit</td>
                                                        <td>
                                                            <span class="badge badge-info"><i class="far fa-clock"></i> <?= date('d M Y H:i', strtotime($k['deadline'])) ?></span>
                                                        </td>
                                                        <td><?= $k['max_attempt'] ?>x Percobaan</td>
                                                        <td class="text-center">
                                                            <a href="<?= base_url('dosen/kuis/detail/'.$k['id']) ?>" class="btn btn-warning btn-xs text-white"><i class="fas fa-tasks"></i> Kelola Soal</a>
                                                            <form action="<?= base_url('dosen/kuis/delete/'.$k['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kuis ini secara permanen?')">
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
                        
                        <!-- Tab Jadwal -->
                        <div class="<?= $activeTab == 'jadwal' ? 'active' : '' ?> tab-pane" id="jadwal">
                            <div class="card card-outline card-success mb-0">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0 font-weight-bold">Jadwal Perkuliahan & Ruangan</h5>
                                    <button type="button" class="btn btn-success btn-sm ml-auto" data-toggle="modal" data-target="#modalTambahJadwal">
                                        <i class="fas fa-plus"></i> Tambah Jadwal Pertemuan
                                    </button>
                                </div>
                                <div class="card-body">
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
                                                    <td><code><?= substr($row['jam_mulai'], 0, 5) ?> -  <?= substr($row['jam_selesai'], 0, 5) ?></code></td>
                                                    <td><span class="badge badge-primary"><i class="fas fa-door-open mr-1"></i> <?= esc($row['ruangan']) ?></span></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-warning btn-xs text-white" onclick="editJadwal(<?= htmlspecialchars(json_encode($row)) ?>)">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </button>
                                                        <form action="<?= base_url('dosen/kelas/jadwal/delete/'.$row['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal pertemuan ini?')">
                                                            <button type="submit" class="btn btn-danger btn-xs"><i class="fas fa-trash"></i> Hapus</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                
                                                <?php if (empty($jadwal)): ?>
                                                <tr>
                                                    <td colspan="5" class="text-center p-4 text-muted">Belum ada jadwal pertemuan yang dibuat untuk kelas ini.</td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Tugas -->
    <div class="modal fade" id="modalTambahTugas" tabindex="-1" role="dialog" aria-labelledby="modalTambahTugasLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= base_url('dosen/tugas/store/'.$kelas['id']) ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahTugasLabel">Tambah Tugas Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
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

    <!-- Modal Tambah Kuis -->
    <div class="modal fade" id="modalTambahKuis" tabindex="-1" role="dialog" aria-labelledby="modalTambahKuisLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= base_url('dosen/kuis/store/'.$kelas['id']) ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahKuisLabel">Buat Kuis Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Judul Kuis</label>
                            <input type="text" name="judul" class="form-control" required placeholder="Contoh: Kuis 1 - Pengantar Basis Data">
                        </div>
                        <div class="form-group">
                            <label>Durasi (Menit)</label>
                            <input type="number" name="durasi_menit" class="form-control" required placeholder="Contoh: 30" min="1">
                        </div>
                        <div class="form-group">
                            <label>Batas Waktu Pengerjaan (Deadline)</label>
                            <input type="datetime-local" name="deadline" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Maksimal Percobaan</label>
                            <input type="number" name="max_attempt" class="form-control" value="1" min="1" required>
                            <small class="text-muted">Berapa kali mahasiswa dapat mencoba kuis ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-white">Buat Kuis</button>
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

    function editJadwal(data) {
        $('#formEditJadwal').attr('action', '<?= base_url('dosen/kelas/jadwal/update/') ?>' + data.id);
        $('#edit_pertemuan_ke').val(data.pertemuan_ke);
        $('#edit_tanggal').val(data.tanggal);
        
        let start = data.jam_mulai.substring(0, 5);
        let end = data.jam_selesai.substring(0, 5);
        
        $('#edit_jam_mulai').val(start);
        $('#edit_jam_selesai').val(end);
        $('#edit_ruangan').val(data.ruangan);
        
        $('#modalEditJadwal').modal('show');
    }
    </script>

    <!-- Modal Tambah Jadwal -->
    <div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="<?= base_url('dosen/kelas/jadwal/store/'.$kelas['id']) ?>" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahJadwalLabel">Tambah Jadwal Pertemuan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pertemuan Ke</label>
                            <input type="number" name="pertemuan_ke" class="form-control" value="<?= count($jadwal) + 1 ?>" required min="1">
                        </div>
                        
                        <div class="form-group">
                            <label>Tanggal Perkulihan</label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" value="08:00" required>
                        </div>

                        <div class="form-group">
                            <label>Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" value="10:00" required>
                        </div>

                        <div class="form-group">
                            <label>Ruangan Kelas</label>
                            <input type="text" name="ruangan" class="form-control" placeholder="Contoh: Ruang 202, Laboratorium" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Jadwal</button>
                    </div>
                </form>
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
<?= $this->endSection() ?>
