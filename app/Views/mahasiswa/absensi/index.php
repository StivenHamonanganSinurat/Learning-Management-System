<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<div class="row">
    <div class="col-md-12">
        <?php if (empty($rekapAbsensi)): ?>
            <div class="alert alert-info text-center p-5">
                <i class="fas fa-clipboard-check mb-3" style="font-size:60px;"></i>
                <h4>Belum Ada Data Absensi</h4>
                <p>Anda belum terdaftar di kelas manapun atau dosen belum mencatat absensi.</p>
            </div>
        <?php else: ?>
            <?php foreach ($rekapAbsensi as $rekap): 
                $k   = $rekap['kelas'];
                $pct = $rekap['pctHadir'];
                $progressClass = $pct >= 80 ? 'success' : ($pct >= 60 ? 'warning' : 'danger');
            ?>
            <div class="card card-outline card-<?= $progressClass ?> mb-4">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h4 class="card-title mb-0">
                                <i class="fas fa-chalkboard mr-2"></i>
                                <?= esc($k['nama_mk']) ?>
                            </h4>
                            <small class="text-muted"><?= esc($k['kode_mk']) ?> &bull; TA: <?= esc($k['tahun_ajaran']) ?> (<?= ucfirst($k['semester']) ?>)</small>
                            <br><small class="text-muted"><i class="fas fa-chalkboard-teacher"></i> <?= esc($k['nama_dosen']) ?></small>
                        </div>
                        <div class="col-md-5 text-md-right mt-2 mt-md-0">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="text-success font-weight-bold" style="font-size:1.4em;"><?= $rekap['statMap']['hadir'] ?></div>
                                    <small class="text-muted">Hadir</small>
                                </div>
                                <div class="col-3">
                                    <div class="text-warning font-weight-bold" style="font-size:1.4em;"><?= $rekap['statMap']['izin'] ?></div>
                                    <small class="text-muted">Izin</small>
                                </div>
                                <div class="col-3">
                                    <div class="text-info font-weight-bold" style="font-size:1.4em;"><?= $rekap['statMap']['sakit'] ?></div>
                                    <small class="text-muted">Sakit</small>
                                </div>
                                <div class="col-3">
                                    <div class="text-danger font-weight-bold" style="font-size:1.4em;"><?= $rekap['statMap']['alpha'] ?></div>
                                    <small class="text-muted">Alpha</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Progress Bar Kehadiran -->
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <small><strong>Persentase Kehadiran</strong></small>
                            <small class="text-<?= $progressClass ?>"><strong><?= $pct ?>%</strong> dari <?= $rekap['totalPertemuan'] ?> pertemuan</small>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-<?= $progressClass ?>" style="width: <?= $pct ?>%;" role="progressbar"></div>
                        </div>
                        <?php if ($pct < 75): ?>
                            <small class="text-danger mt-1 d-block"><i class="fas fa-exclamation-triangle"></i> Perhatian: Kehadiran Anda di bawah 75%. Risiko tidak diizinkan ujian!</small>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($rekap['absensiList'])): ?>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="pl-3">Pertemuan</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($rekap['absensiList'] as $abs):
                                    $status = $abs['status'] ?? 'alpha';
                                    $statusMap = [
                                        'hadir' => ['badge-success', 'fas fa-check-circle', 'Hadir'],
                                        'izin'  => ['badge-warning', 'fas fa-file-alt', 'Izin'],
                                        'sakit' => ['badge-info', 'fas fa-heartbeat', 'Sakit'],
                                        'alpha' => ['badge-danger', 'fas fa-times-circle', 'Alpha'],
                                    ];
                                    $s = $statusMap[$status] ?? $statusMap['alpha'];
                                ?>
                                <tr>
                                    <td class="pl-3"><strong>Pertemuan Ke-<?= $abs['pertemuan_ke'] ?></strong></td>
                                    <td><?= date('d M Y', strtotime($abs['tanggal'])) ?></td>
                                    <td class="text-center">
                                        <span class="badge <?= $s[0] ?> px-3 py-1">
                                            <i class="<?= $s[1] ?> mr-1"></i> <?= $s[2] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else: ?>
                <div class="card-body text-center text-muted py-3">
                    Belum ada pertemuan yang dicatat untuk kelas ini.
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
