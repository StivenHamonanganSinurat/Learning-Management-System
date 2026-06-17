<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <h3 class="card-title">Jadwal Kuliah Anda</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($jadwal)): ?>
                    <div class="alert alert-info text-center m-4 py-5">
                        <i class="far fa-calendar-times mb-3" style="font-size:60px;"></i>
                        <h4>Belum Ada Jadwal Kuliah</h4>
                        <p class="mb-0">Tidak ada jadwal perkuliahan terdaftar di kelas-kelas yang Anda ikuti.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 50px">No</th>
                                    <th>Kode MK</th>
                                    <th>Mata Kuliah</th>
                                    <th>Dosen Pengampu</th>
                                    <th>Pertemuan</th>
                                    <th>Hari / Tanggal</th>
                                    <th>Jam Kuliah</th>
                                    <th>Ruangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($jadwal as $row) : 
                                    $isToday = ($row['tanggal'] == date('Y-m-d'));
                                    $rowClass = $isToday ? 'table-warning font-weight-bold' : '';
                                    
                                    // Get Day Name in Indonesian
                                    $dayNum = date('N', strtotime($row['tanggal']));
                                    $days = [
                                        1 => 'Senin',
                                        2 => 'Selasa',
                                        3 => 'Rabu',
                                        4 => 'Kamis',
                                        5 => 'Jumat',
                                        6 => 'Sabtu',
                                        7 => 'Minggu'
                                    ];
                                    $dayName = $days[$dayNum];
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><?= $no++ ?></td>
                                    <td><code><?= esc($row['kode_mk']) ?></code></td>
                                    <td><strong><?= esc($row['nama_mk']) ?></strong></td>
                                    <td><?= esc($row['nama_dosen']) ?></td>
                                    <td><span class="badge badge-info">Pertemuan <?= $row['pertemuan_ke'] ?></span></td>
                                    <td>
                                        <?= $dayName ?>, <?= date('d M Y', strtotime($row['tanggal'])) ?>
                                        <?php if ($isToday): ?>
                                            <span class="badge badge-danger ml-1">Hari Ini</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><code><?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?></code></td>
                                    <td>
                                        <span class="text-danger font-weight-bold">
                                            <i class="fas fa-map-marker-alt mr-1"></i> <?= esc($row['ruangan']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
