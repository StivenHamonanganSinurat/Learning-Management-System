<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Peringkat Saya Card -->
    <div class="col-md-4 mb-4">
        <div class="card card-warning card-outline shadow-sm text-center py-4">
            <div class="card-body">
                <i class="fas fa-trophy text-warning mb-3" style="font-size: 70px;"></i>
                <h4 class="font-weight-bold">Posisi Anda</h4>
                <p class="text-muted">Peringkat Anda dibanding seluruh mahasiswa Stikes</p>
                
                <div class="display-3 font-weight-bold text-warning my-3">
                    #<?= $myRank ?>
                </div>

                <div class="h5 text-muted">
                    Total Poin: <strong class="text-dark"><?= number_format($myPoin) ?> XP</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaderboard Table Card -->
    <div class="col-md-8 mb-4">
        <div class="card card-outline card-primary shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold"><i class="fas fa-list-ol mr-2 text-primary"></i>10 Besar Mahasiswa Teraktif</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 10%" class="text-center">Peringkat</th>
                                <th>Nama Mahasiswa</th>
                                <th>NIM</th>
                                <th class="text-center">Level</th>
                                <th class="text-right" style="padding-right: 20px;">Total Poin</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            foreach ($leaderboard as $row): 
                                $isMe = (session()->get('id') == $row['id']);
                                $medalIcon = '';
                                if ($rank == 1) $medalIcon = '<i class="fas fa-crown text-warning mr-1"></i> ';
                                elseif ($rank == 2) $medalIcon = '<i class="fas fa-medal text-secondary mr-1"></i> ';
                                elseif ($rank == 3) $medalIcon = '<i class="fas fa-medal text-orange mr-1" style="color: #cd7f32;"></i> ';
                            ?>
                            <tr class="<?= $isMe ? 'bg-warning-light font-weight-bold' : '' ?>" style="<?= $isMe ? 'background-color: #fff9e6;' : '' ?>">
                                <td class="text-center">
                                    <?php if ($rank <= 3): ?>
                                        <?= $medalIcon ?><span style="font-size: 1.1em; font-weight: bold;"><?= $rank ?></span>
                                    <?php else: ?>
                                        <?= $rank ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= esc($row['name']) ?>
                                    <?php if ($isMe): ?>
                                        <span class="badge badge-warning text-white ml-1">Saya</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($row['nim_nidn']) ?></code></td>
                                <td class="text-center">
                                    <span class="badge badge-info px-2 py-1">Lvl <?= $row['level'] ?></span>
                                </td>
                                <td class="text-right font-weight-bold text-primary" style="padding-right: 20px;">
                                    <?= number_format($row['poin']) ?> XP
                                </td>
                            </tr>
                            <?php 
                            $rank++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
