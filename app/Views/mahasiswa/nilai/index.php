<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<div class="row">
    <!-- Kartu IPK -->
    <div class="col-md-12 mb-3">
        <div class="card card-primary card-outline">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1"><i class="fas fa-graduation-cap mr-2 text-primary"></i>Transkrip Nilai Anda</h4>
                        <p class="text-muted mb-0">Rekap nilai akhir semua mata kuliah yang telah Anda selesaikan.</p>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <div class="p-3 rounded text-center" style="background: #f8f9fa;">
                            <small class="text-muted d-block">Indeks Prestasi Kumulatif (IPK)</small>
                            <span class="display-4 font-weight-bold text-<?= $ipk >= 3.5 ? 'success' : ($ipk >= 3.0 ? 'info' : ($ipk >= 2.0 ? 'warning' : 'danger')) ?>">
                                <?= number_format($ipk, 2) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Nilai -->
    <div class="col-md-12">
        <div class="card card-outline card-secondary">
            <div class="card-body">
                <?php if (empty($nilai)): ?>
                    <div class="alert alert-info text-center p-5">
                        <i class="fas fa-chart-bar mb-3" style="font-size:60px;"></i>
                        <h4>Belum Ada Nilai</h4>
                        <p>Dosen belum menginput nilai akhir untuk mata kuliah Anda.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kode MK</th>
                                    <th>Nama Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                    <th>TA</th>
                                    <th class="text-center">Tugas</th>
                                    <th class="text-center">Kuis</th>
                                    <th class="text-center">UTS</th>
                                    <th class="text-center">UAS</th>
                                    <th class="text-center">Nilai Akhir</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Sertifikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($nilai as $n): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= esc($n['kode_mk']) ?></code></td>
                                    <td><strong><?= esc($n['nama_mk']) ?></strong></td>
                                    <td class="text-center"><?= $n['sks'] ?></td>
                                    <td><?= ucfirst($n['semester']) ?></td>
                                    <td><?= esc($n['tahun_ajaran']) ?></td>
                                    <td class="text-center"><?= number_format($n['nilai_tugas'], 1) ?></td>
                                    <td class="text-center"><?= number_format($n['nilai_kuis'], 1) ?></td>
                                    <td class="text-center"><?= number_format($n['nilai_uts'], 1) ?></td>
                                    <td class="text-center"><?= number_format($n['nilai_uas'], 1) ?></td>
                                    <td class="text-center"><strong style="font-size: 1.1em;"><?= number_format($n['nilai_akhir'], 2) ?></strong></td>
                                    <td class="text-center">
                                        <span class="badge badge-<?= in_array($n['grade'], ['A', 'B']) ? 'success' : ($n['grade'] == 'C' ? 'warning' : 'danger') ?> font-weight-bold" style="font-size: 1em; padding: 6px 12px;">
                                            <?= $n['grade'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <?php if (in_array($n['grade'], ['A', 'B', 'C'])): ?>
                                            <a href="<?= base_url('mahasiswa/nilai/sertifikat/' . $n['id']) ?>" target="_blank" class="btn btn-success btn-xs">
                                                <i class="fas fa-certificate"></i> Cetak
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="10" class="text-right"><strong>IPK Keseluruhan:</strong></td>
                                    <td class="text-center" colspan="3">
                                        <strong class="text-<?= $ipk >= 3.5 ? 'success' : ($ipk >= 3.0 ? 'info' : ($ipk >= 2.0 ? 'warning' : 'danger')) ?>" style="font-size: 1.2em;">
                                            <?= number_format($ipk, 2) ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Keterangan Grade -->
                    <div class="mt-3 p-3 rounded" style="background: #f8f9fa;">
                        <small class="text-muted">
                            <strong>Keterangan Grade:</strong>
                            <span class="badge badge-success ml-2">A = 4.0 (≥ 85)</span>
                            <span class="badge badge-success ml-1">B = 3.0 (≥ 70)</span>
                            <span class="badge badge-warning ml-1">C = 2.0 (≥ 55)</span>
                            <span class="badge badge-danger ml-1">D = 1.0 (≥ 40)</span>
                            <span class="badge badge-danger ml-1">E = 0 (&lt; 40)</span>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
