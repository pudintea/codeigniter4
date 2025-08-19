<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/dashboard', 'Dashboard::index', ['filter'=>'pdnislogin']);
$routes->match(['GET','POST'],'/login', 'Bo::login');
$routes->get('/logout', 'Bo::logout');

$routes->group('users', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'UsersController::index');
    $routes->get('tambah', 'UsersController::tambah');
});

//====================================================================

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Bo::login');
// LOGIN dan LOGOUT
$routes->match(['GET','POST'],'/login', 'Bo::login');
$routes->get('/logout', 'Bo::logout');

// Masuk dan Keluar
$routes->match(['GET','POST'],'/masuk', 'MasukController::login');
$routes->get('/keluar', 'MasukController::logout');

// DASHBOARD
$routes->get('/dashboard', 'Dashboard::index', ['filter'=>'pdnislogin']);
// USERS
$routes->group('users', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'UsersController::index');
    $routes->match(['GET','POST'], 'tambah', 'UsersController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'UsersController::edit/$1');
    $routes->delete('hapus/(:num)', 'UsersController::hapus/$1');
    // $routes->match(['GET','POST'], 'data_json', 'UsersController::data_json');
    $routes->post('data_json', 'UsersController::data_json');
});

// UNIT
$routes->group('unit', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'UnitController::index');
    $routes->match(['GET','POST'], 'data_json', 'UnitController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'UnitController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'UnitController::edit/$1');
    $routes->delete('hapus/(:num)', 'UnitController::hapus/$1');
});

// AKSES UNIT
$routes->group('aksesunit', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'AksesUnitController::index');
    $routes->match(['GET','POST'], 'data_json', 'AksesUnitController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'AksesUnitController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'AksesUnitController::edit/$1');
    $routes->delete('hapus/(:num)', 'AksesUnitController::hapus/$1');
});

// Pegawai
$routes->group('pegawai', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'PegawaiController::index');
    $routes->match(['GET','POST'], 'data_json', 'PegawaiController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'PegawaiController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'PegawaiController::edit/$1');
    $routes->delete('hapus/(:num)', 'PegawaiController::hapus/$1');
});

// Jabatan
$routes->group('jabatan', ['filter'=>'pdnislogin'], function($routes) {
    $routes->get('/', 'JabatanController::index');
    $routes->match(['GET','POST'], 'data_json', 'JabatanController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'JabatanController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'JabatanController::edit/$1');
    $routes->delete('hapus/(:num)', 'JabatanController::hapus/$1');
});

//// PEGAWAI
// Dashboard Pegawai
$routes->group('dash_peg', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'DashPegController::index');
    $routes->match(['GET','POST'], 'data_json', 'DashPegController::data_json');
});

// Lembur
$routes->group('lembur', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'LemburController::index');
    $routes->match(['GET','POST'], 'data_json', 'LemburController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'LemburController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'LemburController::edit/$1');
    $routes->delete('hapus/(:num)', 'LemburController::hapus/$1');
});

// Hari Kerja
$routes->group('hari_kerja', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'HariKerjaController::index');
    $routes->match(['GET','POST'], 'data_json', 'HariKerjaController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'HariKerjaController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'HariKerjaController::edit/$1');
    $routes->delete('hapus/(:num)', 'HariKerjaController::hapus/$1');
});

// Hari Libur
$routes->group('hari_libur', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'HariLiburController::index');
    $routes->match(['GET','POST'], 'data_json', 'HariLiburController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'HariLiburController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'HariLiburController::edit/$1');
    $routes->delete('hapus/(:num)', 'HariLiburController::hapus/$1');
});

// Sakit
$routes->group('sakit', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'SakitController::index');
    $routes->match(['GET','POST'], 'data_json', 'SakitController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'SakitController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'SakitController::edit/$1');
    $routes->delete('hapus/(:num)', 'SakitController::hapus/$1');
});

// Izin
$routes->group('izin', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'IzinController::index');
    $routes->match(['GET','POST'], 'data_json', 'IzinController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'IzinController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'IzinController::edit/$1');
    $routes->delete('hapus/(:num)', 'IzinController::hapus/$1');
});

// Cuti
$routes->group('cuti', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'CutiController::index');
    $routes->match(['GET','POST'], 'data_json', 'CutiController::data_json');
    $routes->match(['GET','POST'], 'tambah', 'CutiController::tambah');
    $routes->match(['GET','POST'], 'edit/(:num)', 'CutiController::edit/$1');
    $routes->delete('hapus/(:num)', 'CutiController::hapus/$1');
});

// Cetak Lembur
$routes->group('cetak_lembur', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'CetakLemburController::index');
    $routes->post('simpan_priode', 'CetakLemburController::simpan_priode');
});

// Cetak Absensi
$routes->group('cetak_absensi', ['filter'=>'pdnpegislogin'], function($routes) {
    $routes->get('/', 'CetakAbsensiController::index');
    $routes->post('simpan_priode', 'CetakAbsensiController::simpan_priode');
});
