<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'AuthController::login'); // Default ke halaman login
$routes->get('login', 'AuthController::login');
$routes->post('login/process', 'AuthController::processLogin');
$routes->get('logout', 'AuthController::logout');

// Group Admin (sementara placeholder route)
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    
    // Prodi Routes
    $routes->get('prodi', 'Admin\ProdiController::index');
    $routes->get('prodi/create', 'Admin\ProdiController::create');
    $routes->post('prodi/store', 'Admin\ProdiController::store');
    $routes->get('prodi/edit/(:num)', 'Admin\ProdiController::edit/$1');
    $routes->post('prodi/update/(:num)', 'Admin\ProdiController::update/$1');
    $routes->post('prodi/delete/(:num)', 'Admin\ProdiController::delete/$1');

    // Users Routes
    $routes->get('users', 'Admin\UserController::index');
    $routes->get('users/create', 'Admin\UserController::create');
    $routes->post('users/store', 'Admin\UserController::store');
    $routes->get('users/edit/(:num)', 'Admin\UserController::edit/$1');
    $routes->post('users/update/(:num)', 'Admin\UserController::update/$1');
    $routes->post('users/delete/(:num)', 'Admin\UserController::delete/$1');

    // Mata Kuliah Routes
    $routes->get('matakuliah', 'Admin\MataKuliahController::index');
    $routes->get('matakuliah/create', 'Admin\MataKuliahController::create');
    $routes->post('matakuliah/store', 'Admin\MataKuliahController::store');
    $routes->get('matakuliah/edit/(:num)', 'Admin\MataKuliahController::edit/$1');
    $routes->post('matakuliah/update/(:num)', 'Admin\MataKuliahController::update/$1');
    $routes->post('matakuliah/delete/(:num)', 'Admin\MataKuliahController::delete/$1');

    // Kelas Routes
    $routes->get('kelas', 'Admin\KelasController::index');
    $routes->get('kelas/create', 'Admin\KelasController::create');
    $routes->post('kelas/store', 'Admin\KelasController::store');
    $routes->get('kelas/edit/(:num)', 'Admin\KelasController::edit/$1');
    $routes->post('kelas/update/(:num)', 'Admin\KelasController::update/$1');
    $routes->post('kelas/delete/(:num)', 'Admin\KelasController::delete/$1');
    
    // Peserta Kelas Routes
    $routes->get('kelas/peserta/(:num)', 'Admin\KelasController::peserta/$1');
    $routes->post('kelas/peserta/add/(:num)', 'Admin\KelasController::addPeserta/$1');
    $routes->post('kelas/peserta/remove/(:num)', 'Admin\KelasController::removePeserta/$1');

    // Jadwal Kelas Routes
    $routes->get('kelas/jadwal/(:num)', 'Admin\KelasController::jadwal/$1');
    $routes->post('kelas/jadwal/store/(:num)', 'Admin\KelasController::storeJadwal/$1');
    $routes->post('kelas/jadwal/update/(:num)', 'Admin\KelasController::updateJadwal/$1');
    $routes->post('kelas/jadwal/delete/(:num)', 'Admin\KelasController::deleteJadwal/$1');
});

// Group Dosen
$routes->group('dosen', ['filter' => 'auth:dosen'], function ($routes) {
    $routes->get('dashboard', 'Dosen\DashboardController::index');
    
    // Kelas Dosen Routes
    $routes->get('kelas', 'Dosen\KelasController::index');
    $routes->get('kelas/kelola/(:num)', 'Dosen\KelasController::kelola/$1');
    $routes->post('kelas/jadwal/store/(:num)', 'Dosen\KelasController::storeJadwal/$1');
    $routes->post('kelas/jadwal/update/(:num)', 'Dosen\KelasController::updateJadwal/$1');
    $routes->post('kelas/jadwal/delete/(:num)', 'Dosen\KelasController::deleteJadwal/$1');

    // Materi Routes
    $routes->get('materi', 'Dosen\MateriController::index');
    $routes->post('materi/store', 'Dosen\MateriController::store');
    $routes->post('materi/store/(:num)', 'Dosen\MateriController::store/$1');
    $routes->post('materi/update/(:num)', 'Dosen\MateriController::update/$1');
    $routes->post('materi/delete/(:num)', 'Dosen\MateriController::delete/$1');

    // Tugas Routes
    $routes->get('tugas', 'Dosen\TugasController::index');
    $routes->post('tugas/store', 'Dosen\TugasController::store');
    $routes->post('tugas/store/(:num)', 'Dosen\TugasController::store/$1');
    $routes->post('tugas/delete/(:num)', 'Dosen\TugasController::delete/$1');
    $routes->get('tugas/detail/(:num)', 'Dosen\TugasController::detail/$1');
    $routes->post('tugas/nilai/(:num)', 'Dosen\TugasController::nilai/$1');

    // Kuis Routes
    $routes->get('kuis', 'Dosen\KuisController::index');
    $routes->post('kuis/store', 'Dosen\KuisController::store');
    $routes->post('kuis/store/(:num)', 'Dosen\KuisController::store/$1');
    $routes->post('kuis/delete/(:num)', 'Dosen\KuisController::delete/$1');
    $routes->get('kuis/detail/(:num)', 'Dosen\KuisController::detail/$1');
    $routes->post('kuis/update/(:num)', 'Dosen\KuisController::update/$1');
    $routes->post('kuis/updateNilaiAttempt/(:num)', 'Dosen\KuisController::updateNilaiAttempt/$1');
    $routes->post('kuis/storeSoal/(:num)', 'Dosen\KuisController::storeSoal/$1');
    $routes->post('kuis/deleteSoal/(:num)', 'Dosen\KuisController::deleteSoal/$1');

    // Absensi & Nilai Routes
    $routes->get('absensi', 'Dosen\AbsensiNilaiController::index');
    $routes->post('absensi/store/(:num)', 'Dosen\AbsensiNilaiController::storeAbsensi/$1');
    $routes->get('absensi/detail/(:num)', 'Dosen\AbsensiNilaiController::detailAbsensi/$1');
    $routes->post('absensi/update/(:num)', 'Dosen\AbsensiNilaiController::updateAbsensi/$1');
    $routes->post('absensi/delete/(:num)', 'Dosen\AbsensiNilaiController::deleteAbsensi/$1');
    $routes->post('nilai/update/(:num)', 'Dosen\AbsensiNilaiController::updateNilai/$1');
    $routes->post('nilai/sync/(:num)', 'Dosen\AbsensiNilaiController::syncNilai/$1');

    // Pengumuman Routes
    $routes->get('pengumuman', 'Dosen\PengumumanController::index');
    $routes->post('pengumuman/store', 'Dosen\PengumumanController::store');
    $routes->post('pengumuman/update/(:num)', 'Dosen\PengumumanController::update/$1');
    $routes->post('pengumuman/delete/(:num)', 'Dosen\PengumumanController::delete/$1');

    // Forum Routes
    $routes->get('forum', 'Dosen\ForumController::index');
    $routes->post('forum/create/(:num)', 'Dosen\ForumController::createTopik/$1');
    $routes->get('forum/detail/(:num)', 'Dosen\ForumController::detailTopik/$1');
    $routes->post('forum/reply/(:num)', 'Dosen\ForumController::reply/$1');
    $routes->post('forum/delete/(:num)', 'Dosen\ForumController::deleteTopik/$1');
});

// Group Mahasiswa
$routes->group('mahasiswa', ['filter' => 'auth:mahasiswa'], function ($routes) {
    $routes->get('dashboard', 'Mahasiswa\DashboardController::index');

    // Materi Routes
    $routes->get('materi', 'Mahasiswa\MateriController::index');

    // Tugas Routes
    $routes->get('tugas', 'Mahasiswa\TugasController::index');
    $routes->post('tugas/submit/(:num)', 'Mahasiswa\TugasController::submit/$1');

    // Kuis Routes
    $routes->get('kuis', 'Mahasiswa\KuisController::index');
    $routes->get('kuis/mulai/(:num)', 'Mahasiswa\KuisController::mulai/$1');
    $routes->post('kuis/submit/(:num)', 'Mahasiswa\KuisController::submit/$1');
    $routes->get('kuis/hasil/(:num)', 'Mahasiswa\KuisController::hasil/$1');

    // Nilai Routes
    $routes->get('nilai', 'Mahasiswa\NilaiController::index');
    $routes->get('nilai/sertifikat/(:num)', 'Mahasiswa\CertificateController::generate/$1');

    // Absensi Routes
    $routes->get('absensi', 'Mahasiswa\AbsensiController::index');

    // Pengumuman Routes
    $routes->get('pengumuman', 'Mahasiswa\PengumumanController::index');

    // Forum Routes
    $routes->get('forum', 'Mahasiswa\ForumController::index');
    $routes->post('forum/create/(:num)', 'Mahasiswa\ForumController::createTopik/$1');
    $routes->get('forum/detail/(:num)', 'Mahasiswa\ForumController::detailTopik/$1');
    $routes->post('forum/reply/(:num)', 'Mahasiswa\ForumController::reply/$1');

    // Leaderboard Route
    $routes->get('leaderboard', 'Mahasiswa\LeaderboardController::index');

    // Jadwal Perkuliahan Route
    $routes->get('jadwal', 'Mahasiswa\JadwalController::index');
});
