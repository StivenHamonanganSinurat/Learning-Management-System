<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<!-- Gamifikasi Profile Section -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card bg-gradient-primary text-white shadow" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); border-radius: 12px;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-7 mb-3 mb-md-0">
                        <h4 class="mb-1"><i class="fas fa-medal mr-2"></i>Halo, <strong><?= esc($userProfile['name']) ?></strong>!</h4>
                        <p class="mb-0 text-white-50">Semangat belajar hari ini! Kumpulkan poin dengan mengerjakan tugas dan kuis.</p>
                        
                        <!-- Progress Level -->
                        <?php 
                            $poinSekarang = $userProfile['poin'] % 100;
                            $nextLevelPoin = 100;
                            $pctLevel = ($poinSekarang / $nextLevelPoin) * 100;
                        ?>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="font-weight-bold">Level <?= $userProfile['level'] ?></span>
                                <span><?= $poinSekarang ?> / <?= $nextLevelPoin ?> XP (Poin)</span>
                            </div>
                            <div class="progress" style="height: 12px; border-radius: 6px; background: rgba(255,255,255,0.25);">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctLevel ?>%; border-radius: 6px;"></div>
                            </div>
                            <small class="text-white-50 mt-1 d-block"><i class="fas fa-info-circle mr-1"></i>Dapatkan <?= 100 - $poinSekarang ?> Poin lagi untuk naik ke Level <?= $userProfile['level'] + 1 ?></small>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-right border-left border-white-50 pl-md-4">
                        <h5 class="mb-2">Lencana (Badges) Anda:</h5>
                        <div class="d-flex flex-wrap justify-content-md-end" style="gap: 10px;">
                            <?php if (empty($userBadges)): ?>
                                <span class="text-white-50 small">Belum ada lencana yang diperoleh.</span>
                            <?php else: ?>
                                <?php foreach ($userBadges as $b): ?>
                                    <span class="badge badge-light p-2 shadow-sm rounded-lg" data-toggle="tooltip" title="<?= esc($b['deskripsi']) ?>">
                                        <i class="fas <?= esc($b['icon']) ?> mr-1"></i> <?= esc($b['nama']) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<?php if (!empty($reminders)): ?>
<div class="row mb-3">
    <div class="col-md-12">
        <div class="card card-warning card-outline shadow-sm">
            <div class="card-header">
                <h5 class="card-title font-weight-bold text-warning mb-0"><i class="fas fa-bell mr-2"></i> Peringatan Jadwal Kuliah Terdekat</h5>
            </div>
            <div class="card-body py-2 px-3">
                <div class="row">
                    <?php foreach ($reminders as $rem): 
                        $isToday = ($rem['tanggal'] == date('Y-m-d'));
                        $dayText = $isToday ? 'Hari Ini' : 'Besok';
                        $badgeColor = $isToday ? 'danger' : 'warning';
                    ?>
                    <div class="col-md-6 mb-2">
                        <div class="p-2 border rounded bg-light d-flex align-items-center">
                            <div class="mr-3 pl-2">
                                <span class="badge badge-<?= $badgeColor ?> p-2 font-weight-bold text-uppercase"><?= $dayText ?></span>
                            </div>
                            <div>
                                <h6 class="font-weight-bold mb-0"><?= esc($rem['kode_mk']) ?> - <?= esc($rem['nama_mk']) ?></h6>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-layer-group mr-1"></i> Pertemuan <?= $rem['pertemuan_ke'] ?> &bull; 
                                    <i class="far fa-clock mr-1"></i> <?= substr($rem['jam_mulai'], 0, 5) ?> - <?= substr($rem['jam_selesai'], 0, 5) ?>
                                </small>
                                <small class="text-danger font-weight-bold d-block mt-1">
                                    <i class="fas fa-map-marker-alt mr-1"></i> Ruangan: <?= esc($rem['ruangan']) ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row">
    <!-- Stat Boxes -->
    <div class="col-lg-4 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $jumlahKelas ?></h3>
                <p>Kelas Aktif</p>
            </div>
            <div class="icon"><i class="fas fa-chalkboard"></i></div>
            <a href="<?= base_url('mahasiswa/materi') ?>" class="small-box-footer">Lihat Materi <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $tugasBelumKumpul ?></h3>
                <p>Tugas Belum Dikumpulkan</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
            <a href="<?= base_url('mahasiswa/tugas') ?>" class="small-box-footer">Lihat Tugas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-lg-4 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $kuisTersedia ?></h3>
                <p>Kuis Tersedia</p>
            </div>
            <div class="icon"><i class="fas fa-question-circle"></i></div>
            <a href="<?= base_url('mahasiswa/kuis') ?>" class="small-box-footer">Ikut Kuis <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kelas Aktif -->
    <div class="col-md-7">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-chalkboard mr-2"></i>Kelas yang Anda Ikuti</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($kelasList)): ?>
                    <div class="p-4 text-center text-muted">Anda belum terdaftar di kelas manapun.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($kelasList as $k): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= esc($k['nama_mk']) ?></strong>
                                <br><small class="text-muted"><?= esc($k['kode_mk']) ?> &bull; <?= ucfirst($k['semester']) ?> <?= esc($k['tahun_ajaran']) ?></small>
                                <br><small class="text-muted"><i class="fas fa-chalkboard-teacher"></i> <?= esc($k['nama_dosen']) ?></small>
                            </div>
                            <a href="<?= base_url('mahasiswa/materi') ?>" class="btn btn-outline-info btn-xs">Lihat Materi</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tugas Mendekati Deadline -->
    <div class="col-md-5">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-clock mr-2"></i>Tugas Mendekati Deadline</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($tugasDeadline)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-check-circle text-success" style="font-size:40px;"></i>
                        <p class="mt-2">Semua tugas sudah dikumpulkan!</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($tugasDeadline as $t): 
                            $sisa = strtotime($t['deadline']) - time();
                            $sisaJam = round($sisa / 3600);
                            $badgeClass = $sisaJam < 24 ? 'danger' : ($sisaJam < 72 ? 'warning' : 'info');
                        ?>
                        <li class="list-group-item">
                            <strong><?= esc($t['judul']) ?></strong>
                            <br><small class="text-muted"><?= esc($t['nama_mk']) ?></small>
                            <br>
                            <span class="badge badge-<?= $badgeClass ?>">
                                <i class="far fa-clock"></i>
                                <?= date('d M Y H:i', strtotime($t['deadline'])) ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('mahasiswa/tugas') ?>" class="btn btn-warning btn-sm text-white">Lihat Semua Tugas</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pengumuman Terbaru -->
        <div class="card card-outline card-danger">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bullhorn mr-2"></i>Pengumuman Terbaru</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($latestPengumuman)): ?>
                    <div class="p-4 text-center text-muted">Belum ada pengumuman terbaru.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($latestPengumuman as $p): ?>
                        <li class="list-group-item">
                            <strong><?= esc($p['judul']) ?></strong>
                            <br><small class="text-muted"><?= esc($p['nama_mk']) ?> &bull; <?= date('d M Y H:i', strtotime($p['created_at'])) ?></small>
                            <p class="mb-0 text-muted small mt-1"><?= esc(substr(strip_tags($p['isi']), 0, 75)) ?>...</p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="card-footer text-center">
                        <a href="<?= base_url('mahasiswa/pengumuman') ?>" class="btn btn-outline-danger btn-sm">Lihat Semua Pengumuman</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
