<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Routes untuk API AJAX
$route['api/status/(:num)'] = 'pelamar/get_status/$1';
$route['api/trainer'] = 'art/get_trainer';
$route['api/departemen'] = 'pelamar/get_departemen';

// Routes untuk laporan
$route['laporan/pelamar/cetak'] = 'laporan/cetak_pelamar';
$route['laporan/art/cetak'] = 'laporan/cetak_art';
$route['laporan/ho/cetak'] = 'laporan/cetak_ho';