<?= $this->extend('layouts/adminlte') ?>
<?= $this->section('content') ?>

<!-- Timer Bar -->
<div class="alert alert-danger d-flex align-items-center justify-content-between" id="timerBar">
    <div>
        <i class="fas fa-stopwatch mr-2"></i>
        <strong>Waktu Tersisa: </strong>
        <span id="timerDisplay" class="font-weight-bold" style="font-size: 1.2em;">
            <?= str_pad($kuis['durasi_menit'], 2, '0', STR_PAD_LEFT) ?>:00
        </span>
    </div>
    <div class="text-right">
        <strong><?= esc($kuis['judul']) ?></strong><br>
        <small><?= esc($kuis['nama_mk']) ?></small>
    </div>
</div>

<form action="<?= base_url('mahasiswa/kuis/submit/' . $kuis['id']) ?>" method="post" id="formKuis">
    <input type="hidden" name="attempt_id" value="<?= $attemptId ?>">

    <?php if (empty($soal)): ?>
        <div class="card">
            <div class="card-body text-center p-5">
                <i class="fas fa-exclamation-triangle text-warning" style="font-size:60px;"></i>
                <h4 class="mt-3">Belum Ada Soal</h4>
                <p>Dosen belum menambahkan soal untuk kuis ini.</p>
                <a href="<?= base_url('mahasiswa/kuis') ?>" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($soal as $i => $s): ?>
        <div class="card mb-3">
            <div class="card-header bg-light">
                <strong>Soal <?= $i + 1 ?></strong>
                <?php if ($s['poin']): ?>
                    <span class="badge badge-info float-right"><?= $s['poin'] ?> poin</span>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <p class="mb-4" style="font-size: 1.05em;"><?= esc($s['pertanyaan']) ?></p>
                <div class="row">
                    <?php foreach (['a', 'b', 'c', 'd'] as $opt):
                        $key = 'opsi_' . $opt;
                        if (empty($s[$key])) continue;
                    ?>
                    <div class="col-md-6 mb-2">
                        <label class="btn btn-outline-secondary w-100 text-left p-3 opsi-btn" style="cursor:pointer; border-radius: 8px;">
                            <input type="radio" name="jawaban[<?= $s['id'] ?>]" value="<?= strtoupper($opt) ?>" style="margin-right: 8px;" required>
                            <strong><?= strtoupper($opt) ?>.</strong> <?= esc($s[$key]) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted mb-3">Pastikan semua soal sudah dijawab sebelum submit.</p>
                <button type="submit" class="btn btn-success btn-lg px-5" onclick="return confirm('Yakin ingin mengumpulkan jawaban sekarang?')">
                    <i class="fas fa-paper-plane"></i> Kumpulkan Jawaban
                </button>
            </div>
        </div>
    <?php endif; ?>
</form>

<style>
.opsi-btn:hover { background-color: #f0f8ff; border-color: #007bff; }
.opsi-btn input[type=radio]:checked ~ * { color: #007bff; }
label.opsi-btn:has(input:checked) { background-color: #e3f2fd; border-color: #007bff; color: #007bff; }
</style>

<script>
// Countdown Timer
var totalSec = <?= $kuis['durasi_menit'] ?> * 60;
var timerInterval = setInterval(function() {
    totalSec--;
    if (totalSec <= 0) {
        clearInterval(timerInterval);
        alert('Waktu habis! Jawaban akan dikumpulkan otomatis.');
        document.getElementById('formKuis').submit();
        return;
    }
    var menit = Math.floor(totalSec / 60);
    var detik = totalSec % 60;
    document.getElementById('timerDisplay').textContent =
        String(menit).padStart(2, '0') + ':' + String(detik).padStart(2, '0');

    // Ubah warna jika < 5 menit
    if (totalSec < 300) {
        document.getElementById('timerBar').classList.remove('alert-warning');
        document.getElementById('timerBar').classList.add('alert-danger');
    } else if (totalSec < 600) {
        document.getElementById('timerBar').classList.remove('alert-danger');
        document.getElementById('timerBar').classList.add('alert-warning');
    }
}, 1000);

// Highlight opsi yang dipilih
document.querySelectorAll('.opsi-btn input[type=radio]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        var name = this.name;
        document.querySelectorAll('input[name="' + name + '"]').forEach(function(r) {
            r.closest('label').style.backgroundColor = '';
            r.closest('label').style.borderColor = '';
            r.closest('label').style.color = '';
        });
        this.closest('label').style.backgroundColor = '#e3f2fd';
        this.closest('label').style.borderColor = '#007bff';
        this.closest('label').style.color = '#007bff';
    });
});
</script>
<?= $this->endSection() ?>
