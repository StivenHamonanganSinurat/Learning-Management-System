<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'LMS Stikes Nauli Husada' ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Theme style (AdminLTE 3) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Select2 custom styling agar sesuai AdminLTE */
        .select2-container--default .select2-selection--single {
            height: calc(1.5em + .75rem + 2px);
            padding: .375rem .75rem;
            border: 1px solid #ced4da;
            border-radius: .25rem;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
            color: #495057;
            padding-left: 0;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(1.5em + .75rem + 2px);
        }
        .select2-container--default .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: .25rem;
            padding: .375rem .75rem;
        }
        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: .25rem;
        }
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #007bff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link text-danger" href="<?= base_url('logout') ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light text-center d-block"><b>LMS</b> Stikes</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="info">
                    <a href="#" class="d-block"><?= session()->get('name') ?> <br> <small class="text-muted">(<?= ucfirst(session()->get('role')) ?>)</small></a>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    <?php $role = session()->get('role'); $uri = service('uri')->getPath(); ?>
                    
                    <?php if($role == 'admin'): ?>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= $uri == 'admin/dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/users') ?>" class="nav-link <?= strpos($uri, 'admin/users') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Manajemen User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/prodi') ?>" class="nav-link <?= strpos($uri, 'admin/prodi') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-university"></i>
                                <p>Program Studi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/matakuliah') ?>" class="nav-link <?= strpos($uri, 'admin/matakuliah') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Mata Kuliah</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('admin/kelas') ?>" class="nav-link <?= strpos($uri, 'admin/kelas') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chalkboard"></i>
                                <p>Manajemen Kelas</p>
                            </a>
                        </li>
                    
                    <?php elseif($role == 'dosen'): ?>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/dashboard') ?>" class="nav-link <?= $uri == 'dosen/dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/kelas') ?>" class="nav-link <?= strpos($uri, 'dosen/kelas') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chalkboard"></i>
                                <p>Kelas Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/materi') ?>" class="nav-link <?= strpos($uri, 'dosen/materi') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Materi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/tugas') ?>" class="nav-link <?= strpos($uri, 'dosen/tugas') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Tugas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/kuis?tipe=kuis') ?>" class="nav-link <?= (strpos($uri, 'dosen/kuis') !== false && service('request')->getGet('tipe') != 'uts' && service('request')->getGet('tipe') != 'uas') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-question-circle"></i>
                                <p>Kuis</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/kuis?tipe=uts') ?>" class="nav-link <?= (strpos($uri, 'dosen/kuis') !== false && service('request')->getGet('tipe') == 'uts') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>Ujian UTS</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/kuis?tipe=uas') ?>" class="nav-link <?= (strpos($uri, 'dosen/kuis') !== false && service('request')->getGet('tipe') == 'uas') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Ujian UAS</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/absensi') ?>" class="nav-link <?= strpos($uri, 'dosen/absensi') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Absensi & Rekap Nilai</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/pengumuman') ?>" class="nav-link <?= strpos($uri, 'dosen/pengumuman') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Pengumuman</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('dosen/forum') ?>" class="nav-link <?= strpos($uri, 'dosen/forum') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Forum Diskusi</p>
                            </a>
                        </li>
                    
                    <?php elseif($role == 'mahasiswa'): ?>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/dashboard') ?>" class="nav-link <?= $uri == 'mahasiswa/dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/materi') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/materi') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>Materi Kuliah</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/tugas') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/tugas') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tasks"></i>
                                <p>Tugas Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/kuis?tipe=kuis') ?>" class="nav-link <?= (strpos($uri, 'mahasiswa/kuis') !== false && service('request')->getGet('tipe') != 'uts' && service('request')->getGet('tipe') != 'uas') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-question-circle"></i>
                                <p>Kuis Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/kuis?tipe=uts') ?>" class="nav-link <?= (strpos($uri, 'mahasiswa/kuis') !== false && service('request')->getGet('tipe') == 'uts') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-signature"></i>
                                <p>UTS Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/kuis?tipe=uas') ?>" class="nav-link <?= (strpos($uri, 'mahasiswa/kuis') !== false && service('request')->getGet('tipe') == 'uas') ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>UAS Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/nilai') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/nilai') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Nilai & Transkrip</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/absensi') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/absensi') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Rekap Absensi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/pengumuman') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/pengumuman') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Pengumuman</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/forum') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/forum') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-comments"></i>
                                <p>Forum Diskusi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/leaderboard') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/leaderboard') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-trophy"></i>
                                <p>Leaderboard Poin</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('mahasiswa/jadwal') ?>" class="nav-link <?= strpos($uri, 'mahasiswa/jadwal') !== false ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-calendar-alt"></i>
                                <p>Jadwal Kuliah</p>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= $title ?? 'Dashboard' ?></h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; <?= date('Y') ?> LMS Stikes Nauli Husada.</strong>
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function() {
    // ============================================================
    // 1. Inisialisasi Select2 untuk elemen DI LUAR modal (halaman utama)
    //    Contoh: dropdown pilih kelas di halaman absensi
    // ============================================================
    $('select.select2').not('.modal select.select2').select2({
        width: '100%',
        placeholder: '-- Cari atau pilih kelas --',
        allowClear: true,
        language: {
            noResults: function() { return 'Tidak ada hasil ditemukan'; },
            searching: function() { return 'Mencari...'; }
        }
    });

    // ============================================================
    // 2. Inisialisasi Select2 untuk elemen DI DALAM modal
    //    Diinisialisasi saat modal terbuka (shown.bs.modal) dengan
    //    dropdownParent ke modal agar focus tidak konflik
    // ============================================================
    $(document).on('shown.bs.modal', '.modal', function() {
        var $modal = $(this);
        $modal.find('select.select2').each(function() {
            // Destroy dulu jika sudah diinisialisasi sebelumnya
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            $(this).select2({
                width: '100%',
                placeholder: '-- Cari atau pilih kelas --',
                allowClear: true,
                language: {
                    noResults: function() { return 'Tidak ada hasil ditemukan'; },
                    searching: function() { return 'Mencari...'; }
                },
                dropdownParent: $modal  // Kunci utama: render dropdown di dalam modal
            });
        });
    });

    // ============================================================
    // 3. Bersihkan (destroy) Select2 saat modal ditutup
    //    Agar tidak ada memory leak / konflik saat modal dibuka ulang
    // ============================================================
    $(document).on('hidden.bs.modal', '.modal', function() {
        $(this).find('select.select2').each(function() {
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });
    });
});
</script>
</body>
</html>
