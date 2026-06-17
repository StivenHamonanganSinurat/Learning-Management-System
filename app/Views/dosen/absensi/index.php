<?php
$activeTab = service('request')->getGet('tab') ?? 'absensi';
if (!in_array($activeTab, ['absensi', 'nilai'])) {
    $activeTab = 'absensi';
}
?>

<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Dropdown Pilih Kelas -->
    <div class="col-md-12 mb-3">
        <div class="card card-outline card-primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 font-weight-bold">Pilih Kelas / Mata Kuliah untuk Dikelola:</h5>
                    </div>
                    <div class="col-md-6">
                        <select id="selectKelas" class="form-control">
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
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="col-md-12">
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($selected_kelas)): ?>
        <!-- Kelola Absensi & Nilai Konten -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link <?= $activeTab == 'absensi' ? 'active' : '' ?>" href="#tab_absensi" data-toggle="tab">Absensi Kelas</a></li>
                        <li class="nav-item"><a class="nav-link <?= $activeTab == 'nilai' ? 'active' : '' ?>" href="#tab_nilai" data-toggle="tab">Rekap & Input Nilai Akhir</a></li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Tab Absensi -->
                        <div class="tab-pane <?= $activeTab == 'absensi' ? 'active' : '' ?>" id="tab_absensi">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Pertemuan Absensi</h4>
                                <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalTambahAbsensi">
                                    <i class="fas fa-plus"></i> Input Absensi Baru
                                </button>
                            </div>

                            <?php if(empty($absensi)): ?>
                                <div class="alert alert-info text-center mb-0">
                                    Belum ada catatan absensi untuk kelas ini. Silakan klik tombol di kanan atas untuk membuat pertemuan absensi baru.
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead>
                                            <tr>
                                                <th>Pertemuan Ke</th>
                                                <th>Tanggal</th>
                                                <th>Hadir / Sakit / Izin / Alpha</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($absensi as $abs): ?>
                                            <?php 
                                                // Hitung statistik singkat kehadiran
                                                $db = \Config\Database::connect();
                                                $stats = $db->table('detail_absensi')
                                                            ->select('status, count(id) as total')
                                                            ->where('absensi_id', $abs['id'])
                                                            ->groupBy('status')
                                                            ->get()->getResultArray();
                                                $stat_map = ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpha' => 0];
                                                foreach($stats as $row) {
                                                    $stat_map[$row['status']] = $row['total'];
                                                }
                                            ?>
                                            <tr>
                                                <td><strong>Pertemuan Ke-<?= $abs['pertemuan_ke'] ?></strong></td>
                                                <td><?= date('d M Y', strtotime($abs['tanggal'])) ?></td>
                                                <td>
                                                    <span class="badge badge-success">Hadir: <?= $stat_map['hadir'] ?></span>
                                                    <span class="badge badge-warning">Izin: <?= $stat_map['izin'] ?></span>
                                                    <span class="badge badge-info">Sakit: <?= $stat_map['sakit'] ?></span>
                                                    <span class="badge badge-danger">Alpha: <?= $stat_map['alpha'] ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-warning btn-xs text-white" onclick="editAbsensi(<?= $abs['id'] ?>)"><i class="fas fa-edit"></i> Edit</button>
                                                    <form action="<?= base_url('dosen/absensi/delete/'.$abs['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus absensi pertemuan ini?')">
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

                         <!-- Tab Nilai -->
                        <div class="tab-pane <?= $activeTab == 'nilai' ? 'active' : '' ?>" id="tab_nilai">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="mb-0">Daftar Nilai Akhir Mahasiswa</h4>
                                <div class="ml-auto">
                                    <form action="<?= base_url('dosen/nilai/sync/'.$selected_kelas['id']) ?>" method="post" class="d-inline">
                                        <button type="submit" class="btn btn-info btn-sm mr-2" onclick="return confirm('Sinkronisasikan nilai rata-rata tugas, kuis, UTS, dan UAS mahasiswa secara otomatis?')">
                                            <i class="fas fa-sync"></i> Sync Nilai Keseluruhan
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <form action="<?= base_url('dosen/nilai/update/'.$selected_kelas['id']) ?>" method="post">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th style="width: 5%">No</th>
                                                <th>NIM</th>
                                                <th>Nama Mahasiswa</th>
                                                <th style="width: 12%">Tugas (30%)</th>
                                                <th style="width: 12%">Kuis (20%)</th>
                                                <th style="width: 12%">UTS (25%)</th>
                                                <th style="width: 12%">UAS (25%)</th>
                                                <th style="width: 12%">Nilai Akhir</th>
                                                <th style="width: 10%">Grade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no=1; foreach($nilai as $n): ?>
                                            <tr>
                                                <td><?= $no++ ?></td>
                                                <td><strong><?= esc($n['nim']) ?></strong></td>
                                                <td><?= esc($n['nama_mahasiswa']) ?></td>
                                                <td>
                                                    <input type="number" step="0.01" name="nilai[<?= $n['mahasiswa_id'] ?>][tugas]" class="form-control form-control-sm" value="<?= $n['nilai_tugas'] ?>" min="0" max="100" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="nilai[<?= $n['mahasiswa_id'] ?>][kuis]" class="form-control form-control-sm" value="<?= $n['nilai_kuis'] ?>" min="0" max="100" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="nilai[<?= $n['mahasiswa_id'] ?>][uts]" class="form-control form-control-sm" value="<?= $n['nilai_uts'] ?>" min="0" max="100" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="nilai[<?= $n['mahasiswa_id'] ?>][uas]" class="form-control form-control-sm" value="<?= $n['nilai_uas'] ?>" min="0" max="100" required>
                                                </td>
                                                <td>
                                                    <span class="font-weight-bold" style="font-size: 16px;"><?= number_format($n['nilai_akhir'], 2) ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?= $n['grade'] == 'A' || $n['grade'] == 'B' ? 'success' : ($n['grade'] == 'C' ? 'warning' : 'danger') ?> font-weight-bold" style="font-size: 14px; padding: 5px 10px;">
                                                        <?= $n['grade'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 text-right">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Seluruh Nilai</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Absensi -->
        <div class="modal fade" id="modalTambahAbsensi" tabindex="-1" role="dialog" aria-labelledby="modalTambahAbsensiLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="<?= base_url('dosen/absensi/store/'.$selected_kelas['id']) ?>" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTambahAbsensiLabel">Input Absensi Pertemuan Baru</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Pertemuan Ke</label>
                                    <input type="number" name="pertemuan_ke" class="form-control" value="<?= count($absensi) + 1 ?>" required min="1">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>

                            <hr>
                            <label>Daftar Kehadiran Mahasiswa</label>
                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th class="text-center" style="width: 40%">Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($mahasiswa)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Belum ada mahasiswa terdaftar di kelas ini.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach($mahasiswa as $m): ?>
                                            <tr>
                                                <td><?= esc($m['nim']) ?></td>
                                                <td><?= esc($m['nama_mahasiswa']) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                        <label class="btn btn-outline-success btn-xs active">
                                                            <input type="radio" name="status[<?= $m['mahasiswa_id'] ?>]" value="hadir" checked> Hadir
                                                        </label>
                                                        <label class="btn btn-outline-warning btn-xs">
                                                            <input type="radio" name="status[<?= $m['mahasiswa_id'] ?>]" value="izin"> Izin
                                                        </label>
                                                        <label class="btn btn-outline-info btn-xs">
                                                            <input type="radio" name="status[<?= $m['mahasiswa_id'] ?>]" value="sakit"> Sakit
                                                        </label>
                                                        <label class="btn btn-outline-danger btn-xs">
                                                            <input type="radio" name="status[<?= $m['mahasiswa_id'] ?>]" value="alpha"> Alpha
                                                        </label>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Simpan Absen</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Absensi -->
        <div class="modal fade" id="modalEditAbsensi" tabindex="-1" role="dialog" aria-labelledby="modalEditAbsensiLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="formEditAbsensi" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditAbsensiLabel">Edit Absensi Pertemuan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Pertemuan Ke</label>
                                    <input type="number" name="pertemuan_ke" id="edit_pertemuan_ke" class="form-control" required min="1">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Tanggal</label>
                                    <input type="date" name="tanggal" id="edit_tanggal" class="form-control" required>
                                </div>
                            </div>

                            <hr>
                            <label>Daftar Kehadiran Mahasiswa</label>
                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>NIM</th>
                                            <th>Nama Mahasiswa</th>
                                            <th class="text-center" style="width: 40%">Kehadiran</th>
                                        </tr>
                                    </thead>
                                    <tbody id="editAbsensiStudentsBody">
                                        <!-- Injected by Ajax -->
                                    </tbody>
                                </table>
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
    <?php else: ?>
        <div class="col-md-12">
            <div class="alert alert-info text-center p-5">
                <i class="fas fa-chalkboard mb-3" style="font-size: 60px;"></i>
                <h4>Silakan Pilih Kelas</h4>
                <p>Silakan pilih kelas pada dropdown di atas untuk memulai pengelolaan absensi dan nilai mahasiswa.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Event listener untuk dropdown pilih kelas dengan vanilla JS
document.addEventListener('DOMContentLoaded', function() {
    var selectKelas = document.getElementById('selectKelas');
    if (selectKelas) {
        selectKelas.addEventListener('change', function() {
            var kelasId = this.value;
            if (kelasId) {
                window.location.href = '<?= base_url('dosen/absensi?kelas_id=') ?>' + kelasId;
            } else {
                window.location.href = '<?= base_url('dosen/absensi') ?>';
            }
        });
    }
});

function editAbsensi(absensiId) {
    $.ajax({
        url: '<?= base_url('dosen/absensi/detail/') ?>' + absensiId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#formEditAbsensi').attr('action', '<?= base_url('dosen/absensi/update/') ?>' + absensiId);
                $('#edit_pertemuan_ke').val(response.absensi.pertemuan_ke);
                $('#edit_tanggal').val(response.absensi.tanggal);
                
                let html = '';
                response.details.forEach(function(student) {
                    html += `
                        <tr>
                            <td>${student.nim}</td>
                            <td>${student.nama_mahasiswa}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn btn-outline-success btn-xs ${student.status === 'hadir' ? 'active' : ''}">
                                        <input type="radio" name="status[${student.mahasiswa_id}]" value="hadir" ${student.status === 'hadir' ? 'checked' : ''}> Hadir
                                    </label>
                                    <label class="btn btn-outline-warning btn-xs ${student.status === 'izin' ? 'active' : ''}">
                                        <input type="radio" name="status[${student.mahasiswa_id}]" value="izin" ${student.status === 'izin' ? 'checked' : ''}> Izin
                                    </label>
                                    <label class="btn btn-outline-info btn-xs ${student.status === 'sakit' ? 'active' : ''}">
                                        <input type="radio" name="status[${student.mahasiswa_id}]" value="sakit" ${student.status === 'sakit' ? 'checked' : ''}> Sakit
                                    </label>
                                    <label class="btn btn-outline-danger btn-xs ${student.status === 'alpha' ? 'active' : ''}">
                                        <input type="radio" name="status[${student.mahasiswa_id}]" value="alpha" ${student.status === 'alpha' ? 'checked' : ''}> Alpha
                                    </label>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                $('#editAbsensiStudentsBody').html(html);
                
                // Trigger jQuery buttons initialization
                $('#modalEditAbsensi').modal('show');
            } else {
                alert('Gagal memuat detail absensi: ' + response.message);
            }
        },
        error: function() {
            alert('Terjadi kesalahan koneksi ke server.');
        }
    });
}
</script>
<?= $this->endSection() ?>
